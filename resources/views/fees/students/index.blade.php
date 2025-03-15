<!-- resources/views/fees/students/index.blade.php -->
@extends('layouts.app')

@section('title', 'Student Fees')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Student Fees - {{ $class->class_name }} {{ $class->section }}</h3>
                    <div class="card-tools">
                        <div class="btn-group">
                            <a href="{{ route('student-fees.index', ['class_id' => $class->class_id, 'status' => 'all']) }}" class="btn btn-sm {{ $status == 'all' ? 'btn-primary' : 'btn-default' }}">
                                All
                            </a>
                            <a href="{{ route('student-fees.index', ['class_id' => $class->class_id, 'status' => 'unpaid']) }}" class="btn btn-sm {{ $status == 'unpaid' ? 'btn-primary' : 'btn-default' }}">
                                Unpaid
                            </a>
                            <a href="{{ route('student-fees.index', ['class_id' => $class->class_id, 'status' => 'partially_paid']) }}" class="btn btn-sm {{ $status == 'partially_paid' ? 'btn-primary' : 'btn-default' }}">
                                Partially Paid
                            </a>
                            <a href="{{ route('student-fees.index', ['class_id' => $class->class_id, 'status' => 'paid']) }}" class="btn btn-sm {{ $status == 'paid' ? 'btn-primary' : 'btn-default' }}">
                                Paid
                            </a>
                        </div>
                        <a href="{{ route('student-fees.index') }}" class="btn btn-default btn-sm ml-2">
                            <i class="fas fa-arrow-left"></i> Back to Classes
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Student ID</th>
                                    <th>Student Name</th>
                                    <th>Total Fees</th>
                                    <th>Paid</th>
                                    <th>Balance</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($students as $student)
                                    @php
                                        $fees = $studentFees[$student->student_id] ?? collect();
                                        $totalAmount = $fees->sum('amount');
                                        $totalPaid = $fees->reduce(function ($carry, $fee) {
                                            return $carry + $fee->payments->sum('amount_paid');
                                        }, 0);
                                        $balance = $totalAmount - $totalPaid;
                                    @endphp
                                    <tr>
                                        <td>{{ $student->student_id }}</td>
                                        <td>
                                            <a href="{{ route('students.show', $student->student_id) }}">
                                                {{ $student->first_name }} {{ $student->last_name }}
                                            </a>
                                        </td>
                                        <td>{{ number_format($totalAmount, 2) }}</td>
                                        <td>{{ number_format($totalPaid, 2) }}</td>
                                        <td>{{ number_format($balance, 2) }}</td>
                                        <td>
                                            @if($balance <= 0)
                                                <span class="badge badge-success">Paid</span>
                                            @elseif($totalPaid > 0)
                                                <span class="badge badge-warning">Partially Paid</span>
                                            @else
                                                <span class="badge badge-danger">Unpaid</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="#" class="btn btn-sm btn-info" data-toggle="dropdown">
                                                    <i class="fas fa-cog"></i> Manage <i class="fas fa-caret-down"></i>
                                                </a>
                                                <div class="dropdown-menu">
                                                    <a href="{{ route('students.fees', $student->student_id) }}" class="dropdown-item">
                                                        <i class="fas fa-list"></i> View All Fees
                                                    </a>
                                                    <a href="{{ route('students.add-fee', $student->student_id) }}" class="dropdown-item">
                                                        <i class="fas fa-plus"></i> Add New Fee
                                                    </a>
                                                    <a href="{{ route('students.fee-statement', $student->student_id) }}" class="dropdown-item">
                                                        <i class="fas fa-file-alt"></i> Fee Statement
                                                    </a>
                                                    <a href="{{ route('students.payment-history', $student->student_id) }}" class="dropdown-item">
                                                        <i class="fas fa-history"></i> Payment History
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No students found in this class.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    {{ $students->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection