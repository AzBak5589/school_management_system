<!-- resources/views/attendance/teacher/index.blade.php -->
@extends('layouts.app')

@section('title', 'Teacher Attendance')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Teacher Attendance</h3>
                    <div class="card-tools">
                        <a href="{{ route('teacher.attendance.report') }}" class="btn btn-info btn-sm">
                            <i class="fas fa-chart-bar"></i> Attendance Reports
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

                    <form action="{{ route('teacher.attendance.store') }}" method="POST">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="date">Date</label>
                                    <input type="date" name="date" id="date" class="form-control @error('date') is-invalid @enderror" value="{{ old('date', date('Y-m-d')) }}" required>
                                    @error('date')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Department</th>
                                        <th>Status</th>
                                        <th>Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($teachers as $teacher)
                                        <tr>
                                            <td>{{ $teacher->teacher_id }}</td>
                                            <td>{{ $teacher->user->name }}</td>
                                            <td>{{ $teacher->department }}</td>
                                            <td>
                                                <div class="form-group mb-0">
                                                    <div class="custom-control custom-radio custom-control-inline">
                                                        <input type="radio" id="present_{{ $teacher->teacher_id }}" name="attendance[{{ $teacher->teacher_id }}][status]" value="present" class="custom-control-input" {{ old("attendance.{$teacher->teacher_id}.status", $attendanceData[$teacher->teacher_id]['status'] ?? 'present') == 'present' ? 'checked' : '' }}>
                                                        <label class="custom-control-label" for="present_{{ $teacher->teacher_id }}">Present</label>
                                                    </div>
                                                    <div class="custom-control custom-radio custom-control-inline">
                                                        <input type="radio" id="absent_{{ $teacher->teacher_id }}" name="attendance[{{ $teacher->teacher_id }}][status]" value="absent" class="custom-control-input" {{ old("attendance.{$teacher->teacher_id}.status", $attendanceData[$teacher->teacher_id]['status'] ?? '') == 'absent' ? 'checked' : '' }}>
                                                        <label class="custom-control-label" for="absent_{{ $teacher->teacher_id }}">Absent</label>
                                                    </div>
                                                    <div class="custom-control custom-radio custom-control-inline">
                                                        <input type="radio" id="late_{{ $teacher->teacher_id }}" name="attendance[{{ $teacher->teacher_id }}][status]" value="late" class="custom-control-input" {{ old("attendance.{$teacher->teacher_id}.status", $attendanceData[$teacher->teacher_id]['status'] ?? '') == 'late' ? 'checked' : '' }}>
                                                        <label class="custom-control-label" for="late_{{ $teacher->teacher_id }}">Late</label>
                                                    </div>
                                                    <div class="custom-control custom-radio custom-control-inline">
                                                        <input type="radio" id="leave_{{ $teacher->teacher_id }}" name="attendance[{{ $teacher->teacher_id }}][status]" value="leave" class="custom-control-input" {{ old("attendance.{$teacher->teacher_id}.status", $attendanceData[$teacher->teacher_id]['status'] ?? '') == 'leave' ? 'checked' : '' }}>
                                                        <label class="custom-control-label" for="leave_{{ $teacher->teacher_id }}">Leave</label>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <input type="text" name="attendance[{{ $teacher->teacher_id }}][remarks]" class="form-control form-control-sm" value="{{ old("attendance.{$teacher->teacher_id}.remarks", $attendanceData[$teacher->teacher_id]['remarks'] ?? '') }}" placeholder="Remarks (optional)">
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">No teachers found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="form-group mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Attendance
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // When date changes, reload the page with the new date
        document.getElementById('date').addEventListener('change', function() {
            window.location.href = '{{ route("teacher.attendance.index") }}?date=' + this.value;
        });
    });
</script>
@endpush