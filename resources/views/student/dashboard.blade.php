@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Student Dashboard</h1>
    </div>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Welcome</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $student->full_name }}</div>
                            <div class="text-muted">{{ $student->admission_number }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-graduate fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- More cards will be added here as functionality is expanded -->
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Student Information</h6>
                    </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <tr>
                                <th width="200">Name</th>
                                <td>{{ $student->full_name }}</td>
                            </tr>
                            <tr>
                                <th>Admission Number</th>
                                <td>{{ $student->admission_number }}</td>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <td>{{ Auth::user()->email }}</td>
                            </tr>
                            <tr>
                                <th>Current Class</th>
                                <td>{{ $student->currentClass ? $student->currentClass->class_name . ' ' . $student->currentClass->section : 'Not assigned' }}</td>
                            </tr>
                            <tr>
                                <th>Date of Birth</th>
                                <td>{{ $student->date_of_birth->format('F d, Y') }}</td>
                            </tr>
                            <tr>
                                <th>Admission Date</th>
                                <td>{{ $student->admission_date->format('F d, Y') }}</td>
                            </tr>
                            <tr>
                                <th>Gender</th>
                                <td>{{ ucfirst($student->gender) }}</td>
                            </tr>
                            <tr>
                                <th>Phone</th>
                                <td>{{ $student->phone ?: 'Not specified' }}</td>
                            </tr>
                            <tr>
                                <th>Emergency Contact</th>
                                <td>
                                    @if($student->emergency_contact_name)
                                        {{ $student->emergency_contact_name }} 
                                        @if($student->emergency_contact_phone)
                                            ({{ $student->emergency_contact_phone }})
                                        @endif
                                    @else
                                        Not specified
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Fee Information Card -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Fee Information</h6>
                </div>
                <div class="card-body">
                    <p>Your fee information will appear here once implemented.</p>
                    <!-- Fee information will be added in Phase 5 -->
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance Summary Card -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Attendance Summary</h6>
                </div>
                <div class="card-body">
                    <p>Your attendance summary will appear here once implemented.</p>
                    <!-- Attendance information will be added in Phase 4 -->
                </div>
            </div>
        </div>
    </div>

    <!-- Upcoming Exams Card -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Upcoming Exams</h6>
                </div>
                <div class="card-body">
                    <p>Your upcoming exams will appear here once implemented.</p>
                    <!-- Exam information will be added in Phase 4 -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection