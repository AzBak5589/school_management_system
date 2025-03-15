<!-- resources/views/fees/students/student_fees.blade.php -->
@extends('layouts.app')

@section('title', 'My Fees')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">My Fees</h3>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Student Information</h5>
                            <p><strong>Name:</strong> {{ $student->first_name }} {{ $student->last_name }}</p>
                            <p><strong>ID Number:</strong> {{ $student->student_id }}</p>
                            <p><strong>Class:</strong> {{ $student->currentClass->class_name }} {{ $student->currentClass->section }}</p>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5>Fee Summary</h5>
                                    <div class="row">
                                        <div class="col-md-4 text-center">
                                            <h4>{{ number_format($totalFees, 2) }}</h4>
                                            <p>Total Fees</p>
                                        </div>
                                        <div class="col-md-4 text-center">
                                            <h4>{{ number_format($totalPaid, 2) }}</h4>
                                            <p>Total Paid</p>
                                        </div>
                                        <div class="col-md-4 text-center">
                                            <h4>{{ number_format($totalBalance, 2) }}</h4>
                                            <p>Balance Due</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Fee Type</th>
                                    <th>Academic Year</th>
                                    <th>Due Date</th>
                                    <th>Amount</th>
                                    <th>Paid</th>
                                    <th>Balance</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($studentFees as $fee)
                                    <tr class="{{ $fee->is_overdue ? 'bg-warning' : '' }}">
                                        <td>{{ $fee->feeStructure->feeType->type_name }}</td>
                                        <td>{{ $fee->feeStructure->academicYear->year_name }}</td>
                                        <td>{{ $fee->due_date->format('d-m-Y') }}</td>
                                        <td>{{ number_format($fee->amount, 2) }}</td>
                                        <td>{{ number_format($fee->paid_amount, 2) }}</td>
                                        <td>{{ number_format($fee->balance, 2) }}</td>
                                        <td>
                                            @if($fee->is_fully_paid)
                                                <span class="badge badge-success">Paid</span>
                                            @elseif($fee->paid_amount > 0)
                                                <span class="badge badge-warning">Partially Paid</span>
                                            @elseif($fee->is_overdue)
                                                <span class="badge badge-danger">Overdue</span>
                                            @else
                                                <span class="badge badge-secondary">Unpaid</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('student-fees.show', $fee->fee_id) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i> Details
                                            </a>
                                            @if($fee->payments->count() > 0)
                                                <a href="{{ route('student.payment-history', $fee->fee_id) }}" class="btn btn-sm btn-secondary">
                                                    <i class="fas fa-history"></i> Payments
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No fee records found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection