@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Examination Schedules</h1>
        <div>
            <a href="{{ route('examinations.show', $examination->exam_id) }}" class="btn btn-info text-white">
                <i class="fas fa-eye"></i> View Details
            </a>
            <a href="{{ route('examinations.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Examinations
            </a>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ $examination->exam_name }}</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Academic Year:</strong> {{ $examination->academicYear->year_name }}</p>
                            <p><strong>Description:</strong> {{ $examination->description ?: 'No description' }}</p>
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
        </div>
    </div>

    <div class="row">
        <div class="col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Add Exam Schedule</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('examinations.schedules.store', $examination->exam_id) }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="class_id" class="form-label">Class *</label>
                            <select class="form-select @error('class_id') is-invalid @enderror" id="class_id" name="class_id" required>
                                <option value="">Select Class</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->class_id }}" {{ old('class_id') == $class->class_id ? 'selected' : '' }}>
                                        {{ $class->class_name }} - {{ $class->section }}
                                    </option>
                                @endforeach
                            </select>
                            @error('class_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="subject_id" class="form-label">Subject *</label>
                            <select class="form-select @error('subject_id') is-invalid @enderror" id="subject_id" name="subject_id" required>
                                <option value="">Select Subject</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->subject_id }}" {{ old('subject_id') == $subject->subject_id ? 'selected' : '' }}>
                                        {{ $subject->subject_name }} ({{ $subject->subject_code }})
                                    </option>
                                @endforeach
                            </select>
                            @error('subject_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="exam_date" class="form-label">Exam Date *</label>
                            <input type="date" class="form-control @error('exam_date') is-invalid @enderror" id="exam_date" name="exam_date" value="{{ old('exam_date') }}" 
                                min="{{ $examination->start_date->format('Y-m-d') }}" 
                                max="{{ $examination->end_date->format('Y-m-d') }}" required>
                            @error('exam_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="start_time" class="form-label">Start Time *</label>
                                <input type="time" class="form-control @error('start_time') is-invalid @enderror" id="start_time" name="start_time" value="{{ old('start_time') }}" required>
                                @error('start_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="end_time" class="form-label">End Time *</label>
                                <input type="time" class="form-control @error('end_time') is-invalid @enderror" id="end_time" name="end_time" value="{{ old('end_time') }}" required>
                                @error('end_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="room" class="form-label">Room</label>
                            <input type="text" class="form-control @error('room') is-invalid @enderror" id="room" name="room" value="{{ old('room') }}">
                            @error('room')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Add Schedule</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Existing Schedules</h6>
                </div>
                <div class="card-body">
                    @if($schedules->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Class</th>
                                        <th>Subject</th>
                                        <th>Date & Time</th>
                                        <th>Room</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($schedules as $schedule)
                                        <tr>
                                            <td>{{ $schedule->class->class_name }} - {{ $schedule->class->section }}</td>
                                            <td>{{ $schedule->subject->subject_name }}</td>
                                            <td>
                                                {{ $schedule->exam_date->format('M d, Y') }}<br>
                                                <small>{{ $schedule->start_time->format('h:i A') }} - {{ $schedule->end_time->format('h:i A') }}</small>
                                            </td>
                                            <td>{{ $schedule->room ?: 'Not specified' }}</td>
                                            <td>
                                                <a href="{{ route('examinations.schedules.edit', [$examination->exam_id, $schedule->schedule_id]) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('examinations.schedules.destroy', [$examination->exam_id, $schedule->schedule_id]) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this schedule?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            No schedules have been created for this examination yet.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection