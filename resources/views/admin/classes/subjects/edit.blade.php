@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Subject Assignment</h1>
        <a href="{{ route('classes.subjects.index', $class->class_id) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Subjects
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
            <div class="mb-4">
                <h5>Subject: {{ $classSubject->subject->subject_name }} ({{ $classSubject->subject->subject_code }})</h5>
                <p>{{ $classSubject->subject->description }}</p>
            </div>
            
            <form action="{{ route('classes.subjects.update', [$class->class_id, $classSubject->class_subject_id]) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-3">
                    <label for="teacher_id" class="form-label">Teacher *</label>
                    <select class="form-select @error('teacher_id') is-invalid @enderror" id="teacher_id" name="teacher_id" required>
                        <option value="">Select Teacher</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->teacher_id }}" {{ old('teacher_id', $classSubject->teacher_id) == $teacher->teacher_id ? 'selected' : '' }}>
                                {{ $teacher->full_name }} ({{ $teacher->employee_id }})
                            </option>
                        @endforeach
                    </select>
                    @error('teacher_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="submit" class="btn btn-primary">Update Assignment</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection