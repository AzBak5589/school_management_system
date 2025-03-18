<!-- resources/views/attendance/teacher/report.blade.php -->
@extends('layouts.app')

@section('title', 'Teacher Attendance Report')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Teacher Attendance Report</h3>
                    <div class="card-tools">
                        <a href="{{ route('teacher.attendance.index') }}" class="btn btn-default btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Attendance
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <form action="{{ route('teacher.attendance.report') }}" method="GET" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="teacher_id">Teacher</label>
                                    <select name="teacher_id" id="teacher_id" class="form-control">
                                        <option value="">All Teachers</option>
                                        @foreach($teachers as $teacher)
                                            <option value="{{ $teacher->teacher_id }}" {{ request('teacher_id') == $teacher->teacher_id ? 'selected' : '' }}>
                                                {{ $teacher->user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="">All Statuses</option>
                                        <option value="present" {{ request('status') == 'present' ? 'selected' : '' }}>Present</option>
                                        <option value="absent" {{ request('status') == 'absent' ? 'selected' : '' }}>Absent</option>
                                        <option value="late" {{ request('status') == 'late' ? 'selected' : '' }}>Late</option>
                                        <option value="leave" {{ request('status') == 'leave' ? 'selected' : '' }}>Leave</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="start_date">From Date</label>
                                    <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date', date('Y-m-01')) }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="end_date">To Date</label>
                                    <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date', date('Y-m-t')) }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="btn btn-primary form-control">
                                        <i class="fas fa-search"></i> Filter
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    @if($selectedTeacher)
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Attendance Summary for {{ $selectedTeacher->user->name }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-3 text-center">
                                                <div class="info-box bg-success">
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Present</span>
                                                        <span class="info-box-number">{{ $summary['present'] ?? 0 }}</span>
                                                        <div class="progress">
                                                            <div class="progress-bar" style="width: {{ $summary['present_percentage'] ?? 0 }}%"></div>
                                                        </div>
                                                        <span class="progress-description">
                                                            {{ $summary['present_percentage'] ?? 0 }}% of total
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 text-center">
                                                <div class="info-box bg-danger">
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Absent</span>
                                                        <span class="info-box-number">{{ $summary['absent'] ?? 0 }}</span>
                                                        <div class="progress">
                                                            <div class="progress-bar" style="width: {{ $summary['absent_percentage'] ?? 0 }}%"></div>
                                                        </div>
                                                        <span class="progress-description">
                                                            {{ $summary['absent_percentage'] ?? 0 }}% of total
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 text-center">
                                                <div class="info-box bg-warning">
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Late</span>
                                                        <span class="info-box-number">{{ $summary['late'] ?? 0 }}</span>
                                                        <div class="progress">
                                                            <div class="progress-bar" style="width: {{ $summary['late_percentage'] ?? 0 }}%"></div>
                                                        </div>
                                                        <span class="progress-description">
                                                            {{ $summary['late_percentage'] ?? 0 }}% of total
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 text-center">
                                                <div class="info-box bg-info">
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Leave</span>
                                                        <span class="info-box-number">{{ $summary['leave'] ?? 0 }}</span>
                                                        <div class="progress">
                                                            <div class="progress-bar" style="width: {{ $summary['leave_percentage'] ?? 0 }}%"></div>
                                                        </div>
                                                        <span class="progress-description">
                                                            {{ $summary['leave_percentage'] ?? 0 }}% of total
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @elseif(!request('teacher_id'))
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Overall Attendance Summary</h5>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="attendanceChart" height="100"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Attendance Records</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Teacher</th>
                                            <th>Department</th>
                                            <th>Status</th>
                                            <th>Remarks</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($attendanceRecords as $record)
                                            <tr>
                                                <td>{{ $record->date->format('d-m-Y') }}</td>
                                                <td>{{ $record->teacher->user->name }}</td>
                                                <td>{{ $record->teacher->department }}</td>
                                                <td>
                                                    @if($record->status == 'present')
                                                        <span class="badge badge-success">Present</span>
                                                    @elseif($record->status == 'absent')
                                                        <span class="badge badge-danger">Absent</span>
                                                    @elseif($record->status == 'late')
                                                        <span class="badge badge-warning">Late</span>
                                                    @elseif($record->status == 'leave')
                                                        <span class="badge badge-info">Leave</span>
                                                    @endif
                                                </td>
                                                <td>{{ $record->remarks }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">No attendance records found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="mt-3">
                                {{ $attendanceRecords->appends(request()->except('page'))->links() }}
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <a href="{{ route('teacher.attendance.export', request()->all()) }}" class="btn btn-success">
                            <i class="fas fa-file-excel"></i> Export to Excel
                        </a>
                        <a href="{{ route('teacher.attendance.print', request()->all()) }}" class="btn btn-primary" target="_blank">
                            <i class="fas fa-print"></i> Print Report
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if(!request('teacher_id'))
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var ctx = document.getElementById('attendanceChart').getContext('2d');
        var attendanceChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json(array_keys($overallSummary)),
                datasets: [{
                    label: 'Present',
                    data: @json(array_map(function($teacher) { return $teacher['present'] ?? 0; }, $overallSummary)),
                    backgroundColor: 'rgba(40, 167, 69, 0.7)',
                    borderColor: 'rgba(40, 167, 69, 1)',
                    borderWidth: 1
                }, {
                    label: 'Absent',
                    data: @json(array_map(function($teacher) { return $teacher['absent'] ?? 0; }, $overallSummary)),
                    backgroundColor: 'rgba(220, 53, 69, 0.7)',
                    borderColor: 'rgba(220, 53, 69, 1)',
                    borderWidth: 1
                }, {
                    label: 'Late',
                    data: @json(array_map(function($teacher) { return $teacher['late'] ?? 0; }, $overallSummary)),
                    backgroundColor: 'rgba(255, 193, 7, 0.7)',
                    borderColor: 'rgba(255, 193, 7, 1)',
                    borderWidth: 1
                }, {
                    label: 'Leave',
                    data: @json(array_map(function($teacher) { return $teacher['leave'] ?? 0; }, $overallSummary)),
                    backgroundColor: 'rgba(23, 162, 184, 0.7)',
                    borderColor: 'rgba(23, 162, 184, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        stacked: false
                    },
                    y: {
                        stacked: false,
                        beginAtZero: true
                    }
                }
            }
        });
    });
</script>
@endpush
@endif
@endsection