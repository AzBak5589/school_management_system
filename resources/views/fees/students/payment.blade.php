<!-- resources/views/fees/students/payment.blade.php -->
@extends('layouts.app')

@section('title', 'Record Payment')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Record Payment</h3>
                    <div class="card-tools">
                        <a href="{{ route('student-fees.show', $studentFee->fee_id) }}" class="btn btn-default btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Fee Details
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

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Fee Information</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th>Fee Type:</th>
                                    <td>{{ $studentFee->feeStructure->feeType->type_name }}</td>
                                </tr>
                                <tr>
                                    <th>Total Amount:</th>
                                    <td>{{ number_format($studentFee->amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Amount Paid:</th>
                                    <td>{{ number_format($paidAmount, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Remaining Balance:</th>
                                    <td>{{ number_format($remainingBalance, 2) }}</td>
                                </tr>
                            </table>
                        </div>
                        
                        <div class="col-md-6">
                            <h5>Student Information</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th>Student ID:</th>
                                    <td>{{ $studentFee->student->student_id }}</td>
                                </tr>
                                <tr>
                                    <th>Name:</th>
                                    <td>{{ $studentFee->student->first_name }} {{ $studentFee->student->last_name }}</td>
                                </tr>
                                <tr>
                                    <th>Class:</th>
                                    <td>{{ $studentFee->student->currentClass->class_name }} {{ $studentFee->student->currentClass->section }}</td>
                                </tr>
                            </table>
                            
                            <div class="mt-4">
                                <h5>Actions</h5>
                                <div class="btn-group">
                                    @if(Auth::user()->role === 'admin')
                                        <a href="{{ route('student-fees.edit', $studentFee->fee_id) }}" class="btn btn-info">
                                            <i class="fas fa-edit"></i> Edit Fee
                                        </a>
                                        <a href="{{ route('student-fees.record-payment', $studentFee->fee_id) }}" class="btn btn-success">
                                            <i class="fas fa-money-bill"></i> Record Payment
                                        </a>
                                        <form action="{{ route('student-fees.destroy', $studentFee->fee_id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this fee?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h5>Payment History</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Receipt No.</th>
                                            <th>Date</th>
                                            <th>Amount</th>
                                            <th>Method</th>
                                            <th>Received By</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($studentFee->payments as $payment)
                                            <tr>
                                                <td>{{ $payment->receipt_number }}</td>
                                                <td>{{ $payment->payment_date->format('d-m-Y') }}</td>
                                                <td>{{ number_format($payment->amount_paid, 2) }}</td>
                                                <td>{{ ucfirst($payment->payment_method) }}</td>
                                                <td>{{ $payment->receiver->name }}</td>
                                                <td>
                                                    <a href="{{ route('payments.receipt', $payment->payment_id) }}" class="btn btn-sm btn-success">
                                                        <i class="fas fa-receipt"></i> Receipt
                                                    </a>
                                                    @if(Auth::user()->role === 'admin')
                                                        <a href="{{ route('payments.delete', $payment->payment_id) }}" class="btn btn-sm btn-danger">
                                                            <i class="fas fa-trash"></i> Delete
                                                        </a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">No payment records found.</td>
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
    </div>
</div>
@endsection