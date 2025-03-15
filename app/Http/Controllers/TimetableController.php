<?php

namespace App\Http\Controllers;

use App\Models\Timetable;
use App\Models\ClassSubject;
use App\Models\Classes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TimetableController extends Controller
{
    public function index(Request $request)
    {
        $classId = $request->input('class_id');
        
        if (!$classId) {
            // If no class is selected, show a list of classes
            $classes = Classes::with(['academicYear'])->orderBy('class_name')->get();
            return view('timetable.select_class', compact('classes'));
        }
        
        $class = Classes::with(['academicYear'])->findOrFail($classId);
        
        // If it's a student, verify they are enrolled in this class
        if (Auth::user()->role === 'student') {
            $student = Auth::user()->student;
            
            if ($student->current_class_id !== $class->class_id) {
                abort(403, 'You are not authorized to view this class timetable.');
            }
        }
        
        // Get timetable entries for this class
        $timetableEntries = Timetable::with(['classSubject.subject', 'classSubject.teacher'])
                                    ->whereHas('classSubject', function($q) use ($classId) {
                                        $q->where('class_id', $classId);
                                    })
                                    ->orderBy('day_of_week')
                                    ->orderBy('start_time')
                                    ->get();
        
        // Organize entries by day of week for easier display
        $timetableByDay = [];
        foreach ($timetableEntries as $entry) {
            $timetableByDay[$entry->day_of_week][] = $entry;
        }
        
        return view('timetable.index', compact('class', 'timetableByDay'));
    }
    
    public function create($classId)
    {
        // Check if user is admin
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $class = Classes::with(['academicYear'])->findOrFail($classId);
        
        // Get subjects assigned to this class
        $classSubjects = ClassSubject::with(['subject', 'teacher'])
                                    ->where('class_id', $classId)
                                    ->get();
        
        if ($classSubjects->isEmpty()) {
            return redirect()->route('classes.subjects.index', $classId)
                ->with('error', 'You need to assign subjects to this class before creating a timetable.');
        }
        
        return view('timetable.create', compact('class', 'classSubjects'));
    }
    
    public function store(Request $request, $classId)
    {
        // Check if user is admin
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $class = Classes::findOrFail($classId);
        
        $request->validate([
            'class_subject_id' => 'required|exists:class_subjects,class_subject_id',
            'day_of_week' => 'required|integer|min:1|max:7',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'room' => 'nullable|string|max:50',
        ]);
        
        // Check if the class subject belongs to this class
        $classSubject = ClassSubject::findOrFail($request->class_subject_id);
        if ($classSubject->class_id !== $class->class_id) {
            return redirect()->back()->withInput()
                ->with('error', 'The selected subject is not assigned to this class.');
        }
        
        // Check for schedule conflicts
        $conflicts = $this->checkScheduleConflicts(
            $classId,
            $request->day_of_week,
            $request->start_time,
            $request->end_time,
            null // No timetable ID for new entries
        );
        
        if ($conflicts) {
            return redirect()->back()->withInput()
                ->with('error', 'There is a schedule conflict with another class period.');
        }
        
        Timetable::create([
            'class_subject_id' => $request->class_subject_id,
            'day_of_week' => $request->day_of_week,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'room' => $request->room,
        ]);
        
        return redirect()->route('timetable.index', ['class_id' => $classId])
            ->with('success', 'Timetable entry created successfully');
    }
    
    public function edit($classId, $id)
    {
        // Check if user is admin
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $class = Classes::with(['academicYear'])->findOrFail($classId);
        $timetableEntry = Timetable::with(['classSubject.subject', 'classSubject.teacher'])->findOrFail($id);
        
        // Verify the timetable entry belongs to this class
        if ($timetableEntry->classSubject->class_id !== $class->class_id) {
            abort(404, 'Timetable entry not found for this class.');
        }
        
        // Get subjects assigned to this class
        $classSubjects = ClassSubject::with(['subject', 'teacher'])
                                    ->where('class_id', $classId)
                                    ->get();
        
        return view('timetable.edit', compact('class', 'timetableEntry', 'classSubjects'));
    }
    
    public function update(Request $request, $classId, $id)
    {
        // Check if user is admin
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $class = Classes::findOrFail($classId);
        $timetableEntry = Timetable::findOrFail($id);
        
        // Verify the timetable entry belongs to this class
        if ($timetableEntry->classSubject->class_id !== $class->class_id) {
            abort(404, 'Timetable entry not found for this class.');
        }
        
        $request->validate([
            'class_subject_id' => 'required|exists:class_subjects,class_subject_id',
            'day_of_week' => 'required|integer|min:1|max:7',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'room' => 'nullable|string|max:50',
        ]);
        
        // Check if the class subject belongs to this class
        $classSubject = ClassSubject::findOrFail($request->class_subject_id);
        if ($classSubject->class_id !== $class->class_id) {
            return redirect()->back()->withInput()
                ->with('error', 'The selected subject is not assigned to this class.');
        }
        
        // Check for schedule conflicts
        $conflicts = $this->checkScheduleConflicts(
            $classId,
            $request->day_of_week,
            $request->start_time,
            $request->end_time,
            $id // Exclude current timetable entry
        );
        
        if ($conflicts) {
            return redirect()->back()->withInput()
                ->with('error', 'There is a schedule conflict with another class period.');
        }
        
        $timetableEntry->update([
            'class_subject_id' => $request->class_subject_id,
            'day_of_week' => $request->day_of_week,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'room' => $request->room,
        ]);
        
        return redirect()->route('timetable.index', ['class_id' => $classId])
            ->with('success', 'Timetable entry updated successfully');
    }
    
    public function destroy($classId, $id)
    {
        // Check if user is admin
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $timetableEntry = Timetable::findOrFail($id);
        
        // Verify the timetable entry belongs to this class
        if ($timetableEntry->classSubject->class_id != $classId) {
            abort(404, 'Timetable entry not found for this class.');
        }
        
        $timetableEntry->delete();
        
        return redirect()->route('timetable.index', ['class_id' => $classId])
            ->with('success', 'Timetable entry deleted successfully');
    }
    
    /**
     * Check for schedule conflicts with other timetable entries
     */
    private function checkScheduleConflicts($classId, $dayOfWeek, $startTime, $endTime, $excludeId = null)
    {
        $query = Timetable::with(['classSubject'])
                        ->whereHas('classSubject', function($q) use ($classId) {
                            $q->where('class_id', $classId);
                        })
                        ->where('day_of_week', $dayOfWeek);
        
        if ($excludeId) {
            $query->where('timetable_id', '!=', $excludeId);
        }
        
        // Convert times to minutes for easier comparison
        $startMinutes = $this->timeToMinutes($startTime);
        $endMinutes = $this->timeToMinutes($endTime);
        
        // Check for conflicts
        $conflicts = $query->get()->filter(function($entry) use ($startMinutes, $endMinutes) {
            $entryStartMinutes = $this->timeToMinutes($entry->start_time->format('H:i'));
            $entryEndMinutes = $this->timeToMinutes($entry->end_time->format('H:i'));
            
            // Check if the time periods overlap
            return !($startMinutes >= $entryEndMinutes || $endMinutes <= $entryStartMinutes);
        });
        
        return $conflicts->count() > 0;
    }
    
    /**
     * Convert time to minutes for easier comparison
     */
    private function timeToMinutes($time)
    {
        list($hours, $minutes) = explode(':', $time);
        return ($hours * 60) + $minutes;
    }
}