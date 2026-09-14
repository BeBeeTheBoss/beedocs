<?php

namespace App\Http\Controllers;

use App\Models\Api;
use App\Models\Docs;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Project;

class GroupController extends Controller
{
    public function __construct(protected Group $model)
    {

    }

    public function store(Request $request){
        Project::where('user_id', Auth::id())->findOrFail($request->project_id);
        $this->model->create([
            'project_id' => $request->project_id,
            'name' => $request->name,
        ]);

        session()->flash('message', 'Group created successfully.');
        return back();
    }

    public function destroy($groupId){
        $groupId = (int) $groupId;
        $group = $this->model->whereHas('project', fn($query) => $query->where('user_id', Auth::id()))->findOrFail($groupId);

        $apis = Api::where('group_id',$groupId)->get();
        foreach($apis as $api){
            Docs::where('api_id',$api->id)->delete();
            $api->delete();
        }

        $group->delete();

        session()->flash('message', 'Group deleted successfully.');
        return back();

    }

}
