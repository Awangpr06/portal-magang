<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // HALAMAN LOGIN
    public function login()
    {
        return view('auth.login');
    }

    // PROSES LOGIN
    public function loginProses(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required',
            'password' => 'required'
        ]);

        if(Auth::attempt($credentials))
        {
            $request->session()->regenerate();

            $user = Auth::user();

            // REDIRECT BERDASARKAN ROLE
            if($user->role == 'super_admin')
            {
                return redirect('/admin/dashboard');
            }

            if($user->role == 'mentor')
            {
                return redirect('/mentor/dashboard');
            }

            if($user->role == 'pembimbing')
            {
                return redirect('/pembimbing/dashboard');
            }

            if($user->role == 'peserta')
            {
                return redirect('/peserta/dashboard');
            }
        }

        return back()->with('error', 'Email atau Password salah');
    }

    // HALAMAN REGISTER
    public function register()
    {
        return view('auth.register');
    }

    // PROSES REGISTER
    public function registerProses(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'email' => 'required|unique:users',
            'password' => 'required|min:6',
            'role' => 'required'
        ]);

        User::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role
        ]);

        return redirect('/login')
            ->with('success', 'Register berhasil');
    }

    // LOGOUT
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }
}