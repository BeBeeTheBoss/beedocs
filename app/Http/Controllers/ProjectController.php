<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    public function __construct(protected Project $model)
    {

    }

    public function index(){
        $projects = $this->model->where('user_id',Auth::user()->id)->withCount('groups')->get();
        return Inertia::render('Project',['projects' => $projects]);
    }

    public function store(Request $request){

        $this->model->create([
            'name' => $request->name,
            'base_url' => $request->base_url,
            'user_id' => Auth::user()->id
        ]);
        session(['message' => 'Project created successfully.']);
        return redirect()->route('projects.get');
    }

    public function update(Request $request){

        $project = $this->model->find($request->id);
        $project->update(['name' => $request->name,'base_url' => $request->base_url]);
        session(['message' => 'Project updated successfully.']);
        return redirect()->route('projects.get');
    }

    public function destroy($id){
        $project = $this->model->find($id);
        $project->delete();
        session(['message' => 'Project deleted successfully.']);
        return redirect()->route('projects.get');
    }

    public function setActive($id){
        $this->model->where('user_id',Auth::user()->id)->update(['active' => 0]);
        $project = $this->model->find($id);
        $project->active = 1;
        $project->save();
        session(['message' => 'Project switched.']);
        return redirect('/home');
    }

}
