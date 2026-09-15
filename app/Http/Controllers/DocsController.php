<?php

namespace App\Http\Controllers;

use App\Models\Docs;
use App\Models\ErrorResponse;
use Error;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Api;

class DocsController extends Controller
{
    public function __construct(protected Docs $model) {}

    public function index(){

    }

    public function createPage($apiId){
        $api = Api::whereHas('group.project', fn($query) => $query->where('user_id', Auth::id()))
            ->with('group')->findOrFail($apiId);
        return Inertia::render('Docs/Create',[
            'apiId' => $apiId,
            'projectId' => $api->group->project_id,
        ]);
    }

    public function store(Request $request){
        $api = Api::whereHas('group.project', fn($query) => $query->where('user_id', Auth::id()))
            ->with('group')->findOrFail($request->api_id);

        $request->validate([
            'api_id' => 'required',
            'description' => 'required',
            'method' => 'required',
            'endpoints' => 'required',
            'is_authorized' => 'required',
            'request' => 'required',
            'response' => 'required',
        ]);

        $doc = $this->model->create($request->all());

        foreach($request->error_responses as $error){
            ErrorResponse::create([
                'doc_id' => $doc->id,
                'status_code' => $error['status_code'],
                'message' => $error['message']
            ]);
        }

        session()->flash('message', 'Document created successfully.');
        return redirect()->route('projects.api.view', [
            'project' => $api->group->project_id,
            'api' => $api->id,
        ]);
    }

    public function edit($docId){
        $doc = $this->model->whereHas('api.group.project', fn($query) => $query->where('user_id', Auth::id()))
            ->where('id',$docId)->with(['error_responses', 'api.group'])->firstOrFail();
        return Inertia::render('Docs/Edit',[
            'doc' => $doc,
            'projectId' => $doc->api->group->project_id,
        ]);
    }

    public function update(Request $request){
        $doc = $this->model->whereHas('api.group.project', fn($query) => $query->where('user_id', Auth::id()))->findOrFail($request->id);
        $input = $request->all();

        $doc->update([
            'api_id' => $input['api_id'],
            'description' => $input['description'],
            'method' => $input['method'],
            'endpoints' => $input['endpoints'],
            'is_authorized' => $input['is_authorized'],
            'request' => $input['request'],
            'response' => $input['response'],
        ]);

        ErrorResponse::where('doc_id',$doc->id)->delete();
        foreach($request->error_responses as $error){
            ErrorResponse::create([
                'doc_id' => $doc->id,
                'status_code' => $error['status_code'],
                'message' => $error['message']
            ]);
        }

        session()->flash('message', 'Document updated successfully.');
        $api = $doc->api()->with('group')->firstOrFail();
        return redirect()->route('projects.api.view', [
            'project' => $api->group->project_id,
            'api' => $api->id,
        ]);
    }

}
