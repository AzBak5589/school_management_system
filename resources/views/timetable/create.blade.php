@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Add Class Period to Timetable</h1>
        <a href="{{ route('timetable.index', ['class_id' => $class->class_id]) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Timetable
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                Class: {{ $class->class_name }} - {{ $class->section }}
                ({{ $class->academicYear->year_name }})
            </h6>
        </div>
        <div class="card-body">
            <form action="{{ route('timetable.store', $class->class_id) }}" method="POST">
                @csrf
                
                <div class="mb-3">
                    <label for="day_of_week" class="form-label">Day of Week *</label>
                    <select class="form-select @error('day_of_week') is-invalid @enderror" id="day_of_week" name="day_of_week" required>
                        <option value="">Select Day</option>
                        <option value="1" {{ old('day_of_week') == 1 ? 'selected' : '' }}>Monday</option>
                        <option value="2" {{ old('day_of_week') == 2 ? 'selected' : '' }}>Tuesday</option>
                        <option value="3" {{ old('day_of_week') == 3 ? 'selected' : '' }}>Wednesday</option>
                        <option value="4" {{ old('day_of_week') == 4 ? 'selected' : '' }}>Thursday</option>
                        <option value="5" {{ old('day_of_week') == 5 ? 'selected' : '' }}>Friday</option>
                        <option value="6" {{ old('day_of_week') == 6 ? 'selected' : '' }}>Saturday</option>
                        <option value="7" {{ old('day_of_week') == 7 ? 'selected' : '' }}>Sunday</option>
                    </select>
                    @error('day_of_week')
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
                    <label for="class_subject_id" class="form-label">Subject and Teacher *</label>
                    <select class="form-select @error('class_subject_id') is-invalid @enderror" id="class_subject_id" name="class_subject_id" required>
                        <option value="">Select Subject</option>
                        @foreach($classSubjects as $classSubject)
                            <option value="{{ $classSubject->class_subject_id }}" {{ old('class_subject_id') == $classSubject->class_subject_id ? 'selected' : '' }}>
                                {{ $classSubject->subject->subject_name }} ({{ $classSubject->subject->subject_code }}) - {{ $classSubject->teacher->full_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('class_subject_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="room" class="form-label">Room</label>
                    <input type="text" class="form-control @error('room') is-invalid @enderror" id="room" name="room" value="{{ old('room') }}">
                    @error('room')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="reset" class="btn btn-secondary me-md-2">Reset</button>
                    <button type="submit" class="btn btn-primary">Add to Timetable</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection