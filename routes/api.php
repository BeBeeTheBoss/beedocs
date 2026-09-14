<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware(['auth.check'])->group(function () {
    Route::post('/set-active-api',function(Request $request){
        return $request;
        User::where('id',Auth::user()->id)->update([
            'api_id' => $request->api_id
        ]);
        return response()->json(1);
    });
});
