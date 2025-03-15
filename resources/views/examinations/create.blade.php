@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Add New Examination</h1>
        <a href="{{ route('examinations.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Examinations
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Examination Information</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('examinations.store') }}" method="POST">
                @csrf
                
                <div class="mb-3">
                    <label for="academic_year_id" class="form-label">Academic Year *</label>
                    <select class="form-select @error('academic_year_id') is-invalid @enderror" id="academic_year_id" name="academic_year_id" required>
                        <option value="">Select Academic Year</option>
                        @foreach($academicYears as $year)
                            <option value="{{ $year->academic_year_id }}" {{ old('academic_year_id') == $year->academic_year_id ? 'selected' : '' }}>
                                {{ $year->year_name }} {{ $year->is_current ? '(Current)' : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('academic_year_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="exam_name" class="form-label">Examination Name *</label>
                    <input type="text" class="form-control @error('exam_name') is-invalid @enderror" id="exam_name" name="exam_name" value="{{ old('exam_name') }}" required>
                    @error('exam_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">Example: Midterm Examination, Final Examination, etc.</small>
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="start_date" class="form-label">Start Date *</label>
                        <input type="date" class="form-control @error('start_date') is-invalid @enderror" id="start_date" name="start_date" value="{{ old('start_date') }}" required>
                        @error('start_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="end_date" class="form-label">End Date *</label>
                        <input type="date" class="form-control @error('end_date') is-invalid @enderror" id="end_date" name="end_date" value="{{ old('end_date') }}" required>
                        @error('end_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="reset" class="btn btn-secondary me-md-2">Reset</button>
                    <button type="submit" class="btn btn-primary">Create Examination</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection