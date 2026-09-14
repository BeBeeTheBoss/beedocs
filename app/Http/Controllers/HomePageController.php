<?php

namespace App\Http\Controllers;

use App\Models\Api;
use Inertia\Inertia;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomePageController extends Controller
{
    private function accessibleProjects(){
        return Project::where('user_id', Auth::id())
            ->orWhereHas('viewers', fn($query) => $query->where('users.id', Auth::id()))
            ->with(['groups.apis:id,group_id,name'])
            ->get()
            ->each(fn($project) => $project->setAttribute('can_edit', $project->user_id === Auth::id()));
    }

    public function homePage(){
        $projects = $this->accessibleProjects();
        $activeProject = false;

        foreach($projects as $project){
            if($project->active == 1){
                $activeProject = $project;
                break;
            }
        }
        if(!$activeProject){
            return redirect()->route('projects.get');
        }

        $api = Api::find(Auth::user()->api_id);

        return Inertia::render('Home',[
            'projects' => $projects,
            'project' => $activeProject,
            'api' => $api ?? null,
            'canEdit' => true,
        ]);
    }

    public function projectPage(Project $project, ?Api $api = null){
        abort_unless($project->canBeViewedBy(Auth::user()), 403);
        if ($api) abort_unless($api->group()->where('project_id', $project->id)->exists(), 404);

        $projects = $this->accessibleProjects();
        $current = $projects->firstWhere('id', $project->id);

        return Inertia::render('Home', [
            'projects' => $projects,
            'project' => $current,
            'api' => $api,
            'canEdit' => $project->user_id === Auth::id(),
        ]);
    }

    public function apiData(Project $project, Api $api){
        abort_unless($project->canBeViewedBy(Auth::user()), 403);
        abort_unless($api->group()->where('project_id', $project->id)->exists(), 404);
        return response()->json(['data' => $api->docs()->with('error_responses')->first()]);
    }
}
