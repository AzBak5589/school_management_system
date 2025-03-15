@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Teacher Dashboard</h1>
    </div>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Welcome</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $teacher->full_name }}</div>
                            <div class="text-muted">{{ $teacher->employee_id }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user fa-2x text-gray-300"></i>
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
                    <h6 class="m-0 font-weight-bold text-primary">Teacher Information</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <tr>
                                <th width="200">Name</th>
                                <td>{{ $teacher->full_name }}</td>
                            </tr>
                            <tr>
                                <th>Employee ID</th>
                                <td>{{ $teacher->employee_id }}</td>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <td>{{ Auth::user()->email }}</td>
                            </tr>
                            <tr>
                                <th>Qualification</th>
                                <td>{{ $teacher->qualification ?: 'Not specified' }}</td>
                            </tr>
                            <tr>
                                <th>Join Date</th>
                                <td>{{ $teacher->join_date->format('F d, Y') }}</td>
                            </tr>
                            <tr>
                                <th>Phone</th>
                                <td>{{ $teacher->phone ?: 'Not specified' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection