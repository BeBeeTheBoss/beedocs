<?php

namespace App\Http\Controllers;

use App\Models\Api;
use App\Models\Docs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Group;

class ApiController extends Controller
{

    public function __construct(protected Api $model)
    {

    }

    public function store(Request $request){
        Group::whereHas('project', fn($query) => $query->where('user_id', Auth::id()))->findOrFail($request->group_id);
        $this->model->create([
            'group_id' => $request->group_id,
            'name' => $request->name,
        ]);

        session()->flash('message', 'API created successfully.');
        return back();
    }

    public function destroy($apiId){
        $api = $this->model->whereHas('group.project', fn($query) => $query->where('user_id', Auth::id()))->findOrFail($apiId);
        Docs::where('api_id',$api->id)->delete();
        $api->delete();
        session()->flash('message', 'API deleted successfully.');
        return back();
    }

    public function update(Request $request, $apiId){
        $api = $this->model->whereHas('group.project', fn($query) => $query->where('user_id', Auth::id()))->findOrFail($apiId);
        $request->validate(['name' => 'required|string|max:255']);
        $api->update(['name' => $request->name]);
        session()->flash('message', 'Endpoint updated successfully.');
        return back();
    }
}
