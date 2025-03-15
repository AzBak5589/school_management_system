<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use App\Models\Subject;
use App\Models\ClassSubject;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClassSubjectController extends Controller
{
    public function index($classId)
    {
        // Check if user is admin
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $class = Classes::with(['academicYear'])->findOrFail($classId);
        $classSubjects = ClassSubject::with(['subject', 'teacher'])
                                    ->where('class_id', $classId)
                                    ->get();
        
        return view('admin.classes.subjects.index', compact('class', 'classSubjects'));
    }
    
    public function create($classId)
    {
        // Check if user is admin
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $class = Classes::with(['academicYear'])->findOrFail($classId);
        
        // Get all subjects not already assigned to this class
        $assignedSubjectIds = ClassSubject::where('class_id', $classId)
                                        ->pluck('subject_id')
                                        ->toArray();
        
        $subjects = Subject::whereNotIn('subject_id', $assignedSubjectIds)
                            ->orderBy('subject_name')
                            ->get();
        
        $teachers = Teacher::orderBy('first_name')
                            ->orderBy('last_name')
                            ->get();
        
        return view('admin.classes.subjects.create', compact('class', 'subjects', 'teachers'));
    }
    
    public function store(Request $request, $classId)
    {
        // Check if user is admin
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $class = Classes::findOrFail($classId);
        
        $request->validate([
            'subject_id' => 'required|exists:subjects,subject_id',
            'teacher_id' => 'required|exists:teachers,teacher_id',
        ]);
        
        // Check if this subject is already assigned to this class
        $exists = ClassSubject::where('class_id', $classId)
                            ->where('subject_id', $request->subject_id)
                            ->exists();
                            
        if ($exists) {
            return redirect()->back()->withInput()
                ->with('error', 'This subject is already assigned to this class.');
        }
        
        ClassSubject::create([
            'class_id' => $classId,
            'subject_id' => $request->subject_id,
            'teacher_id' => $request->teacher_id,
        ]);
        
        return redirect()->route('classes.subjects.index', $classId)
            ->with('success', 'Subject assigned successfully');
    }
    
    public function edit($classId, $id)
    {
        // Check if user is admin
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $class = Classes::with(['academicYear'])->findOrFail($classId);
        $classSubject = ClassSubject::findOrFail($id);
        $teachers = Teacher::orderBy('first_name')
                            ->orderBy('last_name')
                            ->get();
        
        return view('admin.classes.subjects.edit', compact('class', 'classSubject', 'teachers'));
    }
    
    public function update(Request $request, $classId, $id)
    {
        // Check if user is admin
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $classSubject = ClassSubject::findOrFail($id);
        
        $request->validate([
            'teacher_id' => 'required|exists:teachers,teacher_id',
        ]);
        
        $classSubject->update([
            'teacher_id' => $request->teacher_id,
        ]);
        
        return redirect()->route('classes.subjects.index', $classId)
            ->with('success', 'Subject assignment updated successfully');
    }
    
    public function destroy($classId, $id)
    {
        // Check if user is admin
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $classSubject = ClassSubject::findOrFail($id);
        $classSubject->delete();
        
        return redirect()->route('classes.subjects.index', $classId)
            ->with('success', 'Subject assignment removed successfully');
    }
}