<?php

namespace App\Http\Controllers;

use App\Models\Api;
use Inertia\Inertia;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomePageController extends Controller
{
    public function homePage(){

        $projects = Project::where('user_id',Auth::user()->id)->with('groups',function($query){
            $query->with('apis:id,group_id,name');
        })->get();
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
            'api' => $api ?? null
        ]);
    }
}
