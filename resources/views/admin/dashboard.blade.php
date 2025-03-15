@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Admin Dashboard</h1>
    </div>

    <!-- User Statistics Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Administrators</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalAdmins }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-shield fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="{{ route('admin.users', ['role' => 'admin']) }}" class="btn btn-primary btn-sm">
                        Manage Administrators
                    </a>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Teachers</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalTeachers }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chalkboard-teacher fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="{{ route('admin.users', ['role' => 'teacher']) }}" class="btn btn-success btn-sm">
                        Manage Teachers
                    </a>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Students</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalStudents }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-graduate fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="{{ route('admin.users', ['role' => 'student']) }}" class="btn btn-info btn-sm text-white">
                        Manage Students
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Academic Years Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Academic Years</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ \App\Models\AcademicYear::count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="{{ route('academic-years.index') }}" class="btn btn-warning btn-sm">
                        Manage Academic Years
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Second Row for Additional Stats -->
    <div class="row">
        <!-- Classes Card -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Classes</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ \App\Models\Classes::count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-school fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="{{ route('classes.index') }}" class="btn btn-primary btn-sm">
                        Manage Classes
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Subjects Card -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Subjects</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ \App\Models\Subject::count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-book fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="{{ route('subjects.index') }}" class="btn btn-success btn-sm">
                        Manage Subjects
                    </a>
                </div>
            </div>
        </div>
        <!-- Enrollments Card -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Current Students</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ \App\Models\Student::whereNotNull('current_class_id')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="#" class="btn btn-info btn-sm text-white">
                        View Enrolled Students
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Access Cards -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-block">
                                <i class="fas fa-user-plus"></i> Add New User
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('classes.create') }}" class="btn btn-success btn-block">
                                <i class="fas fa-plus-circle"></i> Create New Class
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('subjects.create') }}" class="btn btn-info text-white btn-block">
                                <i class="fas fa-book-medical"></i> Add New Subject
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('academic-years.create') }}" class="btn btn-warning btn-block">
                                <i class="fas fa-calendar-plus"></i> Add Academic Year
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                        <a href="{{ route('curriculum.create') }}" class="btn btn-success btn-block">
                            <i class="fas fa-book-medical"></i> Add Curriculum Item
                        </a>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('timetable.index') }}" class="btn btn-info text-white btn-block">
                            <i class="fas fa-calendar-plus"></i> Manage Timetables
                        </a>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
        
