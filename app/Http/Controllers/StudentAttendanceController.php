<?php

namespace App\Http\Controllers;

use App\Models\StudentAttendance;
use App\Models\Student;
use App\Models\Classes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class StudentAttendanceController extends Controller
{
    public function index(Request $request)
    {
        // Only admin and teachers can access attendance records
        if (!in_array(Auth::user()->role, ['admin', 'teacher'])) {
            abort(403, 'Unauthorized action.');
        }
        
        $classId = $request->input('class_id');
        $date = $request->input('date') ? Carbon::parse($request->input('date')) : Carbon::today();
        
        // Get classes for the filter dropdown
        $classes = Classes::where('academic_year_id', function ($query) {
            $query->select('academic_year_id')
                ->from('academic_years')
                ->where('is_current', true)
                ->limit(1);
        })->orderBy('class_name')->get();
        
        // If no class is selected, show the class selection view
        if (!$classId && Auth::user()->role === 'admin') {
            return view('attendance.student.select_class', compact('classes', 'date'));
        }
        
        // For teachers, only show their classes
        if (Auth::user()->role === 'teacher') {
            $teacherId = Auth::user()->teacher->teacher_id;
            
            // If no class selected, use the first class they teach
            if (!$classId) {
                $teacherClass = Classes::where('class_teacher_id', $teacherId)->first();
                
                if ($teacherClass) {
                    $classId = $teacherClass->class_id;
                } else {
                    // If teacher doesn't have any assigned classes
                    return view('attendance.student.select_class', compact('classes', 'date'))
                        ->with('error', 'You are not assigned as a class teacher to any class.');
                }
            } else {
                // Check if the teacher is authorized to take attendance for this class
                $isClassTeacher = Classes::where('class_id', $classId)
                    ->where('class_teacher_id', $teacherId)
                    ->exists();
                
                $isSubjectTeacher = \App\Models\ClassSubject::where('class_id', $classId)
                    ->where('teacher_id', $teacherId)
                    ->exists();
                
                if (!$isClassTeacher && !$isSubjectTeacher) {
                    abort(403, 'You are not authorized to take attendance for this class.');
                }
            }
        }
        
        // Get the class details
        $class = Classes::findOrFail($classId);
        
        // Get students for this class
        $students = Student::where('current_class_id', $classId)->orderBy('first_name')->get();
        
        // Get existing attendance records for the selected date and class
        $attendanceRecords = StudentAttendance::where('class_id', $classId)
            ->where('date', $date->format('Y-m-d'))
            ->get()
            ->keyBy('student_id');
        
        return view('attendance.student.index', compact('class', 'students', 'date', 'attendanceRecords', 'classes'));
    }
    
    public function store(Request $request)
    {
        // Only admin and teachers can record attendance
        if (!in_array(Auth::user()->role, ['admin', 'teacher'])) {
            abort(403, 'Unauthorized action.');
        }
        
        $request->validate([
            'class_id' => 'required|exists:classes,class_id',
            'date' => 'required|date',
            'status' => 'required|array',
            'status.*' => 'required|in:present,absent,late,excused',
            'remarks' => 'nullable|array',
        ]);
        
        $classId = $request->class_id;
        $date = Carbon::parse($request->date);
        
        // For teachers, check if they are authorized to take attendance for this class
        if (Auth::user()->role === 'teacher') {
            $teacherId = Auth::user()->teacher->teacher_id;
            
            $isClassTeacher = Classes::where('class_id', $classId)
                ->where('class_teacher_id', $teacherId)
                ->exists();
            
            $isSubjectTeacher = \App\Models\ClassSubject::where('class_id', $classId)
                ->where('teacher_id', $teacherId)
                ->exists();
            
            if (!$isClassTeacher && !$isSubjectTeacher) {
                abort(403, 'You are not authorized to take attendance for this class.');
            }
        }
        
        // Process each student's attendance
        foreach ($request->status as $studentId => $status) {
            $remarks = $request->remarks[$studentId] ?? null;
            
            // Check if the student belongs to this class
            $student = Student::where('student_id', $studentId)
                ->where('current_class_id', $classId)
                ->first();
            
            if (!$student) {
                continue; // Skip if student doesn't belong to this class
            }
            
            // Try to find existing attendance record
            $attendance = StudentAttendance::where('student_id', $studentId)
                ->where('class_id', $classId)
                ->where('date', $date->format('Y-m-d'))
                ->first();
            
            if ($attendance) {
                // Update existing record
                $attendance->update([
                    'status' => $status,
                    'remarks' => $remarks,
                    'recorded_by' => Auth::id(),
                ]);
            } else {
                // Create new record
                StudentAttendance::create([
                    'student_id' => $studentId,
                    'class_id' => $classId,
                    'date' => $date->format('Y-m-d'),
                    'status' => $status,
                    'remarks' => $remarks,
                    'recorded_by' => Auth::id(),
                ]);
            }
        }
        
        return redirect()->route('student.attendance.index', ['class_id' => $classId, 'date' => $date->format('Y-m-d')])
            ->with('success', 'Attendance recorded successfully');
    }
    
    public function report(Request $request)
    {
        // Only admin and teachers can view attendance reports
        if (!in_array(Auth::user()->role, ['admin', 'teacher'])) {
            abort(403, 'Unauthorized action.');
        }
        
        $classId = $request->input('class_id');
        $month = $request->input('month') ? Carbon::parse($request->input('month')) : Carbon::today()->startOfMonth();
        $studentId = $request->input('student_id');
        
        // Get classes for filter dropdown
        $classes = Classes::where('academic_year_id', function ($query) {
            $query->select('academic_year_id')
                ->from('academic_years')
                ->where('is_current', true)
                ->limit(1);
        })->orderBy('class_name')->get();
        
        // If no class is selected, show class selection view
        if (!$classId) {
            return view('attendance.student.report_select_class', compact('classes', 'month'));
        }
        
        // For teachers, check if they are authorized to view this class
        if (Auth::user()->role === 'teacher') {
            $teacherId = Auth::user()->teacher->teacher_id;
            
            $isClassTeacher = Classes::where('class_id', $classId)
                ->where('class_teacher_id', $teacherId)
                ->exists();
            
            $isSubjectTeacher = \App\Models\ClassSubject::where('class_id', $classId)
                ->where('teacher_id', $teacherId)
                ->exists();
            
            if (!$isClassTeacher && !$isSubjectTeacher) {
                abort(403, 'You are not authorized to view attendance for this class.');
            }
        }
        
        $class = Classes::findOrFail($classId);
        
        // Get students for this class
        $students = Student::where('current_class_id', $classId)->orderBy('first_name')->get();
        
        if ($studentId) {
            // If a specific student is selected, show detailed report
            $student = Student::findOrFail($studentId);
            
            // Check if student belongs to the selected class
            if ($student->current_class_id != $classId) {
                return redirect()->route('student.attendance.report', ['class_id' => $classId, 'month' => $month->format('Y-m')])
                    ->with('error', 'Student does not belong to the selected class.');
            }
            
            // Get attendance records for the month
            $startDate = $month->copy()->startOfMonth();
            $endDate = $month->copy()->endOfMonth();
            
            $attendanceRecords = StudentAttendance::where('student_id', $studentId)
                ->where('class_id', $classId)
                ->whereBetween('date', [$startDate, $endDate])
                ->orderBy('date')
                ->get();
            
            // Calculate attendance statistics
            $totalDays = $attendanceRecords->count();
            $presentDays = $attendanceRecords->where('status', 'present')->count();
            $absentDays = $attendanceRecords->where('status', 'absent')->count();
            $lateDays = $attendanceRecords->where('status', 'late')->count();
            $excusedDays = $attendanceRecords->where('status', 'excused')->count();
            
            $attendancePercentage = $totalDays > 0 
                ? round((($presentDays + $lateDays) / $totalDays) * 100, 2)
                : 0;
            
            return view('attendance.student.report_detail', compact(
                'class', 'student', 'month', 'attendanceRecords',
                'totalDays', 'presentDays', 'absentDays', 'lateDays', 'excusedDays',
                'attendancePercentage'
            ));
        } else {
            // Show monthly summary for all students
            // Generate a calendar for the selected month
            $startDate = $month->copy()->startOfMonth();
            $endDate = $month->copy()->endOfMonth();
            $calendarDays = [];
            
            for ($day = $startDate->copy(); $day->lte($endDate); $day->addDay()) {
                // Skip weekends (optional)
                //if ($day->isWeekend()) continue;
                
                $calendarDays[] = $day->copy();
            }
            
            // Get attendance records for all students in the class for the month
            $attendanceRecords = StudentAttendance::where('class_id', $classId)
                ->whereBetween('date', [$startDate, $endDate])
                ->get()
                ->groupBy('student_id');
            
            return view('attendance.student.report_monthly', compact(
                'class', 'students', 'month', 'calendarDays', 'attendanceRecords'
            ));
        }
    }
    
    public function studentReport(Request $request)
    {
        // For students to view their own attendance
        if (Auth::user()->role !== 'student') {
            abort(403, 'Unauthorized action.');
        }
        
        $month = $request->input('month') ? Carbon::parse($request->input('month')) : Carbon::today()->startOfMonth();
        
        $student = Auth::user()->student;
        $class = Classes::find($student->current_class_id);
        
        if (!$class) {
            return redirect()->route('student.dashboard')
                ->with('error', 'You are not assigned to any class.');
        }
        
        // Get attendance records for the month
        $startDate = $month->copy()->startOfMonth();
        $endDate = $month->copy()->endOfMonth();
        
        $attendanceRecords = StudentAttendance::where('student_id', $student->student_id)
            ->where('class_id', $class->class_id)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date')
            ->get();
        
        // Calculate attendance statistics
        $totalDays = $attendanceRecords->count();
        $presentDays = $attendanceRecords->where('status', 'present')->count();
        $absentDays = $attendanceRecords->where('status', 'absent')->count();
        $lateDays = $attendanceRecords->where('status', 'late')->count();
        $excusedDays = $attendanceRecords->where('status', 'excused')->count();
        
        $attendancePercentage = $totalDays > 0 
            ? round((($presentDays + $lateDays) / $totalDays) * 100, 2)
            : 0;
        
        return view('attendance.student.student_report', compact(
            'class', 'student', 'month', 'attendanceRecords',
            'totalDays', 'presentDays', 'absentDays', 'lateDays', 'excusedDays',
            'attendancePercentage'
        ));
    }
}