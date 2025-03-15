@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Add New Curriculum Item</h1>
        <a href="{{ route('curriculum.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Curriculum
        </a>
    </div>
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Curriculum Information</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('curriculum.store') }}" method="POST">
                @csrf
                
                <div class="mb-3">
                    <label for="class_subject_id" class="form-label">Class Subject *</label>
                    <select class="form-select @error('class_subject_id') is-invalid @enderror" id="class_subject_id" name="class_subject_id" required>
                        <option value="">Select Class and Subject</option>
                        @foreach($classSubjects as $classSubject)
                            <option value="{{ $classSubject->class_subject_id }}" {{ old('class_subject_id') == $classSubject->class_subject_id ? 'selected' : '' }}>
                                {{ $classSubject->class->class_name }} - {{ $classSubject->class->section }} | {{ $classSubject->subject->subject_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('class_subject_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="title" class="form-label">Title *</label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="5">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="resource_link" class="form-label">Resource Link</label>
                    <input type="text" class="form-control @error('resource_link') is-invalid @enderror" id="resource_link" name="resource_link" value="{{ old('resource_link') }}">
                    @error('resource_link')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">URL to any additional resources (documents, videos, etc.)</small>
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="reset" class="btn btn-secondary me-md-2">Reset</button>
                    <button type="submit" class="btn btn-primary">Create Curriculum Item</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection