@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Academic Year</h1>
        <a href="{{ route('academic-years.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Academic Years
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Academic Year Information</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('academic-years.update', $academicYear->academic_year_id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-3">
                    <label for="year_name" class="form-label">Year Name *</label>
                    <input type="text" class="form-control @error('year_name') is-invalid @enderror" id="year_name" name="year_name" value="{{ old('year_name', $academicYear->year_name) }}" required>
                    @error('year_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="start_date" class="form-label">Start Date *</label>
                        <input type="date" class="form-control @error('start_date') is-invalid @enderror" id="start_date" name="start_date" value="{{ old('start_date', $academicYear->start_date->format('Y-m-d')) }}" required>
                        @error('start_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="end_date" class="form-label">End Date *</label>
                        <input type="date" class="form-control @error('end_date') is-invalid @enderror" id="end_date" name="end_date" value="{{ old('end_date', $academicYear->end_date->format('Y-m-d')) }}" required>
                        @error('end_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="is_current" name="is_current" value="1" {{ old('is_current', $academicYear->is_current) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_current">Set as Current Academic Year</label>
                    <div class="form-text">If checked, this will be set as the current academic year and any other current academic year will be marked as inactive.</div>
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="submit" class="btn btn-primary">Update Academic Year</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection