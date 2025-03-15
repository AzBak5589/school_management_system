<!-- resources/views/fees/payments/student_payments.blade.php -->
@extends('layouts.app')

@section('title', 'My Payment History')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">My Payment History</h3>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Student Information</h5>
                            <p><strong>Name:</strong> {{ $student->first_name }} {{ $student->last_name }}</p>
                            <p><strong>ID Number:</strong> {{ $student->student_id }}</p>
                            <p><strong>Class:</strong> {{ $student->currentClass->class_name }} {{ $student->currentClass->section }}</p>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Receipt No.</th>
                                    <th>Fee Type</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                    <th>Method</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($payments as $payment)
                                    <tr>
                                        <td>{{ $payment->receipt_number }}</td>
                                        <td>{{ $payment->studentFee->feeStructure->feeType->type_name }}</td>
                                        <td>{{ number_format($payment->amount_paid, 2) }}</td>
                                        <td>{{ $payment->payment_date->format('d-m-Y') }}</td>
                                        <td>
                                            @if($payment->payment_method == 'cash')
                                                <span class="badge badge-success">Cash</span>
                                            @elseif($payment->payment_method == 'check')
                                                <span class="badge badge-info">Check</span>
                                            @elseif($payment->payment_method == 'bank_transfer')
                                                <span class="badge badge-primary">Bank Transfer</span>
                                            @else
                                                <span class="badge badge-secondary">Online</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('payments.receipt', $payment->payment_id) }}" class="btn btn-sm btn-success">
                                                <i class="fas fa-receipt"></i> View Receipt
                                            </a>
                                            <a href="{{ route('payments.download-receipt', $payment->payment_id) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-download"></i> Download
                                            </a>
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
                    
                    {{ $payments->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection