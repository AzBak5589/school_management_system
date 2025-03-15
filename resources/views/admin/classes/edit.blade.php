@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Class</h1>
        <a href="{{ route('classes.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Classes
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Class Information</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('classes.update', $class->class_id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-3">
                    <label for="academic_year_id" class="form-label">Academic Year *</label>
                    <select class="form-select @error('academic_year_id') is-invalid @enderror" id="academic_year_id" name="academic_year_id" required>
                        <option value="">Select Academic Year</option>
                        @foreach($academicYears as $year)
                            <option value="{{ $year->academic_year_id }}" {{ old('academic_year_id', $class->academic_year_id) == $year->academic_year_id ? 'selected' : '' }}>
                                {{ $year->year_name }} {{ $year->is_current ? '(Current)' : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('academic_year_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-8">
                        <label for="class_name" class="form-label">Class Name *</label>
                        <input type="text" class="form-control @error('class_name') is-invalid @enderror" id="class_name" name="class_name" value="{{ old('class_name', $class->class_name) }}" required>
                        @error('class_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-4">
                        <label for="section" class="form-label">Section *</label>
                        <input type="text" class="form-control @error('section') is-invalid @enderror" id="section" name="section" value="{{ old('section', $class->section) }}" required>
                        @error('section')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="capacity" class="form-label">Capacity *</label>
                    <input type="number" class="form-control @error('capacity') is-invalid @enderror" id="capacity" name="capacity" value="{{ old('capacity', $class->capacity) }}" min="1" max="100" required>
                    @error('capacity')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">Maximum number of students that can be enrolled in this class.</small>
                </div>
                
                <div class="mb-3">
                    <label for="class_teacher_id" class="form-label">Class Teacher</label>
                    <select class="form-select @error('class_teacher_id') is-invalid @enderror" id="class_teacher_id" name="class_teacher_id">
                        <option value="">Select Class Teacher (Optional)</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->teacher_id }}" {{ old('class_teacher_id', $class->class_teacher_id) == $teacher->teacher_id ? 'selected' : '' }}>
                                {{ $teacher->full_name }} ({{ $teacher->employee_id }})
                            </option>
                        @endforeach
                    </select>
                    @error('class_teacher_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="submit" class="btn btn-primary">Update Class</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection