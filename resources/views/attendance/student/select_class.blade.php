@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Student Attendance</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Select a Class</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('student.attendance.index') }}" method="GET" class="mb-4">
                <div class="row align-items-end">
                    <div class="col-md-5 mb-3">
                        <label for="class_id" class="form-label">Class</label>
                        <select class="form-select" id="class_id" name="class_id" required>
                            <option value="">Select Class</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->class_id }}">
                                    {{ $class->class_name }} - {{ $class->section }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-5 mb-3">
                        <label for="date" class="form-label">Date</label>
                        <input type="date" class="form-control" id="date" name="date" value="{{ $date->format('Y-m-d') }}">
                    </div>
                    
                    <div class="col-md-2 mb-3">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-check-circle"></i> Take Attendance
                        </button>
                    </div>
                </div>
            </form>
            
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Please select a class and date to take attendance.
            </div>
        </div>
    </div>
</div>
@endsection