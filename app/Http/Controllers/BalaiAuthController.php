<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class BalaiAuthController extends Controller
{

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::guard('balai')->attempt($credentials)) {
            throw ValidationException::withMessages([
                'username' => 'Username atau password yang Anda masukkan salah.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('balai.dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::guard('balai')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('balai.login');
    }
}