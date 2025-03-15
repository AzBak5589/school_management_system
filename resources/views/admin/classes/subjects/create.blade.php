@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Assign Subject to {{ $class->class_name }} - {{ $class->section }}</h1>
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
            <form action="{{ route('classes.subjects.store', $class->class_id) }}" method="POST">
                @csrf
                
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
                    @if(count($subjects) == 0)
                        <div class="form-text text-warning">
                            All subjects are already assigned to this class or no subjects are available.
                            <a href="{{ route('subjects.create') }}">Create a new subject</a>.
                        </div>
                    @endif
                </div>
                
                <div class="mb-3">
                    <label for="teacher_id" class="form-label">Teacher *</label>
                    <select class="form-select @error('teacher_id') is-invalid @enderror" id="teacher_id" name="teacher_id" required>
                        <option value="">Select Teacher</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->teacher_id }}" {{ old('teacher_id') == $teacher->teacher_id ? 'selected' : '' }}>
                                {{ $teacher->full_name }} ({{ $teacher->employee_id }})
                            </option>
                        @endforeach
                    </select>
                    @error('teacher_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="submit" class="btn btn-primary">Assign Subject</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection