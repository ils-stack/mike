<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth; // ✅ THIS IS MISSING
use App\Models\User;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        // return view('auth.login');
        return view('layouts.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors(['email' => 'Invalid credentials.']);
        }

        Auth::login($user); // ✅ Now this will work

        return redirect()->intended('/dashboard');
    }

    public function logout()
    {
        Auth::logout(); // ✅ This too
        return redirect('/login');
    }
}
