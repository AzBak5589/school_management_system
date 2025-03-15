@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Subject</h1>
        <a href="{{ route('subjects.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Subjects
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Subject Information</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('subjects.update', $subject->subject_id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-3">
                    <label for="subject_code" class="form-label">Subject Code *</label>
                    <input type="text" class="form-control @error('subject_code') is-invalid @enderror" id="subject_code" name="subject_code" value="{{ old('subject_code', $subject->subject_code) }}" required>
                    @error('subject_code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="subject_name" class="form-label">Subject Name *</label>
                    <input type="text" class="form-control @error('subject_name') is-invalid @enderror" id="subject_name" name="subject_name" value="{{ old('subject_name', $subject->subject_name) }}" required>
                    @error('subject_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4">{{ old('description', $subject->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="submit" class="btn btn-primary">Update Subject</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection