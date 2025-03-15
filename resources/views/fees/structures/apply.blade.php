<!-- resources/views/fees/structures/apply.blade.php -->
@extends('layouts.app')

@section('title', 'Apply Fee to Students')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Apply Fee to Students</h3>
                    <div class="card-tools">
                        <a href="{{ route('fees.structures.index') }}" class="btn btn-default btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Fee Structures
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="alert alert-info">
                        <p>You are about to apply the following fee to all students in {{ $feeStructure->class->class_name }} - {{ $feeStructure->class->section }}.</p>
                        <p><strong>Fee Type:</strong> {{ $feeStructure->feeType->type_name }}</p>
                        <p><strong>Amount:</strong> {{ number_format($feeStructure->amount, 2) }}</p>
                        <p><strong>Academic Year:</strong> {{ $feeStructure->academicYear->year_name }}</p>
                    </div>

                    <form action="{{ route('fees.structures.process-apply', $feeStructure->fee_structure_id) }}" method="POST">
                        @csrf
                        
                        <div class="form-group">
                            <label for="due_date">Due Date<span class="text-danger">*</span></label>
                            <input type="date" name="due_date" id="due_date" class="form-control @error('due_date') is-invalid @enderror" value="{{ old('due_date', date('Y-m-d', strtotime('+30 days'))) }}" required>
                            @error('due_date')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-success" onclick="return confirm('Are you sure you want to apply this fee to all students in this class?');">
                                <i class="fas fa-check"></i> Apply Fee to All Students
                            </button>
                            <a href="{{ route('fees.structures.index') }}" class="btn btn-default">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection