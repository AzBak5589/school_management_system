@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">User Management</h1>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
            <i class="fas fa-user-plus"></i> Add New User
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Users List</h6>
            <div class="btn-group">
                <a href="{{ route('admin.users', ['role' => 'all']) }}" class="btn btn-sm {{ $role === 'all' ? 'btn-primary' : 'btn-outline-primary' }}">All</a>
                <a href="{{ route('admin.users', ['role' => 'admin']) }}" class="btn btn-sm {{ $role === 'admin' ? 'btn-primary' : 'btn-outline-primary' }}">Admins</a>
                <a href="{{ route('admin.users', ['role' => 'teacher']) }}" class="btn btn-sm {{ $role === 'teacher' ? 'btn-primary' : 'btn-outline-primary' }}">Teachers</a>
                <a href="{{ route('admin.users', ['role' => 'student']) }}" class="btn btn-sm {{ $role === 'student' ? 'btn-primary' : 'btn-outline-primary' }}">Students</a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td>{{ $user->user_id }}</td>
                                <td>{{ $user->username }}</td>
                                <td>{{ $user->profile() ? $user->profile()->full_name : 'N/A' }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @if($user->role === 'admin')
                                        <span class="badge bg-primary">Admin</span>
                                    @elseif($user->role === 'teacher')
                                        <span class="badge bg-success">Teacher</span>
                                    @elseif($user->role === 'student')
                                        <span class="badge bg-info">Student</span>
                                    @endif
                                </td>
                                <td>
                                    @if($user->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.users.edit', $user->user_id) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.users.delete', $user->user_id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this user?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                {{ $users->appends(['role' => $role])->links() }}
            </div>
        </div>
    </div>
</div>
@endsection