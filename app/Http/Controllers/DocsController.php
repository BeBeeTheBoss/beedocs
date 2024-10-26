<?php

namespace App\Http\Controllers;

use App\Models\Docs;
use App\Models\ErrorResponse;
use Error;
use Inertia\Inertia;
use Illuminate\Http\Request;

class DocsController extends Controller
{
    public function __construct(protected Docs $model) {}

    public function index(){

    }

    public function createPage($apiId){
        return Inertia::render('Docs/Create',[
            'apiId' => $apiId
        ]);
    }

    public function store(Request $request){

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

        session(['message' => 'Document created successfully.']);
        return redirect()->route('home');
    }

    public function edit($docId){
        $doc = $this->model->where('id',$docId)->with('error_responses')->first();
        return Inertia::render('Docs/Edit',[
            'doc' => $doc
        ]);
    }

    public function update(Request $request){
        $doc = $this->model->find($request->id);
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

        session(['message' => 'Document updated successfully.']);
        return redirect()->route('home');
    }

}
