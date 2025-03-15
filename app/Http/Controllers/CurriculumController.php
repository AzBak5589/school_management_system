<?php

namespace App\Http\Controllers;

use App\Models\Curriculum;
use App\Models\ClassSubject;
use App\Models\Classes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CurriculumController extends Controller
{
    public function index(Request $request)
    {
        // Check if user is admin or teacher
        if (!in_array(Auth::user()->role, ['admin', 'teacher'])) {
            abort(403, 'Unauthorized action.');
        }
        
        $classId = $request->input('class_id');
        $subjectId = $request->input('subject_id');
        
        $query = Curriculum::with(['classSubject.class', 'classSubject.subject', 'classSubject.teacher', 'creator']);
        
        if ($classId) {
            $query->whereHas('classSubject', function($q) use ($classId) {
                $q->where('class_id', $classId);
            });
        }
        
        if ($subjectId) {
            $query->whereHas('classSubject', function($q) use ($subjectId) {
                $q->where('subject_id', $subjectId);
            });
        }
        
        // If it's a teacher, show only their curriculum
        if (Auth::user()->role === 'teacher') {
            $teacherId = Auth::user()->teacher->teacher_id;
            $query->whereHas('classSubject', function($q) use ($teacherId) {
                $q->where('teacher_id', $teacherId);
            });
        }
        
        $curriculumItems = $query->orderBy('created_at', 'desc')->paginate(10);
        
        // Get classes and subjects for filter dropdowns
        $classes = Classes::orderBy('class_name')->get();
        $subjects = \App\Models\Subject::orderBy('subject_name')->get();
        
        return view('curriculum.index', compact('curriculumItems', 'classes', 'subjects', 'classId', 'subjectId'));
    }
    
    public function create()
    {
        // Check if user is admin or teacher
        if (!in_array(Auth::user()->role, ['admin', 'teacher'])) {
            abort(403, 'Unauthorized action.');
        }
        
        // Get available class subjects
        if (Auth::user()->role === 'admin') {
            $classSubjects = ClassSubject::with(['class', 'subject', 'teacher'])->get();
        } else {
            $teacherId = Auth::user()->teacher->teacher_id;
            $classSubjects = ClassSubject::with(['class', 'subject'])
                                        ->where('teacher_id', $teacherId)
                                        ->get();
        }
        
        return view('curriculum.create', compact('classSubjects'));
    }
    
    public function store(Request $request)
    {
        // Check if user is admin or teacher
        if (!in_array(Auth::user()->role, ['admin', 'teacher'])) {
            abort(403, 'Unauthorized action.');
        }
        
        $request->validate([
            'class_subject_id' => 'required|exists:class_subjects,class_subject_id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'resource_link' => 'nullable|string|max:255',
        ]);
        
        // If it's a teacher, verify they are assigned to this class subject
        if (Auth::user()->role === 'teacher') {
            $teacherId = Auth::user()->teacher->teacher_id;
            $classSubject = ClassSubject::findOrFail($request->class_subject_id);
            
            if ($classSubject->teacher_id !== $teacherId) {
                return redirect()->back()->withInput()
                    ->with('error', 'You are not authorized to add curriculum to this class subject.');
            }
        }
        
        Curriculum::create([
            'class_subject_id' => $request->class_subject_id,
            'title' => $request->title,
            'description' => $request->description,
            'resource_link' => $request->resource_link,
            'created_by' => Auth::id(),
        ]);
        
        return redirect()->route('curriculum.index')
            ->with('success', 'Curriculum item created successfully');
    }
    
    public function edit($id)
    {
        // Check if user is admin or teacher
        if (!in_array(Auth::user()->role, ['admin', 'teacher'])) {
            abort(403, 'Unauthorized action.');
        }
        
        $curriculumItem = Curriculum::with(['classSubject.class', 'classSubject.subject'])->findOrFail($id);
        
        // If it's a teacher, verify they are the creator or assigned to this class subject
        if (Auth::user()->role === 'teacher') {
            $teacherId = Auth::user()->teacher->teacher_id;
            
            if ($curriculumItem->classSubject->teacher_id !== $teacherId && $curriculumItem->created_by !== Auth::id()) {
                abort(403, 'You are not authorized to edit this curriculum item.');
            }
        }
        
        // Get available class subjects
        if (Auth::user()->role === 'admin') {
            $classSubjects = ClassSubject::with(['class', 'subject', 'teacher'])->get();
        } else {
            $teacherId = Auth::user()->teacher->teacher_id;
            $classSubjects = ClassSubject::with(['class', 'subject'])
                                        ->where('teacher_id', $teacherId)
                                        ->get();
        }
        
        return view('curriculum.edit', compact('curriculumItem', 'classSubjects'));
    }
    
    public function update(Request $request, $id)
    {
        // Check if user is admin or teacher
        if (!in_array(Auth::user()->role, ['admin', 'teacher'])) {
            abort(403, 'Unauthorized action.');
        }
        
        $curriculumItem = Curriculum::findOrFail($id);
        
        // If it's a teacher, verify they are the creator or assigned to this class subject
        if (Auth::user()->role === 'teacher') {
            $teacherId = Auth::user()->teacher->teacher_id;
            
            if ($curriculumItem->classSubject->teacher_id !== $teacherId && $curriculumItem->created_by !== Auth::id()) {
                abort(403, 'You are not authorized to update this curriculum item.');
            }
        }
        
        $request->validate([
            'class_subject_id' => 'required|exists:class_subjects,class_subject_id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'resource_link' => 'nullable|string|max:255',
        ]);
        
        // If it's a teacher, verify they are assigned to the new class subject
        if (Auth::user()->role === 'teacher' && $request->class_subject_id != $curriculumItem->class_subject_id) {
            $teacherId = Auth::user()->teacher->teacher_id;
            $classSubject = ClassSubject::findOrFail($request->class_subject_id);
            
            if ($classSubject->teacher_id !== $teacherId) {
                return redirect()->back()->withInput()
                    ->with('error', 'You are not authorized to assign this curriculum to this class subject.');
            }
        }
        
        $curriculumItem->update([
            'class_subject_id' => $request->class_subject_id,
            'title' => $request->title,
            'description' => $request->description,
            'resource_link' => $request->resource_link,
        ]);
        
        return redirect()->route('curriculum.index')
            ->with('success', 'Curriculum item updated successfully');
    }
    
    public function destroy($id)
    {
        // Check if user is admin or teacher
        if (!in_array(Auth::user()->role, ['admin', 'teacher'])) {
            abort(403, 'Unauthorized action.');
        }
        
        $curriculumItem = Curriculum::findOrFail($id);
        
        // If it's a teacher, verify they are the creator or assigned to this class subject
        if (Auth::user()->role === 'teacher') {
            $teacherId = Auth::user()->teacher->teacher_id;
            
            if ($curriculumItem->classSubject->teacher_id !== $teacherId && $curriculumItem->created_by !== Auth::id()) {
                abort(403, 'You are not authorized to delete this curriculum item.');
            }
        }
        
        $curriculumItem->delete();
        
        return redirect()->route('curriculum.index')
            ->with('success', 'Curriculum item deleted successfully');
    }
    
    public function show($id)
    {
        $curriculumItem = Curriculum::with(['classSubject.class', 'classSubject.subject', 'classSubject.teacher', 'creator'])
                                    ->findOrFail($id);
        
        // If it's a student, verify they are enrolled in this class
        if (Auth::user()->role === 'student') {
            $student = Auth::user()->student;
            
            if ($student->current_class_id !== $curriculumItem->classSubject->class_id) {
                abort(403, 'You are not authorized to view this curriculum item.');
            }
        }
        
        return view('curriculum.show', compact('curriculumItem'));
    }
}