@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">My Profile</h1>
        <div>
            <a href="{{ route('profile.edit') }}" class="btn btn-primary">
                <i class="fas fa-edit fa-sm"></i> Edit Profile
            </a>
            <a href="{{ route('profile.password') }}" class="btn btn-secondary">
                <i class="fas fa-key fa-sm"></i> Change Password
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Personal Information</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <tr>
                                <th width="200">Username</th>
                                <td>{{ $user->username }}</td>
                            </tr>
                            <tr>
                                <th>Full Name</th>
                                <td>{{ $profile->full_name }}</td>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <td>{{ $user->email }}</td>
                            </tr>
                            <tr>
                                <th>Role</th>
                                <td>
                                    @if($user->isAdmin())
                                        <span class="badge bg-primary">Administrator</span>
                                    @elseif($user->isTeacher())
                                        <span class="badge bg-success">Teacher</span>
                                    @elseif($user->isStudent())
                                        <span class="badge bg-info">Student</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Phone</th>
                                <td>{{ $profile->phone ?: 'Not specified' }}</td>
                            </tr>
                            <tr>
                                <th>Address</th>
                                <td>{{ $profile->address ?: 'Not specified' }}</td>
                            </tr>
                            
                            @if($user->isTeacher())
                                <tr>
                                    <th>Employee ID</th>
                                    <td>{{ $profile->employee_id }}</td>
                                </tr>
                                <tr>
                                    <th>Qualification</th>
                                    <td>{{ $profile->qualification ?: 'Not specified' }}</td>
                                </tr>
                                <tr>
                                    <th>Join Date</th>
                                    <td>{{ $profile->join_date->format('F d, Y') }}</td>
                                </tr>
                            @elseif($user->isStudent())
                                <tr>
                                    <th>Admission Number</th>
                                    <td>{{ $profile->admission_number }}</td>
                                </tr>
                                <tr>
                                    <th>Admission Date</th>
                                    <td>{{ $profile->admission_date->format('F d, Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Emergency Contact</th>
                                    <td>
                                        @if($profile->emergency_contact_name)
                                            {{ $profile->emergency_contact_name }} 
                                            @if($profile->emergency_contact_phone)
                                                ({{ $profile->emergency_contact_phone }})
                                            @endif
                                        @else
                                            Not specified
                                        @endif
                                    </td>
                                </tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Account Information</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Account Status:</strong>
                        @if($user->is_active)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-danger">Inactive</span>
                        @endif
                    </div>
                    
                    <div class="mb-3">
                        <strong>Last Login:</strong>
                        <div>{{ $user->last_login ? $user->last_login->format('F d, Y h:i A') : 'Never' }}</div>
                    </div>
                    
                    <div class="mb-3">
                        <strong>Account Created:</strong>
                        <div>{{ $user->created_at->format('F d, Y') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection