<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    public function __construct(protected User $model)
    {

    }

    public function loginPage(){
        return Inertia::render('Login');
    }

    public function signupPage(){
        return Inertia::render('Signup');
    }

    public function signup(Request $request){

        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
        ]);

        $user = $this->model->create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);
        session(['message' => 'Signup successful! Now you can create your api docs.']);
        return redirect()->route('projects.get');
    }

    public function login(Request $request){

        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            session(['message' => 'Login successful!']);
            return redirect('/home');
        }

        return back()->withErrors(['message' => 'Invalid credentials. Please login again!']);
    }

}
