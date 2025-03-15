@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Profile</h1>
        <a href="{{ route('profile.show') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left fa-sm"></i> Back to Profile
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Edit Personal Information</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="first_name" class="form-label">First Name</label>
                                <input type="text" class="form-control @error('first_name') is-invalid @enderror" id="first_name" name="first_name" value="{{ old('first_name', $profile->first_name) }}" required>
                                @error('first_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="last_name" class="form-label">Last Name</label>
                                <input type="text" class="form-control @error('last_name') is-invalid @enderror" id="last_name" name="last_name" value="{{ old('last_name', $profile->last_name) }}" required>
                                @error('last_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $profile->phone) }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3">{{ old('address', $profile->address) }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        @if($user->isTeacher())
                            <div class="mb-3">
                                <label for="qualification" class="form-label">Qualification</label>
                                <input type="text" class="form-control @error('qualification') is-invalid @enderror" id="qualification" name="qualification" value="{{ old('qualification', $profile->qualification) }}">
                                @error('qualification')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        @elseif($user->isStudent())
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="emergency_contact_name" class="form-label">Emergency Contact Name</label>
                                    <input type="text" class="form-control @error('emergency_contact_name') is-invalid @enderror" id="emergency_contact_name" name="emergency_contact_name" value="{{ old('emergency_contact_name', $profile->emergency_contact_name) }}">
                                    @error('emergency_contact_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="emergency_contact_phone" class="form-label">Emergency Contact Phone</label>
                                    <input type="text" class="form-control @error('emergency_contact_phone') is-invalid @enderror" id="emergency_contact_phone" name="emergency_contact_phone" value="{{ old('emergency_contact_phone', $profile->emergency_contact_phone) }}">
                                    @error('emergency_contact_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        @endif
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary">Update Profile</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Profile Information</h6>
                </div>
                <div class="card-body">
                    <p>Update your personal information here. Some fields cannot be changed as they were set during registration.</p>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> To change your password, please use the <a href="{{ route('profile.password') }}">Change Password</a> option.
                    </div>
                    
                    @if($user->isTeacher() || $user->isStudent())
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> Some identity information (such as 
                            @if($user->isTeacher())
                                Employee ID and Join Date
                            @elseif($user->isStudent())
                                Admission Number and Admission Date
                            @endif
                            ) can only be updated by administrators.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection