<?php

namespace App\Http\Controllers;

use App\Models\Api;
use App\Models\Docs;
use App\Models\Group;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function __construct(protected Group $model)
    {

    }

    public function store(Request $request){
        $this->model->create([
            'project_id' => $request->project_id,
            'name' => $request->name,
        ]);

        session(['message' => 'Group created successfully.']);
        return redirect()->route('home');
    }

    public function destroy($groupId){
        $groupId = (int) $groupId;
        $group = $this->model->find($groupId);

        $apis = Api::where('group_id',$groupId)->get();
        foreach($apis as $api){
            Docs::where('api_id',$api->id)->delete();
            $api->delete();
        }

        $group->delete();

        session(['message' => 'Group deleted successfully.']);
        return back();

    }

}
