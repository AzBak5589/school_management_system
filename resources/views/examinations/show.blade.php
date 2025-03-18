@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ $examination->exam_name }}</h1>
        <div>
            @if(Auth::user()->role === 'admin')
                <a href="{{ route('examinations.schedules', $examination->exam_id) }}" class="btn btn-primary">
                    <i class="fas fa-calendar-alt"></i> Manage Schedules
                </a>
                <a href="{{ route('examinations.edit', $examination->exam_id) }}" class="btn btn-info text-white">
                    <i class="fas fa-edit"></i> Edit
                </a>
            @endif
            <a href="{{ route('examinations.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Examinations
            </a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Examination Details</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Academic Year:</strong> {{ $examination->academicYear->year_name }}</p>
                    <p><strong>Description:</strong> {{ $examination->description ?: 'No description provided' }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Period:</strong> {{ $examination->start_date->format('M d, Y') }} - {{ $examination->end_date->format('M d, Y') }}</p>
                    <p><strong>Status:</strong>
                        @php
                            $now = \Carbon\Carbon::now();
                            if ($now->lt($examination->start_date)) {
                                echo '<span class="badge bg-info">Upcoming</span>';
                            } elseif ($now->gt($examination->end_date)) {
                                echo '<span class="badge bg-success">Completed</span>';
                            } else {
                                echo '<span class="badge bg-warning text-dark">In Progress</span>';
                            }
                        @endphp
                    </p>
                </div>
            </div>
        </div>
    </div>

    @if($schedulesByClass->count() > 0)
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Examination Schedule</h6>
            </div>
            <div class="card-body">
                @foreach($schedulesByClass as $classId => $classSchedules)
                    <div class="mb-4">
                        <h5>{{ $classSchedules->first()->class->class_name }} - {{ $classSchedules->first()->class->section }}</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Subject</th>
                                        <th>Room</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($classSchedules as $schedule)
                                        <tr>
                                            <td>{{ $schedule->exam_date->format('M d, Y') }}</td>
                                            <td>{{ $schedule->start_time->format('h:i A') }} - {{ $schedule->end_time->format('h:i A') }}</td>
                                            <td>{{ $schedule->subject->subject_name }}</td>
                                            <td>{{ $schedule->room ?: 'Not specified' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Examination Schedule</h6>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    No examination schedules have been created yet.
                    @if(Auth::user()->role === 'admin')
                        <a href="{{ route('examinations.schedules', $examination->exam_id) }}">Create schedules now</a>.
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
@endsection