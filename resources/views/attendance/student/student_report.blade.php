@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">My Attendance</h1>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Student Information</h6>
                </div>
                <div class="card-body">
                    <h5 class="font-weight-bold">{{ $student->full_name }}</h5>
                    <p><strong>Admission Number:</strong> {{ $student->admission_number }}</p>
                    <p><strong>Class:</strong> {{ $class->class_name }} - {{ $class->section }}</p>
                    <p><strong>Month:</strong> {{ $month->format('F Y') }}</p>
                    
                    <div class="text-center mt-4">
                        <div class="row">
                            <div class="col-6">
                                <div class="h4 mb-0 font-weight-bold text-gray-800">{{ $totalDays }}</div>
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total Days
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="h4 mb-0 font-weight-bold text-gray-800">{{ $attendancePercentage }}%</div>
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Attendance
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-center mt-4">
                        <form action="{{ route('student.attendance.my-report') }}" method="GET" class="form-inline justify-content-center">
                            <div class="input-group">
                                <input type="month" class="form-control" name="month" value="{{ $month->format('Y-m') }}">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-calendar-day"></i> Change Month
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Attendance Details</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-3 text-center">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body py-2">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="h5 mb-0 font-weight-bold text-success">{{ $presentDays }}</div>
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Present
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-check fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 text-center">
                            <div class="card border-left-danger shadow h-100 py-2">
                                <div class="card-body py-2">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="h5 mb-0 font-weight-bold text-danger">{{ $absentDays }}</div>
                                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                                Absent
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-times fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 text-center">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body py-2">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="h5 mb-0 font-weight-bold text-warning">{{ $lateDays }}</div>
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                Late
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 text-center">
                            <div class="card border-left-info shadow h-100 py-2">
                                <div class="card-body py-2">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="h5 mb-0 font-weight-bold text-info">{{ $excusedDays }}</div>
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                Excused
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-calendar-minus fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    @if($attendanceRecords->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Day</th>
                                        <th>Status</th>
                                        <th>Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($attendanceRecords as $record)
                                        <tr>
                                            <td>{{ $record->date->format('d/m/Y') }}</td>
                                            <td>{{ $record->date->format('l') }}</td>
                                            <td>
                                                @if($record->status === 'present')
                                                    <span class="badge bg-success">Present</span>
                                                @elseif($record->status === 'absent')
                                                    <span class="badge bg-danger">Absent</span>
                                                @elseif($record->status === 'late')
                                                    <span class="badge bg-warning text-dark">Late</span>
                                                @elseif($record->status === 'excused')
                                                    <span class="badge bg-info">Excused</span>
                                                @endif
                                            </td>
                                            <td>{{ $record->remarks ?: 'No remarks' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            No attendance records found for you in {{ $month->format('F Y') }}.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection