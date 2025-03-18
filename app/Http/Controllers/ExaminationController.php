<?php

namespace App\Http\Controllers;

use App\Models\Examination;
use App\Models\AcademicYear;
use App\Models\Classes;
use App\Models\Subject;
use App\Models\ExamSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ExaminationController extends Controller
{
    public function index()
    {
        // Check if user is admin or teacher
        if (!in_array(Auth::user()->role, ['admin', 'teacher'])) {
            abort(403, 'Unauthorized action.');
        }
        
        // Get all examinations
        $examinations = Examination::with(['academicYear'])
                                   ->orderBy('start_date', 'desc')
                                   ->paginate(10);
        
        return view('examinations.index', compact('examinations'));
    }
    
    public function create()
    {
        // Only admin can create examinations
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        // Get academic years for dropdown
        $academicYears = AcademicYear::orderBy('year_name', 'desc')->get();
        
        return view('examinations.create', compact('academicYears'));
    }
    
    public function store(Request $request)
    {
        // Only admin can create examinations
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $request->validate([
            'academic_year_id' => 'required|exists:academic_years,academic_year_id',
            'exam_name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);
        
        Examination::create([
            'academic_year_id' => $request->academic_year_id,
            'exam_name' => $request->exam_name,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'created_by' => Auth::id(),
        ]);
        
        return redirect()->route('examinations.index')
            ->with('success', 'Examination created successfully');
    }
    
    public function edit($id)
    {
        // Only admin can edit examinations
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $examination = Examination::findOrFail($id);
        $academicYears = AcademicYear::orderBy('year_name', 'desc')->get();
        
        return view('examinations.edit', compact('examination', 'academicYears'));
    }
    
    public function update(Request $request, $id)
    {
        // Only admin can update examinations
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $examination = Examination::findOrFail($id);
        
        $request->validate([
            'academic_year_id' => 'required|exists:academic_years,academic_year_id',
            'exam_name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);
        
        $examination->update([
            'academic_year_id' => $request->academic_year_id,
            'exam_name' => $request->exam_name,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);
        
        return redirect()->route('examinations.index')
            ->with('success', 'Examination updated successfully');
    }
    
    public function destroy($id)
    {
        // Only admin can delete examinations
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $examination = Examination::findOrFail($id);
        
        // Check if there are any schedules or results for this exam
        if ($examination->schedules()->count() > 0 || $examination->results()->count() > 0) {
            return redirect()->route('examinations.index')
                ->with('error', 'Cannot delete examination with schedules or results');
        }
        
        $examination->delete();
        
        return redirect()->route('examinations.index')
            ->with('success', 'Examination deleted successfully');
    }
    
    public function show($id)
    {
        $examination = Examination::with(['academicYear'])->findOrFail($id);
        
        // Get all schedules for this examination
        $schedules = ExamSchedule::with(['class', 'subject'])
                                ->where('exam_id', $id)
                                ->orderBy('exam_date')
                                ->orderBy('start_time')
                                ->get();
        
        // Group schedules by class for easier display
        $schedulesByClass = $schedules->groupBy('class_id');
        
        return view('examinations.show', compact('examination', 'schedulesByClass'));
    }
    
    public function schedules($id)
    {
        // Only admin can manage exam schedules
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $examination = Examination::with(['academicYear'])->findOrFail($id);
        
        // Get classes for this academic year
        $classes = Classes::where('academic_year_id', $examination->academic_year_id)
                         ->orderBy('class_name')
                         ->get();
        
        // Get subjects
        $subjects = Subject::orderBy('subject_name')->get();
        
        // Get existing schedules
        $schedules = ExamSchedule::with(['class', 'subject'])
                                ->where('exam_id', $id)
                                ->orderBy('exam_date')
                                ->orderBy('start_time')
                                ->get();
        
        return view('examinations.schedules', compact('examination', 'classes', 'subjects', 'schedules'));
    }
    
    public function storeSchedule(Request $request, $id)
    {
        // Only admin can create exam schedules
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $examination = Examination::findOrFail($id);
        
        $request->validate([
            'class_id' => 'required|exists:classes,class_id',
            'subject_id' => 'required|exists:subjects,subject_id',
    'exam_date' => 'required|date|after_or_equal:' . $examination->start_date->format('Y-m-d') . '|before_or_equal:' . $examination->end_date->format('Y-m-d'),
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'room' => 'nullable|string|max:50',
        ]);
        
        // Check if a schedule for this class and subject already exists
        $exists = ExamSchedule::where('exam_id', $id)
                             ->where('class_id', $request->class_id)
                             ->where('subject_id', $request->subject_id)
                             ->exists();
        
        if ($exists) {
            return redirect()->back()->withInput()
                ->with('error', 'A schedule for this class and subject already exists');
        }
        
        ExamSchedule::create([
            'exam_id' => $id,
            'class_id' => $request->class_id,
            'subject_id' => $request->subject_id,
            'exam_date' => $request->exam_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'room' => $request->room,
        ]);
        
        return redirect()->route('examinations.schedules', $id)
            ->with('success', 'Exam schedule created successfully');
    }
    
    public function editSchedule($id, $scheduleId)
    {
        // Only admin can edit exam schedules
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $examination = Examination::with(['academicYear'])->findOrFail($id);
        $schedule = ExamSchedule::findOrFail($scheduleId);
        
        // Verify the schedule belongs to this examination
        if ($schedule->exam_id != $id) {
            abort(404, 'Schedule not found for this examination');
        }
        
        // Get classes for this academic year
        $classes = Classes::where('academic_year_id', $examination->academic_year_id)
                         ->orderBy('class_name')
                         ->get();
        
        // Get subjects
        $subjects = Subject::orderBy('subject_name')->get();
        
        return view('examinations.edit_schedule', compact('examination', 'schedule', 'classes', 'subjects'));
    }
    
    public function updateSchedule(Request $request, $id, $scheduleId)
    {
        // Only admin can update exam schedules
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $examination = Examination::findOrFail($id);
        $schedule = ExamSchedule::findOrFail($scheduleId);
        
        // Verify the schedule belongs to this examination
        if ($schedule->exam_id != $id) {
            abort(404, 'Schedule not found for this examination');
        }
        
        $request->validate([
            'class_id' => 'required|exists:classes,class_id',
            'subject_id' => 'required|exists:subjects,subject_id',
    'exam_date' => 'required|date|after_or_equal:' . $examination->start_date->format('Y-m-d') . '|before_or_equal:' . $examination->end_date->format('Y-m-d'),
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'room' => 'nullable|string|max:50',
        ]);
        
        // Check if a schedule for this class and subject already exists (excluding this one)
        $exists = ExamSchedule::where('exam_id', $id)
                             ->where('class_id', $request->class_id)
                             ->where('subject_id', $request->subject_id)
                             ->where('schedule_id', '!=', $scheduleId)
                             ->exists();
        
        if ($exists) {
            return redirect()->back()->withInput()
                ->with('error', 'Another schedule for this class and subject already exists');
        }
        
        $schedule->update([
            'class_id' => $request->class_id,
            'subject_id' => $request->subject_id,
            'exam_date' => $request->exam_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'room' => $request->room,
        ]);
        
        return redirect()->route('examinations.schedules', $id)
            ->with('success', 'Exam schedule updated successfully');
    }
    public function destroySchedule($id, $scheduleId)
    {
        // Only admin can delete exam schedules
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $schedule = ExamSchedule::findOrFail($scheduleId);
        
        // Verify the schedule belongs to this examination
        if ($schedule->exam_id != $id) {
            abort(404, 'Schedule not found for this examination');
        }
        
        // Check if there are any results recorded for this schedule
        $resultsExist = \App\Models\ExamResult::where('exam_id', $id)
                                            ->where('subject_id', $schedule->subject_id)
                                            ->whereHas('student', function($query) use ($schedule) {
                                                $query->where('current_class_id', $schedule->class_id);
                                            })
                                            ->exists();
        
        if ($resultsExist) {
            return redirect()->route('examinations.schedules', $id)
                ->with('error', 'Cannot delete schedule with recorded results');
        }
        
        $schedule->delete();
        
        return redirect()->route('examinations.schedules', $id)
            ->with('success', 'Exam schedule deleted successfully');
    }
    
    public function results($id)
    {
        // Check if user is admin or teacher
        if (!in_array(Auth::user()->role, ['admin', 'teacher'])) {
            abort(403, 'Unauthorized action.');
        }
        
        $examination = Examination::with(['academicYear'])->findOrFail($id);
        
        // Get classes for this academic year
        $classes = Classes::where('academic_year_id', $examination->academic_year_id)
                         ->orderBy('class_name')
                         ->get();
        
        return view('examinations.results', compact('examination', 'classes'));
    }
    
    public function classResults($id, $classId)
    {
        // Check if user is admin or teacher
        if (!in_array(Auth::user()->role, ['admin', 'teacher'])) {
            abort(403, 'Unauthorized action.');
        }
        
        $examination = Examination::with(['academicYear'])->findOrFail($id);
        $class = Classes::findOrFail($classId);
        
        // Verify the class belongs to the same academic year as the examination
        if ($class->academic_year_id != $examination->academic_year_id) {
            return redirect()->route('examinations.results', $id)
                ->with('error', 'The selected class does not belong to the academic year of this examination');
        }
        
        // If it's a teacher, check if they teach any subject to this class
        if (Auth::user()->role === 'teacher') {
            $teacherId = Auth::user()->teacher->teacher_id;
            
            $isClassTeacher = $class->class_teacher_id === $teacherId;
            
            $isSubjectTeacher = \App\Models\ClassSubject::where('class_id', $classId)
                                                      ->where('teacher_id', $teacherId)
                                                      ->exists();
            
            if (!$isClassTeacher && !$isSubjectTeacher) {
                abort(403, 'You are not authorized to view or manage results for this class');
            }
        }
        
        // Get all subjects for this class
        $subjects = \App\Models\ClassSubject::with(['subject'])
                                           ->where('class_id', $classId)
                                           ->get()
                                           ->map(function($item) {
                                               return $item->subject;
                                           })
                                           ->unique('subject_id');
        
        // Get all students in this class
        $students = Student::where('current_class_id', $classId)
                          ->orderBy('first_name')
                          ->get();
        
        // Get existing results
        $results = \App\Models\ExamResult::with(['subject'])
                                       ->where('exam_id', $id)
                                       ->whereIn('student_id', $students->pluck('student_id'))
                                       ->get()
                                       ->groupBy('student_id');
        
        return view('examinations.class_results', compact('examination', 'class', 'subjects', 'students', 'results'));
    }
    
    public function enterResults($id, $classId, $subjectId)
    {
        // Check if user is admin or the subject teacher
        if (Auth::user()->role === 'student') {
            abort(403, 'Unauthorized action.');
        }
        
        $examination = Examination::findOrFail($id);
        $class = Classes::findOrFail($classId);
        $subject = Subject::findOrFail($subjectId);
        
        // If it's a teacher, check if they teach this subject to this class
        if (Auth::user()->role === 'teacher') {
            $teacherId = Auth::user()->teacher->teacher_id;
            
            $isSubjectTeacher = \App\Models\ClassSubject::where('class_id', $classId)
                                                      ->where('teacher_id', $teacherId)
                                                      ->where('subject_id', $subjectId)
                                                      ->exists();
            
            if (!$isSubjectTeacher) {
                abort(403, 'You are not authorized to enter results for this subject');
            }
        }
        
        // Verify the class and examination are in the same academic year
        if ($class->academic_year_id != $examination->academic_year_id) {
            return redirect()->route('examinations.results', $id)
                ->with('error', 'The selected class does not belong to the academic year of this examination');
        }
        
        // Check if an exam schedule exists for this combination
        $scheduleExists = ExamSchedule::where('exam_id', $id)
                                    ->where('class_id', $classId)
                                    ->where('subject_id', $subjectId)
                                    ->exists();
        
        if (!$scheduleExists) {
            return redirect()->route('examinations.class_results', [$id, $classId])
                ->with('error', 'No exam schedule exists for this subject. Please create a schedule first.');
        }
        
        // Get all students in this class
        $students = Student::where('current_class_id', $classId)
                          ->orderBy('first_name')
                          ->get();
        
        // Get existing results
        $results = \App\Models\ExamResult::where('exam_id', $id)
                                       ->where('subject_id', $subjectId)
                                       ->whereIn('student_id', $students->pluck('student_id'))
                                       ->get()
                                       ->keyBy('student_id');
        
        return view('examinations.enter_results', compact('examination', 'class', 'subject', 'students', 'results'));
    }
    
    public function storeResults(Request $request, $id, $classId, $subjectId)
    {
        // Check if user is admin or the subject teacher
        if (Auth::user()->role === 'student') {
            abort(403, 'Unauthorized action.');
        }
        
        $examination = Examination::findOrFail($id);
        $class = Classes::findOrFail($classId);
        $subject = Subject::findOrFail($subjectId);
        
        // If it's a teacher, check if they teach this subject to this class
        if (Auth::user()->role === 'teacher') {
            $teacherId = Auth::user()->teacher->teacher_id;
            
            $isSubjectTeacher = \App\Models\ClassSubject::where('class_id', $classId)
                                                      ->where('teacher_id', $teacherId)
                                                      ->where('subject_id', $subjectId)
                                                      ->exists();
            
            if (!$isSubjectTeacher) {
                abort(403, 'You are not authorized to enter results for this subject');
            }
        }
        
        $request->validate([
            'marks' => 'required|array',
            'marks.*' => 'required|numeric|min:0',
            'total_marks' => 'required|numeric|min:1',
            'remarks' => 'nullable|array',
        ]);
        
        // Process each student's results
        foreach ($request->marks as $studentId => $marks) {
            $grade = $this->calculateGrade($marks, $request->total_marks);
            $remarks = $request->remarks[$studentId] ?? null;
            
            // Check if the student belongs to this class
            $student = Student::where('student_id', $studentId)
                            ->where('current_class_id', $classId)
                            ->first();
            
            if (!$student) {
                continue; // Skip if student doesn't belong to this class
            }
            
            // Try to find existing result
            $result = \App\Models\ExamResult::where('exam_id', $id)
                                          ->where('student_id', $studentId)
                                          ->where('subject_id', $subjectId)
                                          ->first();
            
            if ($result) {
                // Update existing record
                $result->update([
                    'marks_obtained' => $marks,
                    'total_marks' => $request->total_marks,
                    'grade' => $grade,
                    'remarks' => $remarks,
                    'entered_by' => Auth::id(),
                ]);
            } else {
                // Create new record
                \App\Models\ExamResult::create([
                    'exam_id' => $id,
                    'student_id' => $studentId,
                    'subject_id' => $subjectId,
                    'marks_obtained' => $marks,
                    'total_marks' => $request->total_marks,
                    'grade' => $grade,
                    'remarks' => $remarks,
                    'entered_by' => Auth::id(),
                ]);
            }
        }
        
        return redirect()->route('examinations.class_results', [$id, $classId])
            ->with('success', 'Examination results recorded successfully');
    }
    
    public function studentResults(Request $request)
    {
        // For students to view their own results
        if (Auth::user()->role !== 'student') {
            abort(403, 'Unauthorized action.');
        }
        
        $examId = $request->input('exam_id');
        
        $student = Auth::user()->student;
        
        // Get all examinations for the current academic year
        $examinations = Examination::whereHas('academicYear', function($query) use ($student) {
            $query->whereHas('classes', function($q) use ($student) {
                $q->where('class_id', $student->current_class_id);
            });
        })->orderBy('start_date', 'desc')->get();
        
        if (!$examId && $examinations->count() > 0) {
            $examId = $examinations->first()->exam_id;
        }
        
        if ($examId) {
            $examination = Examination::findOrFail($examId);
            
            // Get results for this examination
            $results = \App\Models\ExamResult::with(['subject'])
                                           ->where('exam_id', $examId)
                                           ->where('student_id', $student->student_id)
                                           ->get();
            
            // Calculate total and average
            $totalMarksObtained = $results->sum('marks_obtained');
            $totalMaxMarks = $results->sum('total_marks');
            $totalPercentage = $totalMaxMarks > 0 ? round(($totalMarksObtained / $totalMaxMarks) * 100, 2) : 0;
            
            return view('examinations.student_results', compact(
                'student', 'examination', 'examinations', 'results',
                'totalMarksObtained', 'totalMaxMarks', 'totalPercentage'
            ));
        } else {
            return view('examinations.student_results', compact('student', 'examinations'))
                ->with('info', 'No examination results available.');
        }
    }
    
    /**
     * Calculate grade based on marks and total marks
     */
    private function calculateGrade($marks, $totalMarks)
    {
        $percentage = ($marks / $totalMarks) * 100;
        
        if ($percentage >= 90) {
            return 'A+';
        } elseif ($percentage >= 80) {
            return 'A';
        } elseif ($percentage >= 70) {
            return 'B+';
        } elseif ($percentage >= 60) {
            return 'B';
        } elseif ($percentage >= 50) {
            return 'C+';
        } elseif ($percentage >= 40) {
            return 'C';
        } elseif ($percentage >= 33) {
            return 'D';
        } else {
            return 'F';
        }
    }
}