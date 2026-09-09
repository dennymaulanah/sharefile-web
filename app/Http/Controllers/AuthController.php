<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Show admin login form.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.login');
    }

    /**
     * Handle login authentication.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'login.required' => 'Username atau Email wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $login = $credentials['login'];
        $password = $credentials['password'];
        $remember = $request->boolean('remember');

        // Check authentication by username or email
        if (
            Auth::attempt(['username' => $login, 'password' => $password], $remember) ||
            Auth::attempt(['email' => $login, 'password' => $password], $remember)
        ) {
            $request->session()->regenerate();

            return redirect()->intended(route('admin.dashboard'))
                ->with('success', 'Selamat datang di Dashboard Admin, ' . Auth::user()->name . '!');
        }

        return back()->withErrors([
            'login' => 'Username atau password yang Anda masukkan tidak sesuai.',
        ])->onlyInput('login');
    }

    /**
     * Handle logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('success', 'Anda telah berhasil logout.');
    }
}
