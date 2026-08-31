<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $remember = $request->boolean('remember');

        if (!Auth::attempt($credentials, $remember)) {
            return back()
                ->withInput($request->only('email'))
                ->with('error', 'Email atau password salah.');
        }

        $request->session()->regenerate();

        $user = Auth::user();

        if ($user->role === 'admin' || strtolower($user->email) === 'adminbelmart01@gmail.com') {
            return redirect()
                ->route('admin.dashboard')
                ->with('success', 'Selamat datang kembali, Admin!');
        }

        return redirect()
            ->route('home')
            ->with('success', 'Login berhasil! Selamat berbelanja.');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('home')
            ->with('success', 'Berhasil logout.');
    }
}