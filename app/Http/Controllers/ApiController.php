<?php

namespace App\Http\Controllers;

use App\Models\Api;
use App\Models\Docs;
use Illuminate\Http\Request;

class ApiController extends Controller
{

    public function __construct(protected Api $model)
    {

    }

    public function store(Request $request){
        $this->model->create([
            'group_id' => $request->group_id,
            'name' => $request->name,
        ]);

        session(['message' => 'Api created successfully.']);
        return redirect()->route('home');
    }

    public function destroy($apiId){
        $api = $this->model->find($apiId);
        Docs::where('api_id',$api->id)->delete();
        $api->delete();
        session(['message' => 'Api deleted successfully.']);
        return back();
    }
}
