@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Class Timetables</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Select a Class</h6>
        </div>
        <div class="card-body">
            <div class="row">
                @foreach($classes as $class)
                    <div class="col-md-4 mb-4">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            {{ $class->academicYear->year_name }}
                                            @if($class->academicYear->is_current)
                                                <span class="badge bg-success">Current</span>
                                            @endif
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $class->class_name }} - {{ $class->section }}</div>
                                        <div class="text-muted small">
                                            Teacher: {{ $class->classTeacher ? $class->classTeacher->full_name : 'Not assigned' }}
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-calendar fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent">
                                <a href="{{ route('timetable.index', ['class_id' => $class->class_id]) }}" class="btn btn-primary btn-sm btn-block">
                                    View Timetable
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection