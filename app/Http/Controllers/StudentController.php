<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:student');
    }
    
    // Student dashboard
    public function dashboard()
    {
        $student = Auth::user()->student;
        
        // This will be expanded later
        return view('student.dashboard', compact('student'));
    }
}