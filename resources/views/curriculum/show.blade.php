@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ $curriculumItem->title }}</h1>
        <div>
            @if(Auth::user()->role === 'admin' || 
                (Auth::user()->role === 'teacher' && 
                (Auth::user()->teacher->teacher_id === $curriculumItem->classSubject->teacher_id || 
                Auth::id() === $curriculumItem->created_by)))
                <a href="{{ route('curriculum.edit', $curriculumItem->curriculum_id) }}" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Edit
                </a>
            @endif
            <a href="{{ route('curriculum.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Curriculum
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Curriculum Content</h6>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h4>Description</h4>
                        <div class="p-3 bg-light rounded">
                            {!! nl2br(e($curriculumItem->description)) ?: '<em>No description provided</em>' !!}
                        </div>
                    </div>
                    
                    @if($curriculumItem->resource_link)
                        <div class="mb-4">
                            <h4>Resources</h4>
                            <p>
                                <a href="{{ $curriculumItem->resource_link }}" target="_blank" class="btn btn-info text-white">
                                    <i class="fas fa-external-link-alt"></i> View Resource
                                </a>
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Details</h6>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><strong>Class:</strong></span>
                            <span>{{ $curriculumItem->classSubject->class->class_name }} - {{ $curriculumItem->classSubject->class->section }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><strong>Subject:</strong></span>
                            <span>{{ $curriculumItem->classSubject->subject->subject_name }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><strong>Teacher:</strong></span>
                            <span>{{ $curriculumItem->classSubject->teacher->full_name }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><strong>Created By:</strong></span>
                            <span>{{ $curriculumItem->creator->username }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><strong>Created On:</strong></span>
                            <span>{{ $curriculumItem->created_at->format('M d, Y') }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><strong>Last Updated:</strong></span>
                            <span>{{ $curriculumItem->updated_at->format('M d, Y') }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection