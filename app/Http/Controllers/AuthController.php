<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Administrator;
use App\Models\Teacher;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    // Show login form
    public function showLoginForm()
    {
        return view('auth.login');
    }
    
    // Process login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);
        
        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            $user->last_login = now();
            $user->save();
            
            // Redirect based on role
            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            } elseif ($user->isTeacher()) {
                return redirect()->route('teacher.dashboard');
            } else {
                return redirect()->route('student.dashboard');
            }
        }
        
        return back()->withErrors([
            'username' => 'The provided credentials do not match our records.',
        ])->onlyInput('username');
    }
    
    // Logout user
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/login');
    }
    
    // Show profile
    public function profile()
    {
        $user = Auth::user();
        return view('auth.profile', compact('user'));
    }
    
    // Show edit profile form
    public function editProfile()
    {
        $user = Auth::user();
        return view('auth.edit-profile', compact('user'));
    }
    
    // Update profile
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'email' => 'required|email|unique:users,email,' . $user->user_id . ',user_id',
        ]);
        
        $user->email = $request->email;
        $user->save();
        
        $profile = $user->profile();
        
        if ($profile) {
            $profileData = [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'phone' => $request->phone,
                'address' => $request->address,
            ];
            
            // Add role-specific fields
            if ($user->isStudent()) {
                $profileData['emergency_contact_name'] = $request->emergency_contact_name;
                $profileData['emergency_contact_phone'] = $request->emergency_contact_phone;
            } elseif ($user->isTeacher()) {
                $profileData['qualification'] = $request->qualification;
            }
            
            $profile->update($profileData);
        }
        
        return redirect()->route('profile')->with('success', 'Profile updated successfully');
    }
    
    // Show change password form
    public function changePassword()
    {
        return view('auth.change-password');
    }
    
    // Process password change
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);
        
        $user = Auth::user();
        
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect']);
        }
        
        $user->password = Hash::make($request->password);
        $user->save();
        
        return redirect()->route('profile')->with('success', 'Password changed successfully');
    }
    
    // Show forgot password form
    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }
    
    // Process forgot password request
    public function processForgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);
        
        $user = User::where('email', $request->email)->first();
        
        // Generate reset token
        $token = Str::random(60);
        $user->reset_token = $token;
        $user->reset_token_expiry = now()->addHours(24);
        $user->save();
        
        // In a real application, you would send an email here
        // For now, we'll just redirect with the token for demonstration
        
        return redirect()->route('password.reset', ['token' => $token])->with('success', 'Password reset link generated');
    }
    
    // Show reset password form
    public function showResetPassword($token)
    {
        $user = User::where('reset_token', $token)
            ->where('reset_token_expiry', '>', now())
            ->first();
            
        if (!$user) {
            return redirect()->route('login')->with('error', 'Invalid or expired password reset token');
        }
        
        return view('auth.reset-password', ['token' => $token]);
    }
    
    // Process password reset
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);
        
        $user = User::where('reset_token', $request->token)
            ->where('reset_token_expiry', '>', now())
            ->first();
            
        if (!$user) {
            return redirect()->route('login')->with('error', 'Invalid or expired password reset token');
        }
        
        $user->password = Hash::make($request->password);
        $user->reset_token = null;
        $user->reset_token_expiry = null;
        $user->save();
        
        return redirect()->route('login')->with('success', 'Password has been reset successfully');
    }
}