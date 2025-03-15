@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Students in {{ $class->class_name }} - {{ $class->section }}</h1>
        <a href="{{ route('classes.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Classes
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                Class Details: {{ $class->class_name }} - {{ $class->section }} 
                ({{ $class->academicYear->year_name }})
                @if($class->academicYear->is_current)
                    <span class="badge bg-success">Current</span>
                @endif
            </h6>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <p><strong>Academic Year:</strong> {{ $class->academicYear->year_name }}</p>
                    <p><strong>Class Capacity:</strong> {{ $class->capacity }} students</p>
                    <p><strong>Current Enrollment:</strong> {{ $students->total() }} students</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Class Teacher:</strong> 
                        @if($class->classTeacher)
                            {{ $class->classTeacher->full_name }}
                        @else
                            <em>Not assigned</em>
                        @endif
                    </p>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Admission Number</th>
                            <th>Gender</th>
                            <th>Contact</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $student)
                            <tr>
                                <td>{{ $student->student_id }}</td>
                                <td>{{ $student->full_name }}</td>
                                <td>{{ $student->admission_number }}</td>
                                <td>{{ ucfirst($student->gender) }}</td>
                                <td>{{ $student->phone ?: 'N/A' }}</td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-info text-white">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No students enrolled in this class yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                {{ $students->links() }}
            </div>
        </div>
    </div>
</div>
@endsection