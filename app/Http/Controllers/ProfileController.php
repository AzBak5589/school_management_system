<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the user's profile.
     *
     * @return \Illuminate\View\View
     */
    public function show()
    {
        $user = Auth::user();
        $profile = $user->profile();
        
        return view('profile.show', compact('user', 'profile'));
    }

    /**
     * Show the form for editing the user's profile.
     *
     * @return \Illuminate\View\View
     */
    public function edit()
    {
        $user = Auth::user();
        $profile = $user->profile();
        
        return view('profile.edit', compact('user', 'profile'));
    }

    /**
     * Update the user's profile information.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        // Validate common fields
        $request->validate([
            'email' => ['required', 'string', 'email', 'max:100', Rule::unique('users', 'email')->ignore($user->user_id, 'user_id')],
            'first_name' => ['required', 'string', 'max:50'],
            'last_name' => ['required', 'string', 'max:50'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
        ]);
        
        // Update user email
        $user->email = $request->email;
        $user->save();
        
        // Get the profile based on user role
        $profile = $user->profile();
        
        if ($profile) {
            // Common profile fields
            $profileData = [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'phone' => $request->phone,
                'address' => $request->address,
            ];
            
            // Add role-specific validation and updates
            if ($user->isTeacher()) {
                $request->validate([
                    'qualification' => ['nullable', 'string', 'max:100'],
                ]);
                
                $profileData['qualification'] = $request->qualification;
            } elseif ($user->isStudent()) {
                $request->validate([
                    'emergency_contact_name' => ['nullable', 'string', 'max:100'],
                    'emergency_contact_phone' => ['nullable', 'string', 'max:20'],
                ]);
                
                $profileData['emergency_contact_name'] = $request->emergency_contact_name;
                $profileData['emergency_contact_phone'] = $request->emergency_contact_phone;
            }
            
            // Update the profile
            $profile->update($profileData);
        }
        
        return redirect()->route('profile.show')
            ->with('success', 'Profile updated successfully');
    }

    /**
     * Show the form for changing the user's password.
     *
     * @return \Illuminate\View\View
     */
    public function showChangePasswordForm()
    {
        return view('profile.change-password');
    }

    /**
     * Update the user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        
        $user = Auth::user();
        
        // Check if current password is correct
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect']);
        }
        
        // Update password
        $user->password = Hash::make($request->password);
        $user->save();
        
        return redirect()->route('profile.show')
            ->with('success', 'Password changed successfully');
    }
}