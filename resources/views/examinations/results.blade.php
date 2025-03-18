@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Examination Results: {{ $examination->exam_name }}</h1>
        <div>
            <a href="{{ route('examinations.show', $examination->exam_id) }}" class="btn btn-info text-white">
                <i class="fas fa-eye"></i> View Details
            </a>
            <a href="{{ route('examinations.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Examinations
            </a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                Select Class to Manage Results
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Academic Year:</strong> {{ $examination->academicYear->year_name }}</p>
                    <p><strong>Exam Period:</strong> {{ $examination->start_date->format('M d, Y') }} - {{ $examination->end_date->format('M d, Y') }}</p>
                </div>
                <div class="col-md-6">
                    @php
                        $now = \Carbon\Carbon::now();
                        $status = '';
                        if ($now->lt($examination->start_date)) {
                            $status = '<span class="badge bg-info">Upcoming</span>';
                        } elseif ($now->gt($examination->end_date)) {
                            $status = '<span class="badge bg-success">Completed</span>';
                        } else {
                            $status = '<span class="badge bg-warning text-dark">In Progress</span>';
                        }
                    @endphp
                    <p><strong>Status:</strong> {!! $status !!}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Classes</h6>
        </div>
        <div class="card-body">
            @if($classes->count() > 0)
                <div class="row">
                    @foreach($classes as $class)
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $class->class_name }} - {{ $class->section }}</div>
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Capacity: {{ $class->capacity }} students
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <a href="{{ route('examinations.class_results', [$examination->exam_id, $class->class_id]) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-clipboard-list fa-2x"></i> Manage Results
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="alert alert-info">
                    No classes found for this academic year.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection