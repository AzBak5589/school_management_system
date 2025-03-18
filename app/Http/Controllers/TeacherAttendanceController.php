<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Teacher;
use App\Models\TeacherAttendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TeacherAttendanceController extends Controller
{
    /**
     * Display the teacher attendance page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Only admin can access teacher attendance
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        // Get the date from request or use today
        $date = $request->date ? Carbon::parse($request->date) : Carbon::today();
        
        // Get all teachers
        $teachers = Teacher::with('user')->get();
        
        // Get existing attendance data for the selected date
        $attendanceData = [];
        $existingRecords = TeacherAttendance::where('date', $date->format('Y-m-d'))->get();
        
        foreach ($existingRecords as $record) {
            $attendanceData[$record->teacher_id] = [
                'status' => $record->status,
                'remarks' => $record->remarks
            ];
        }
        
        return view('attendance.teacher.index', compact('teachers', 'date', 'attendanceData'));
    }
    
    /**
     * Store teacher attendance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Only admin can store teacher attendance
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $request->validate([
            'date' => 'required|date',
            'attendance' => 'required|array',
            'attendance.*.status' => 'required|in:present,absent,late,leave',
            'attendance.*.remarks' => 'nullable|string|max:255',
        ]);
        
        $attendanceDate = $request->date;
        $attendanceData = $request->attendance;
        
        try {
            DB::beginTransaction();
            
            // Delete existing attendance for this date
            TeacherAttendance::where('date', $attendanceDate)->delete();
            
            // Insert new attendance records
            foreach ($attendanceData as $teacherId => $data) {
                TeacherAttendance::create([
                    'teacher_id' => $teacherId,
                    'date' => $attendanceDate,
                    'status' => $data['status'],
                    'remarks' => $data['remarks'] ?? null,
                    'recorded_by' => Auth::id(),
                ]);
            }
            
            DB::commit();
            
            return redirect()->route('teacher.attendance.index', ['date' => $attendanceDate])
                ->with('success', 'Teacher attendance has been saved successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'An error occurred while saving attendance: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Display the teacher attendance report.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function report(Request $request)
    {
        // Only admin can view attendance reports
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $teacherId = $request->teacher_id;
        $status = $request->status;
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now()->endOfMonth();
        
        // Get all teachers for dropdown
        $teachers = Teacher::with('user')->get();
        
        // Build the query
        $query = TeacherAttendance::with('teacher.user')
                                ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
        
        // Apply filters
        if ($teacherId) {
            $query->where('teacher_id', $teacherId);
            $selectedTeacher = Teacher::with('user')->find($teacherId);
        } else {
            $selectedTeacher = null;
        }
        
        if ($status) {
            $query->where('status', $status);
        }
        
        // Get attendance records
        $attendanceRecords = $query->orderBy('date', 'desc')
                                 ->paginate(20);
        
        // Calculate summary statistics if a teacher is selected
        $summary = [];
        
        if ($teacherId) {
            $totalDays = TeacherAttendance::where('teacher_id', $teacherId)
                                       ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                                       ->count();
            
            if ($totalDays > 0) {
                $statuses = ['present', 'absent', 'late', 'leave'];
                
                foreach ($statuses as $statusType) {
                    $count = TeacherAttendance::where('teacher_id', $teacherId)
                                           ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                                           ->where('status', $statusType)
                                           ->count();
                    
                    $summary[$statusType] = $count;
                    $summary["{$statusType}_percentage"] = round(($count / $totalDays) * 100, 1);
                }
            }
        }
        
        // Generate overall summary if no teacher is selected
        $overallSummary = [];
        
        if (!$teacherId) {
            $teachersList = Teacher::with('user')->get();
            
            foreach ($teachersList as $teacher) {
                $teacherName = $teacher->user->name;
                $overallSummary[$teacherName] = [
                    'department' => $teacher->department,
                ];
                
                $statuses = ['present', 'absent', 'late', 'leave'];
                
                foreach ($statuses as $statusType) {
                    $count = TeacherAttendance::where('teacher_id', $teacher->teacher_id)
                                           ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                                           ->where('status', $statusType)
                                           ->count();
                    
                    $overallSummary[$teacherName][$statusType] = $count;
                }
            }
        }
        
        return view('attendance.teacher.report', compact(
            'teachers', 
            'attendanceRecords', 
            'teacherId', 
            'selectedTeacher', 
            'status', 
            'startDate', 
            'endDate', 
            'summary',
            'overallSummary'
        ));
    }
    
    /**
     * Print the teacher attendance report.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function print(Request $request)
    {
        // Only admin can print attendance reports
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $teacherId = $request->teacher_id;
        $status = $request->status;
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now()->endOfMonth();
        
        // Build the query
        $query = TeacherAttendance::with('teacher.user')
                                ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
        
        // Apply filters
        if ($teacherId) {
            $query->where('teacher_id', $teacherId);
            $selectedTeacher = Teacher::with('user')->find($teacherId);
        } else {
            $selectedTeacher = null;
        }
        
        if ($status) {
            $query->where('status', $status);
        }
        
        // Get all attendance records for print (no pagination)
        $attendanceRecords = $query->orderBy('date', 'desc')->get();
        
        // Calculate summary statistics if a teacher is selected
        $summary = [];
        
        if ($teacherId) {
            $totalDays = TeacherAttendance::where('teacher_id', $teacherId)
                                       ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                                       ->count();
            
            if ($totalDays > 0) {
                $statuses = ['present', 'absent', 'late', 'leave'];
                
                foreach ($statuses as $statusType) {
                    $count = TeacherAttendance::where('teacher_id', $teacherId)
                                           ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                                           ->where('status', $statusType)
                                           ->count();
                    
                    $summary[$statusType] = $count;
                    $summary["{$statusType}_percentage"] = round(($count / $totalDays) * 100, 1);
                }
            }
        }
        
        // Generate overall summary if no teacher is selected
        $overallSummary = [];
        
        if (!$teacherId) {
            $teachersList = Teacher::with('user')->get();
            
            foreach ($teachersList as $teacher) {
                $teacherName = $teacher->user->name;
                $overallSummary[$teacherName] = [
                    'department' => $teacher->department,
                ];
                
                $statuses = ['present', 'absent', 'late', 'leave'];
                
                foreach ($statuses as $statusType) {
                    $count = TeacherAttendance::where('teacher_id', $teacher->teacher_id)
                                           ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                                           ->where('status', $statusType)
                                           ->count();
                    
                    $overallSummary[$teacherName][$statusType] = $count;
                }
            }
        }
        
        return view('attendance.teacher.print', compact(
            'attendanceRecords', 
            'teacherId', 
            'selectedTeacher', 
            'status', 
            'startDate', 
            'endDate', 
            'summary',
            'overallSummary'
        ));
    }
    
    /**
     * Export the teacher attendance report to Excel.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function export(Request $request)
    {
        // Only admin can export attendance reports
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        // This method would typically use a package like Maatwebsite Excel
        // For this implementation, we'll redirect with a message
        return redirect()->route('teacher.attendance.report', $request->all())
            ->with('info', 'Export functionality will be implemented soon.');
    }
}