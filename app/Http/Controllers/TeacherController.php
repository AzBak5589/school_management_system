<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeacherController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:teacher');
    }
    
    // Teacher dashboard
    public function dashboard()
    {
        $teacher = Auth::user()->teacher;
        
        // This will be expanded later
        return view('teacher.dashboard', compact('teacher'));
    }
}