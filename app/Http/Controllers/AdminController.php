<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Administrator;
use App\Models\Teacher;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth');
    //     $this->middleware('role:admin');
    // }
    
    // Admin dashboard
    public function dashboard(Request $request)
    {
        $user = Auth::user();

        if ($user->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        $totalAdmins = User::where('role', 'admin')->count();
        $totalTeachers = User::where('role', 'teacher')->count();
        $totalStudents = User::where('role', 'student')->count();
        
        return view('admin.dashboard', compact('totalAdmins', 'totalTeachers', 'totalStudents'));
    }
    
    // List users
    public function users(Request $request)
    {
        $role = $request->input('role', 'all');
        
        $query = User::query();
        
        if ($role !== 'all') {
            $query->where('role', $role);
        }
        
        $users = $query->paginate(10);
        
        return view('admin.users.index', compact('users', 'role'));
    }
    
    // Show create user form
    public function createUser()
    {
        return view('admin.users.create');
    }
    
    // Store new user
    public function storeUser(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:50|unique:users',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,teacher,student',
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);
        
        DB::beginTransaction();
        
        try {
            // Create user
            $user = User::create([
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'is_active' => true,
            ]);
            
            // Create profile based on role
            $profileData = [
                'user_id' => $user->user_id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'phone' => $request->phone,
                'address' => $request->address,
            ];
            
            if ($request->role === 'admin') {
                Administrator::create($profileData);
            } elseif ($request->role === 'teacher') {
                // Additional teacher fields
                $request->validate([
                    'employee_id' => 'required|string|max:20|unique:teachers',
                    'gender' => 'required|in:male,female,other',
                    'date_of_birth' => 'nullable|date',
                    'join_date' => 'required|date',
                    'qualification' => 'nullable|string|max:100',
                ]);
                
                $profileData = array_merge($profileData, [
                    'employee_id' => $request->employee_id,
                    'gender' => $request->gender,
                    'date_of_birth' => $request->date_of_birth,
                    'join_date' => $request->join_date,
                    'qualification' => $request->qualification,
                ]);
                
                Teacher::create($profileData);
            } elseif ($request->role === 'student') {
                // Additional student fields
                $request->validate([
                    'admission_number' => 'required|string|max:20|unique:students',
                    'gender' => 'required|in:male,female,other',
                    'date_of_birth' => 'required|date',
                    'admission_date' => 'required|date',
                    'emergency_contact_name' => 'nullable|string|max:100',
                    'emergency_contact_phone' => 'nullable|string|max:20',
                ]);
                
                $profileData = array_merge($profileData, [
                    'admission_number' => $request->admission_number,
                    'gender' => $request->gender,
                    'date_of_birth' => $request->date_of_birth,
                    'admission_date' => $request->admission_date,
                    'emergency_contact_name' => $request->emergency_contact_name,
                    'emergency_contact_phone' => $request->emergency_contact_phone,
                ]);
                
                Student::create($profileData);
            }
            
            DB::commit();
            
            return redirect()->route('admin.users', ['role' => $request->role])
                ->with('success', 'User created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error creating user: ' . $e->getMessage());
        }
    }
    
    // Show edit user form
    public function editUser($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }
    
    // Update user
    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'username' => 'required|string|max:50|unique:users,username,' . $id . ',user_id',
            'email' => 'required|string|email|max:100|unique:users,email,' . $id . ',user_id',
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'is_active' => 'required|boolean',
        ]);
        
        DB::beginTransaction();
        
        try {
            // Update user
            $user->username = $request->username;
            $user->email = $request->email;
            $user->is_active = $request->is_active;
            
            if ($request->filled('password')) {
                $request->validate([
                    'password' => 'string|min:8|confirmed',
                ]);
                $user->password = Hash::make($request->password);
            }
            
            $user->save();
            
            // Update profile
            $profile = $user->profile();
            
            if ($profile) {
                $profileData = [
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'phone' => $request->phone,
                    'address' => $request->address,
                ];
                
                // Add role-specific fields
                if ($user->isTeacher()) {
                    $request->validate([
                        'qualification' => 'nullable|string|max:100',
                    ]);
                    
                    $profileData['qualification'] = $request->qualification;
                } elseif ($user->isStudent()) {
                    $request->validate([
                        'emergency_contact_name' => 'nullable|string|max:100',
                        'emergency_contact_phone' => 'nullable|string|max:20',
                    ]);
                    
                    $profileData['emergency_contact_name'] = $request->emergency_contact_name;
                    $profileData['emergency_contact_phone'] = $request->emergency_contact_phone;
                }
                
                $profile->update($profileData);
            }
            
            DB::commit();
            
            return redirect()->route('admin.users', ['role' => $user->role])
                ->with('success', 'User updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error updating user: ' . $e->getMessage());
        }
    }
    
    // Delete user
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        $role = $user->role;
        
        try {
            $user->delete(); // This will cascade delete the profile due to foreign key constraints
            return redirect()->route('admin.users', ['role' => $role])
                ->with('success', 'User deleted successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting user: ' . $e->getMessage());
        }
    }
}