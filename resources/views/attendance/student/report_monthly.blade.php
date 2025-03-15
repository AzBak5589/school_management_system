@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Monthly Attendance Report</h1>
        <div>
            <a href="{{ route('student.attendance.index', ['class_id' => $class->class_id]) }}" class="btn btn-info text-white">
                <i class="fas fa-clipboard-check"></i> Take Attendance
            </a>
            <a href="{{ route('student.attendance.report') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Classes
            </a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                {{ $class->class_name }} - {{ $class->section }} | Attendance for {{ $month->format('F Y') }}
            </h6>
            <form action="{{ route('student.attendance.report') }}" method="GET" class="form-inline">
                <input type="hidden" name="class_id" value="{{ $class->class_id }}">
                <div class="input-group">
                    <input type="month" class="form-control" name="month" value="{{ $month->format('Y-m') }}">
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-calendar-day"></i> Change Month
                    </button>
                </div>
            </form>
        </div>
        <div class="card-body">
            @if($students->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-sm" width="100%" cellspacing="0">
                        <thead class="bg-light">
                            <tr>
                                <th>Student</th>
                                @foreach($calendarDays as $day)
                                    <th class="text-center {{ $day->isWeekend() ? 'bg-light text-muted' : '' }}">
                                        {{ $day->format('d') }}<br>
                                        <small>{{ $day->format('D') }}</small>
                                    </th>
                                @endforeach
                                <th class="text-center">Present</th>
                                <th class="text-center">Absent</th>
                                <th class="text-center">%</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $student)
                                @php
                                    $presentCount = 0;
                                    $absentCount = 0;
                                    $totalDays = 0;
                                    
                                    // Get student's attendance records
                                    $studentAttendance = $attendanceRecords[$student->student_id] ?? collect();
                                    
                                    // Prepare a lookup by date
                                    $attendanceLookup = [];
                                    foreach ($studentAttendance as $record) {
                                        $attendanceLookup[$record->date->format('Y-m-d')] = $record;
                                    }
                                @endphp
                                <tr>
                                    <td>
                                        <a href="{{ route('student.attendance.report', ['class_id' => $class->class_id, 'month' => $month->format('Y-m'), 'student_id' => $student->student_id]) }}">
                                            {{ $student->full_name }}
                                        </a>
                                    </td>
                                    @foreach($calendarDays as $day)
                                        @php
                                            $dayKey = $day->format('Y-m-d');
                                            $record = $attendanceLookup[$dayKey] ?? null;
                                            $status = $record ? $record->status : null;
                                            
                                            if ($record) {
                                                $totalDays++;
                                                if (in_array($status, ['present', 'late'])) {
                                                    $presentCount++;
                                                } elseif ($status === 'absent') {
                                                    $absentCount++;
                                                }
                                            }
                                        @endphp
                                        <td class="text-center {{ $day->isWeekend() ? 'bg-light' : '' }}">
                                            @if($record)
                                                @if($status === 'present')
                                                    <span class="badge bg-success">P</span>
                                                @elseif($status === 'absent')
                                                    <span class="badge bg-danger">A</span>
                                                @elseif($status === 'late')
                                                    <span class="badge bg-warning text-dark">L</span>
                                                @elseif($status === 'excused')
                                                    <span class="badge bg-info">E</span>
                                                @endif
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    @endforeach
                                    <td class="text-center">{{ $presentCount }}</td>
                                    <td class="text-center">{{ $absentCount }}</td>
                                    <td class="text-center">
                                        @if($totalDays > 0)
                                            {{ round(($presentCount / $totalDays) * 100) }}%
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info">
                    No students are enrolled in this class.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection