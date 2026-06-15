<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function index()
    {
        return view('auth.login');
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {

            $request->session()->regenerate();

            $user = Auth::user();

            if ($user->status != 'approved') {

                Auth::logout();

                return back()->with(
                    'error',
                    'Akun belum diverifikasi'
                );
            }

            if ($user->role == 'super_admin') {
                return redirect('/admin/dashboard');
            }

            if ($user->role == 'mentor') {
                return redirect('/mentor/dashboard');
            }

            if ($user->role == 'pembimbing') {
                return redirect('/pembimbing/dashboard');
            }

            if ($user->role == 'peserta') {
                return redirect('/peserta/dashboard');
            }
        }

        return back()->with(
            'error',
            'Email atau password salah'
        );
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }
}