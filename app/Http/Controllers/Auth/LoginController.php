<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class LoginController extends Controller {
    public function showLogin() {
        // If already logged in, go to dashboard
        if (Auth::check()) {
            return redirect()->route('dashboard.index');
        }
        return view('auth.login');
    }

    public function login(Request $request) {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|min:6',
        ]);
        $credentials = $request->only('email', 'password');
        $remember    = $request->boolean('remember');
        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            $user = Auth::user();
            if ($user->status === 'inactive') {
                Auth::logout();
                return back()->withErrors(['email' => 'Your account is inactive. Contact admin.']);
            }
            // Always redirect to dashboard
            return redirect()->intended(route('dashboard.index'))
                ->with('success', 'Welcome back, ' . $user->name . '!');
        }
        return back()->withErrors(['email' => 'Invalid email or password.'])
                    ->withInput($request->only('email'));
    }
    
    public function showRegister() {
        if (Auth::check()) {
            return redirect()->route('dashboard.index');
        }
        return view('auth.register');
    }

    public function register(Request $request) {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'gender'   => 'nullable|in:male,female,other',
            'phone'    => 'nullable|string|max:20',
        ]);
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'phone'    => $request->phone,
            'gender'   => $request->gender,
            'status'   => 'inactive', // Needs admin approval
        ]);
        // Assign default student role
        $user->assignRole('student');
        // Notify admins
        \App\Models\SystemNotification::notifyAdmins(
            'New User Registration',
            $request->name . ' has registered and needs approval',
            route('dashboard.users.index'),
            'fas fa-user-plus',
            '#2563eb'
        );
        return redirect()->route('login')
            ->with('success', 'Registration successful! Please wait for Admin approval before logging in.');
    }

    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');   
    }

    public function showForgotPassword() {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request) {
        $request->validate(['email' => 'required|email']);
        $status = Password::sendResetLink($request->only('email'));
        return $status === Password::RESET_LINK_SENT
            ? back()->with('success', 'Password reset link sent to your email!')
            : back()->withErrors(['email' => __($status)]);
    }

    public function showResetPassword(Request $request, $token) {
        return view('auth.reset-password', ['token' => $token, 'email' => $request->email]);
    }

    public function resetPassword(Request $request) {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill(['password' => Hash::make($password)])->save();
            }
        );
        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('success', 'Password reset successfully!')
            : back()->withErrors(['email' => __($status)]);
    }
}