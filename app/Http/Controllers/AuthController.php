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
        session()->flash('message', 'Signup successful! Now you can create your API docs.');
        return redirect()->route('projects.get');
    }

    public function login(Request $request){

        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = $this->model->where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'No account was found with this email.'])->onlyInput('email');
        }

        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'The password you entered is incorrect.'])->onlyInput('email');
        }

        Auth::login($user);
        $request->session()->regenerate();
        session()->flash('message', 'Login successful!');
        return redirect('/home');
    }

}
