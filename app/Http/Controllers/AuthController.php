<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if (Auth::guard('admin')->attempt($credentials, $remember)) {
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        throw ValidationException::withMessages([
            'email' => ['Email atau password tidak sesuai.'],
        ]);
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admins',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $admin = Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::guard('admin')->login($admin);

        return redirect('/dashboard');
    }

    public function profile()
    {
        return view('layouts.profile');
    }

    public function updateProfile(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        if (!$admin instanceof \App\Models\Admin) {
            $admin = \App\Models\Admin::find($admin->id);
        }
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:admins,email,' . $admin->id,
            'password' => 'nullable|string|min:8',
        ]);

        $admin->name = $request->name;
        $admin->email = $request->email;
        $passwordChanged = false;
        if ($request->filled('password')) {
            $admin->password = Hash::make($request->password);
            $passwordChanged = true;
        }
        $admin->save();

        if ($passwordChanged) {
            Auth::guard('admin')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')->with('success', 'Password berhasil diubah. Silakan login kembali dengan password baru.');
        }

        return redirect()->route('profile')->with('success', 'Profil berhasil diperbarui.');
    }
}