<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubjectController extends Controller
{
    public function index()
    {
        // Check if user is admin
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $subjects = Subject::orderBy('subject_name')->paginate(10);
        
        return view('admin.subjects.index', compact('subjects'));
    }
    
    public function create()
    {
        // Check if user is admin
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        return view('admin.subjects.create');
    }
    
    public function store(Request $request)
    {
        // Check if user is admin
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $request->validate([
            'subject_code' => 'required|string|max:20|unique:subjects',
            'subject_name' => 'required|string|max:100',
            'description' => 'nullable|string',
        ]);
        
        Subject::create([
            'subject_code' => $request->subject_code,
            'subject_name' => $request->subject_name,
            'description' => $request->description,
        ]);
        
        return redirect()->route('subjects.index')
            ->with('success', 'Subject created successfully');
    }
    
    public function edit($id)
    {
        // Check if user is admin
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $subject = Subject::findOrFail($id);
        
        return view('admin.subjects.edit', compact('subject'));
    }
    
    public function update(Request $request, $id)
    {
        // Check if user is admin
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $subject = Subject::findOrFail($id);
        
        $request->validate([
            'subject_code' => 'required|string|max:20|unique:subjects,subject_code,' . $id . ',subject_id',
            'subject_name' => 'required|string|max:100',
            'description' => 'nullable|string',
        ]);
        
        $subject->update([
            'subject_code' => $request->subject_code,
            'subject_name' => $request->subject_name,
            'description' => $request->description,
        ]);
        
        return redirect()->route('subjects.index')
            ->with('success', 'Subject updated successfully');
    }
    
    public function destroy($id)
    {
        // Check if user is admin
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $subject = Subject::findOrFail($id);
        
        // Check if subject is assigned to any classes
        if ($subject->classes()->count() > 0) {
            return redirect()->route('subjects.index')
                ->with('error', 'Cannot delete subject assigned to classes');
        }
        
        $subject->delete();
        
        return redirect()->route('subjects.index')
            ->with('success', 'Subject deleted successfully');
    }
}