@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Subjects for {{ $class->class_name }} - {{ $class->section }}</h1>
        <div>
            <a href="{{ route('classes.subjects.create', $class->class_id) }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Assign Subject
            </a>
            <a href="{{ route('classes.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Classes
            </a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                Class: {{ $class->class_name }} - {{ $class->section }}
                ({{ $class->academicYear->year_name }})
                @if($class->academicYear->is_current)
                    <span class="badge bg-success">Current</span>
                @endif
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Subject Code</th>
                            <th>Subject Name</th>
                            <th>Teacher</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($classSubjects as $classSubject)
                            <tr>
                                <td>{{ $classSubject->subject->subject_code }}</td>
                                <td>{{ $classSubject->subject->subject_name }}</td>
                                <td>{{ $classSubject->teacher->full_name }}</td>
                                <td>
                                    <a href="{{ route('classes.subjects.edit', [$class->class_id, $classSubject->class_subject_id]) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('classes.subjects.destroy', [$class->class_id, $classSubject->class_subject_id]) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to remove this subject from the class?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">No subjects assigned to this class yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection