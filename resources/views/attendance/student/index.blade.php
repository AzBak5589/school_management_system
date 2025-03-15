@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Student Attendance</h1>
        <div>
            <a href="{{ route('student.attendance.report', ['class_id' => $class->class_id, 'month' => $date->format('Y-m')]) }}" class="btn btn-info text-white">
                <i class="fas fa-chart-bar"></i> View Reports
            </a>
            <a href="{{ route('student.attendance.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Classes
            </a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                {{ $class->class_name }} - {{ $class->section }} | Attendance for {{ $date->format('l, F d, Y') }}
            </h6>
            <form action="{{ route('student.attendance.index') }}" method="GET" class="form-inline">
                <input type="hidden" name="class_id" value="{{ $class->class_id }}">
                <div class="input-group">
                    <input type="date" class="form-control" name="date" value="{{ $date->format('Y-m-d') }}">
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-calendar-day"></i> Change Date
                    </button>
                </div>
            </form>
        </div>
        <div class="card-body">
            @if($students->count() > 0)
                <form action="{{ route('student.attendance.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="class_id" value="{{ $class->class_id }}">
                    <input type="hidden" name="date" value="{{ $date->format('Y-m-d') }}">
                    
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Admission Number</th>
                                    <th>Attendance Status</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($students as $student)
                                    <tr>
                                        <td>{{ $student->full_name }}</td>
                                        <td>{{ $student->admission_number }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <input type="radio" class="btn-check" name="status[{{ $student->student_id }}]" id="present-{{ $student->student_id }}" value="present" 
                                                    {{ isset($attendanceRecords[$student->student_id]) && $attendanceRecords[$student->student_id]->status === 'present' ? 'checked' : '' }}
                                                    {{ !isset($attendanceRecords[$student->student_id]) ? 'checked' : '' }}>
                                                <label class="btn btn-outline-success" for="present-{{ $student->student_id }}">Present</label>
                                                
                                                <input type="radio" class="btn-check" name="status[{{ $student->student_id }}]" id="absent-{{ $student->student_id }}" value="absent"
                                                    {{ isset($attendanceRecords[$student->student_id]) && $attendanceRecords[$student->student_id]->status === 'absent' ? 'checked' : '' }}>
                                                <label class="btn btn-outline-danger" for="absent-{{ $student->student_id }}">Absent</label>
                                                
                                                <input type="radio" class="btn-check" name="status[{{ $student->student_id }}]" id="late-{{ $student->student_id }}" value="late"
                                                    {{ isset($attendanceRecords[$student->student_id]) && $attendanceRecords[$student->student_id]->status === 'late' ? 'checked' : '' }}>
                                                <label class="btn btn-outline-warning" for="late-{{ $student->student_id }}">Late</label>
                                                
                                                <input type="radio" class="btn-check" name="status[{{ $student->student_id }}]" id="excused-{{ $student->student_id }}" value="excused"
                                                    {{ isset($attendanceRecords[$student->student_id]) && $attendanceRecords[$student->student_id]->status === 'excused' ? 'checked' : '' }}>
                                                <label class="btn btn-outline-info" for="excused-{{ $student->student_id }}">Excused</label>
                                            </div>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" name="remarks[{{ $student->student_id }}]" 
                                                value="{{ isset($attendanceRecords[$student->student_id]) ? $attendanceRecords[$student->student_id]->remarks : '' }}"
                                                placeholder="Optional remarks">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Attendance
                        </button>
                    </div>
                </form>
            @else
                <div class="alert alert-info">
                    No students are enrolled in this class.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection