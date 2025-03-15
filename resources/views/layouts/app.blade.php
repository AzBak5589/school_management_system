<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Management System</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">
    <style>
        .sidebar {
            min-height: calc(100vh - 56px);
            background-color: #f8f9fa;
        }
        .content {
            padding: 20px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">School Management System</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Login</a>
                        </li>
                    @else
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                {{ Auth::user()->username }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ route('profile.show') }}">My Profile</a></li>
                                <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Edit Profile</a></li>
                                <li><a class="dropdown-item" href="{{ route('profile.password') }}">Change Password</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item">Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            @auth
                <div class="col-md-2 sidebar p-0">
                    <div class="list-group list-group-flush">
                            <!-- For admin users -->
                        @if(Auth::user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                                <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                            </a>
                            
                            <!-- Users Management Section -->
                            <a href="#usersSubmenu" data-bs-toggle="collapse" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                                <span><i class="fas fa-users me-2"></i> Users</span>
                                <i class="fas fa-angle-down"></i>
                            </a>
                            <div class="collapse {{ request()->routeIs('admin.users*') ? 'show' : '' }}" id="usersSubmenu">
                                <a href="{{ route('admin.users', ['role' => 'all']) }}" class="list-group-item list-group-item-action ps-4 {{ request()->is('admin/users') && request()->input('role') == 'all' ? 'active' : '' }}">All Users</a>
                                <a href="{{ route('admin.users', ['role' => 'admin']) }}" class="list-group-item list-group-item-action ps-4 {{ request()->is('admin/users') && request()->input('role') == 'admin' ? 'active' : '' }}">Administrators</a>
                                <a href="{{ route('admin.users', ['role' => 'teacher']) }}" class="list-group-item list-group-item-action ps-4 {{ request()->is('admin/users') && request()->input('role') == 'teacher' ? 'active' : '' }}">Teachers</a>
                                <a href="{{ route('admin.users', ['role' => 'student']) }}" class="list-group-item list-group-item-action ps-4 {{ request()->is('admin/users') && request()->input('role') == 'student' ? 'active' : '' }}">Students</a>
                                <a href="{{ route('admin.users.create') }}" class="list-group-item list-group-item-action ps-4 {{ request()->routeIs('admin.users.create') ? 'active' : '' }}">Add New User</a>
                            </div>
                            
                            <!-- Academic Management Section -->
                            <a href="#academicSubmenu" data-bs-toggle="collapse" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center {{ request()->routeIs('academic-years*') || request()->routeIs('classes*') || request()->routeIs('subjects*') ? 'active' : '' }}">
                                <span><i class="fas fa-school me-2"></i> Academic</span>
                                <i class="fas fa-angle-down"></i>
                            </a>
                            <div class="collapse {{ request()->routeIs('academic-years*') || request()->routeIs('classes*') || request()->routeIs('subjects*') ? 'show' : '' }}" id="academicSubmenu">
                                <a href="{{ route('academic-years.index') }}" class="list-group-item list-group-item-action ps-4 {{ request()->routeIs('academic-years*') ? 'active' : '' }}">Academic Years</a>
                                <a href="{{ route('classes.index') }}" class="list-group-item list-group-item-action ps-4 {{ request()->routeIs('classes*') ? 'active' : '' }}">Classes</a>
                                <a href="{{ route('subjects.index') }}" class="list-group-item list-group-item-action ps-4 {{ request()->routeIs('subjects*') ? 'active' : '' }}">Subjects</a>
                            </div>
                            
                            <!-- Curriculum & Timetable Section -->
                            <a href="#curriculumSubmenu" data-bs-toggle="collapse" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center {{ request()->routeIs('curriculum*') || request()->routeIs('timetable*') ? 'active' : '' }}">
                                <span><i class="fas fa-book me-2"></i> Curriculum & Schedule</span>
                                <i class="fas fa-angle-down"></i>
                            </a>
                            <div class="collapse {{ request()->routeIs('curriculum*') || request()->routeIs('timetable*') ? 'show' : '' }}" id="curriculumSubmenu">
                                <a href="{{ route('curriculum.index') }}" class="list-group-item list-group-item-action ps-4 {{ request()->routeIs('curriculum*') ? 'active' : '' }}">Curriculum</a>
                                <a href="{{ route('timetable.index') }}" class="list-group-item list-group-item-action ps-4 {{ request()->routeIs('timetable*') ? 'active' : '' }}">Timetable</a>
                            </div>
                            
                            <!-- Attendance & Examinations Section -->
                            <a href="#attendanceSubmenu" data-bs-toggle="collapse" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center {{ request()->routeIs('student.attendance*') || request()->routeIs('teacher.attendance*') || request()->routeIs('examinations*') ? 'active' : '' }}">
                                <span><i class="fas fa-clipboard-check me-2"></i> Attendance & Exams</span>
                                <i class="fas fa-angle-down"></i>
                            </a>
                            <div class="collapse {{ request()->routeIs('student.attendance*') || request()->routeIs('teacher.attendance*') || request()->routeIs('examinations*') ? 'show' : '' }}" id="attendanceSubmenu">
                                <a href="{{ route('student.attendance.index') }}" class="list-group-item list-group-item-action ps-4 {{ request()->routeIs('student.attendance*') ? 'active' : '' }}">Student Attendance</a>
                                <a href="{{ route('teacher.attendance.index') }}" class="list-group-item list-group-item-action ps-4 {{ request()->routeIs('teacher.attendance*') ? 'active' : '' }}">Teacher Attendance</a>
                                <a href="{{ route('examinations.index') }}" class="list-group-item list-group-item-action ps-4 {{ request()->routeIs('examinations*') ? 'active' : '' }}">Examinations</a>
                            </div>
                            
                            <!-- Fee Management Section -->
                            <a href="#feeSubmenu" data-bs-toggle="collapse" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center {{ request()->is('fees*') || request()->is('payments*') ? 'active' : '' }}">
                                <span><i class="fas fa-money-bill-wave me-2"></i> Fee Management</span>
                                <i class="fas fa-angle-down"></i>
                            </a>
                            <div class="collapse {{ request()->is('fees*') || request()->is('fees.payments*') ? 'show' : '' }}" id="feeSubmenu">
                                <a href="{{ route('fees.dashboard') }}" class="list-group-item list-group-item-action ps-4 {{ request()->is('fees/dashboard') ? 'active' : '' }}">Fee Dashboard</a>
                                <a href="{{ route('fees.types.index') }}" class="list-group-item list-group-item-action ps-4 {{ request()->is('fees/types*') ? 'active' : '' }}">Fee Types</a>
                                <a href="{{ route('fees.structures.index') }}" class="list-group-item list-group-item-action ps-4 {{ request()->is('fees/structures*') ? 'active' : '' }}">Fee Structures</a>
                                <a href="{{ route('fees.students.index') }}" class="list-group-item list-group-item-action ps-4 {{ request()->is('fees/students') ? 'active' : '' }}">Student Fees</a>
                                <a href="{{ route('payments.index') }}" class="list-group-item list-group-item-action ps-4 {{ request()->is('payments') ? 'active' : '' }}">Payment Records</a>
                                <a href="{{ route('admin.payments.reports') }}" class="list-group-item list-group-item-action ps-4 {{ request()->is('admin/payments/reports') ? 'active' : '' }}">Fee Reports</a>
                                <a href="{{ route('admin.fees.reminders.index') }}" class="list-group-item list-group-item-action ps-4 {{ request()->is('admin/fees/reminders*') ? 'active' : '' }}">Fee Reminders</a>
                                <a href="{{ route('admin.fees.bulk.index') }}" class="list-group-item list-group-item-action ps-4 {{ request()->is('admin/fees/bulk*') ? 'active' : '' }}">Bulk Operations</a>
                            </div>
                        @endif

                        <!-- For teacher users -->
                        @if(Auth::user()->isTeacher())
                            <a href="{{ route('teacher.dashboard') }}" class="list-group-item list-group-item-action {{ request()->routeIs('teacher.dashboard') ? 'active' : '' }}">
                                <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                            </a>
                            
                            <!-- Add curriculum access for teachers -->
                            <a href="{{ route('curriculum.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('curriculum*') ? 'active' : '' }}">
                                <i class="fas fa-book me-2"></i> Curriculum
                            </a>
                            
                            <!-- Add timetable access for teachers -->
                            <a href="{{ route('timetable.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('timetable*') ? 'active' : '' }}">
                                <i class="fas fa-calendar-alt me-2"></i> Timetable
                            </a>
                            
                            <!-- Add attendance access for students -->
                            <a href="{{ route('student.attendance.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('student.attendance*') ? 'active' : '' }}">
                                <i class="fas fa-clipboard-check me-2"></i> Student Attendance
                            </a>
                            
                            <!-- Add examinations access for teachers -->
                            <a href="{{ route('examinations.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('examinations*') ? 'active' : '' }}">
                                <i class="fas fa-graduation-cap me-2"></i> Examinations
                            </a>
                        @endif

                        <!-- For student users -->
                        @if(Auth::user()->isStudent())
                            <a href="{{ route('student.dashboard') }}" class="list-group-item list-group-item-action {{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
                                <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                            </a>
                            
                            <!-- Add curriculum access for students -->
                            <a href="{{ route('curriculum.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('curriculum*') ? 'active' : '' }}">
                                <i class="fas fa-book me-2"></i> Curriculum
                            </a>
                            
                            <!-- Add timetable access for students -->
                            <a href="{{ route('timetable.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('timetable*') ? 'active' : '' }}">
                                <i class="fas fa-calendar-alt me-2"></i> Class Schedule
                            </a>
                            
                            <a href="{{ route('student.attendance.my-report') }}" class="list-group-item list-group-item-action {{ request()->routeIs('student.attendance.my-report') ? 'active' : '' }}">
                                <i class="fas fa-clipboard-check me-2"></i> My Attendance
                            </a>
                            
                            <a href="{{ route('examinations.student_results') }}" class="list-group-item list-group-item-action {{ request()->routeIs('examinations.student_results') ? 'active' : '' }}">
                                <i class="fas fa-graduation-cap me-2"></i> My Results
                            </a>
                            
                            <!-- Fee Management for Students (NEW) -->
                            <a href="{{ route('student.fees.index') }}" class="list-group-item list-group-item-action {{ request()->is('student/fees') ? 'active' : '' }}">
    <i class="fas fa-money-bill-wave me-2"></i> My Fees
</a>

<a href="{{ route('student.payments.history') }}" class="list-group-item list-group-item-action {{ request()->is('student/payments/history') ? 'active' : '' }}">
    <i class="fas fa-receipt me-2"></i> Payment History
</a>
                        @endif
                    </div>
                </div>
                <div class="col-md-10 content">
            @else
                <div class="col-md-12 content">
            @endauth
                
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Add Chart.js for Fee Dashboard (NEW) -->
    @stack('scripts')
</body>
</html>