<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use App\Models\AcademicYear;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClassController extends Controller
{
    public function index(Request $request)
    {
        // Check if user is admin
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        // Get query parameters
        $academicYearId = $request->input('academic_year', null);
        
        // Query builder
        $query = Classes::with(['academicYear', 'classTeacher']);
        
        // Filter by academic year if provided
        if ($academicYearId) {
            $query->where('academic_year_id', $academicYearId);
        }
        
        // Get classes with pagination
        $classes = $query->orderBy('class_name')->orderBy('section')->paginate(10);
        
        // Get all academic years for the filter dropdown
        $academicYears = AcademicYear::orderBy('year_name', 'desc')->get();
        
        return view('admin.classes.index', compact('classes', 'academicYears', 'academicYearId'));
    }
    
    public function create()
    {
        // Check if user is admin
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        // Get academic years and teachers for dropdowns
        $academicYears = AcademicYear::orderBy('year_name', 'desc')->get();
        $teachers = Teacher::orderBy('first_name')->orderBy('last_name')->get();
        
        return view('admin.classes.create', compact('academicYears', 'teachers'));
    }
    
    public function store(Request $request)
    {
        // Check if user is admin
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $request->validate([
            'academic_year_id' => 'required|exists:academic_years,academic_year_id',
            'class_name' => 'required|string|max:50',
            'section' => 'required|string|max:10',
            'capacity' => 'required|integer|min:1|max:100',
            'class_teacher_id' => 'nullable|exists:teachers,teacher_id',
        ]);
        
        // Check if class with same name and section already exists in this academic year
        $exists = Classes::where('academic_year_id', $request->academic_year_id)
            ->where('class_name', $request->class_name)
            ->where('section', $request->section)
            ->exists();
            
        if ($exists) {
            return redirect()->back()->withInput()
                ->with('error', 'A class with this name and section already exists for the selected academic year.');
        }
        
        Classes::create([
            'academic_year_id' => $request->academic_year_id,
            'class_name' => $request->class_name,
            'section' => $request->section,
            'capacity' => $request->capacity,
            'class_teacher_id' => $request->class_teacher_id,
        ]);
        
        return redirect()->route('classes.index')
            ->with('success', 'Class created successfully');
    }
    
    public function edit($id)
    {
        // Check if user is admin
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $class = Classes::findOrFail($id);
        
        // Get academic years and teachers for dropdowns
        $academicYears = AcademicYear::orderBy('year_name', 'desc')->get();
        $teachers = Teacher::orderBy('first_name')->orderBy('last_name')->get();
        
        return view('admin.classes.edit', compact('class', 'academicYears', 'teachers'));
    }
    
    public function update(Request $request, $id)
    {
        // Check if user is admin
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $class = Classes::findOrFail($id);
        
        $request->validate([
            'academic_year_id' => 'required|exists:academic_years,academic_year_id',
            'class_name' => 'required|string|max:50',
            'section' => 'required|string|max:10',
            'capacity' => 'required|integer|min:1|max:100',
            'class_teacher_id' => 'nullable|exists:teachers,teacher_id',
        ]);
        
        // Check if another class with same name and section already exists in this academic year
        $exists = Classes::where('academic_year_id', $request->academic_year_id)
            ->where('class_name', $request->class_name)
            ->where('section', $request->section)
            ->where('class_id', '!=', $id)
            ->exists();
            
        if ($exists) {
            return redirect()->back()->withInput()
                ->with('error', 'Another class with this name and section already exists for the selected academic year.');
        }
        
        $class->update([
            'academic_year_id' => $request->academic_year_id,
            'class_name' => $request->class_name,
            'section' => $request->section,
            'capacity' => $request->capacity,
            'class_teacher_id' => $request->class_teacher_id,
        ]);
        
        return redirect()->route('classes.index')
            ->with('success', 'Class updated successfully');
    }
    
    public function destroy($id)
    {
        // Check if user is admin
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $class = Classes::findOrFail($id);
        
        // Check if there are students enrolled in this class
        if ($class->students()->count() > 0) {
            return redirect()->route('classes.index')
                ->with('error', 'Cannot delete class with enrolled students');
        }
        
        // Check if there are enrollments for this class
        if ($class->enrollments()->count() > 0) {
            return redirect()->route('classes.index')
                ->with('error', 'Cannot delete class with enrollment records');
        }
        
        // Check if there are subjects assigned to this class
        if ($class->subjects()->count() > 0) {
            return redirect()->route('classes.index')
                ->with('error', 'Cannot delete class with assigned subjects');
        }
        
        $class->delete();
        
        return redirect()->route('classes.index')
            ->with('success', 'Class deleted successfully');
    }
    
    public function students($id)
    {
        // Check if user is admin or teacher
        if (!in_array(Auth::user()->role, ['admin', 'teacher'])) {
            abort(403, 'Unauthorized action.');
        }
        
        $class = Classes::with(['academicYear', 'classTeacher'])->findOrFail($id);
        
        // Get students enrolled in this class
        $students = $class->students()->paginate(15);
        
        return view('admin.classes.students', compact('class', 'students'));
    }
}