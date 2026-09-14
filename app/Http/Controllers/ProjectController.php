<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Group;
use App\Models\Api;
use App\Models\Docs;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
{
    public function __construct(protected Project $model)
    {

    }

    public function index(){
        $projects = $this->model->where('user_id', Auth::id())
            ->orWhereHas('viewers', fn($query) => $query->where('users.id', Auth::id()))
            ->withCount(['groups', 'viewers'])->with('viewers:id,name,email')->get()
            ->each(function($project) {
                $canEdit = $project->user_id === Auth::id();
                $project->setAttribute('can_edit', $canEdit);
                if (!$canEdit) $project->unsetRelation('viewers');
            });
        return Inertia::render('Project',['projects' => $projects]);
    }

    public function store(Request $request){

        $this->model->create([
            'name' => $request->name,
            'base_url' => $request->base_url,
            'user_id' => Auth::user()->id
        ]);
        session()->flash('message', 'Project created successfully.');
        return redirect()->route('projects.get');
    }

    public function update(Request $request){

        $project = $this->model->where('user_id', Auth::id())->findOrFail($request->id);
        $project->update(['name' => $request->name,'base_url' => $request->base_url]);
        session()->flash('message', 'Project updated successfully.');
        return redirect()->route('projects.get');
    }

    public function destroy($id){
        $project = $this->model->where('user_id', Auth::id())->findOrFail($id);

        DB::transaction(function () use ($project) {
            $groupIds = Group::where('project_id', $project->id)->pluck('id');
            $apiIds = Api::whereIn('group_id', $groupIds)->pluck('id');

            // Error responses are removed by their existing docs cascade rule.
            Docs::whereIn('api_id', $apiIds)->delete();
            Api::whereIn('id', $apiIds)->delete();
            Group::whereIn('id', $groupIds)->delete();
            $project->viewers()->detach();
            $project->delete();
        });

        session()->flash('message', 'Project deleted successfully.');
        return redirect()->route('projects.get');
    }

    public function setActive($id){
        $this->model->where('user_id',Auth::user()->id)->update(['active' => 0]);
        $project = $this->model->where('user_id', Auth::id())->findOrFail($id);
        $project->active = 1;
        $project->save();
        session()->flash('message', 'Project switched.');
        return redirect('/home');
    }

    public function addViewer(Request $request, $id){
        $project = $this->model->where('user_id', Auth::id())->findOrFail($id);
        $request->validate(['email' => 'required|email|exists:users,email']);
        $viewer = User::where('email', $request->email)->firstOrFail();
        if ($viewer->id === Auth::id()) return back()->withErrors(['email' => 'You already own this project.']);
        $project->viewers()->syncWithoutDetaching([$viewer->id]);
        session()->flash('message', 'Viewer added successfully.');
        return back();
    }

    public function removeViewer($id, $userId){
        $project = $this->model->where('user_id', Auth::id())->findOrFail($id);
        $project->viewers()->detach($userId);
        session()->flash('message', 'Viewer removed.');
        return back();
    }

    public function updateViewer(Request $request, $id, $userId){
        $project = $this->model->where('user_id', Auth::id())->findOrFail($id);
        abort_unless($project->viewers()->where('users.id', $userId)->exists(), 404);
        $request->validate(['email' => 'required|email|exists:users,email']);
        $replacement = User::where('email', $request->email)->firstOrFail();
        if ($replacement->id === Auth::id()) return back()->withErrors(['email' => 'You already own this project.']);

        $project->viewers()->detach($userId);
        $project->viewers()->syncWithoutDetaching([$replacement->id]);
        session()->flash('message', 'Viewer updated successfully.');
        return back();
    }

}
