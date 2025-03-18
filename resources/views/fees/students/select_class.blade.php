<!-- resources/views/fees/students/select_class.blade.php -->
@extends('layouts.app')

@section('title', 'Select Class for Fee Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Select Class for Fee Management</h3>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <div class="row">
                        @foreach($classes as $class)
                            <div class="col-md-3 mb-4">
                                <div class="card">
                                    <div class="card-body text-center">
                                        <h5>{{ $class->class_name }}</h5>
                                        <p>Section: {{ $class->section }}</p>
                                        <a href="{{ route('fees.students.index', ['class_id' => $class->class_id]) }}" class="btn btn-primary">
                                            Manage Fees
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection