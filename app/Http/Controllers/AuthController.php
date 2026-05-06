<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        Auth::login($user);

        // Log registration activity
        ActivityLog::log($user->id, 'user_registered', 'New user account created');

        return redirect()->route('dashboard');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Check for admin login with email "admin@gmail.com" and password "12345678"
        if ($request->input('email') === 'admin@gmail.com' && $request->input('password') === '12345678') {
            $admin = User::where('email', 'admin@gmail.com')->first();
            if ($admin && $admin->is_admin) {
                Auth::login($admin);
                $request->session()->regenerate();
                
                // Log admin login activity
                ActivityLog::log($admin->id, 'admin_login', 'Admin logged in to system');
                
                return redirect()->route('admin.dashboard');
            }
        }

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'The provided credentials do not match our records.'])->onlyInput('email');
        }

        $request->session()->regenerate();
        $user = Auth::user();

        // Log login activity
        ActivityLog::log($user->id, 'user_login', 'User logged in to system');

        // Redirect admin users to admin dashboard
        if ($user->is_admin) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Log logout activity
        if ($user) {
            ActivityLog::log($user->id, 'user_logout', 'User logged out from system');
        }

        return redirect()->route('login');
    }
}
