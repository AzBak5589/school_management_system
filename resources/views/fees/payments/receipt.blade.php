<!-- resources/views/fees/payments/receipt.blade.php -->
@extends('layouts.app')

@section('title', 'Payment Receipt')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Payment Receipt</h3>
                    <div class="card-tools">
                        <a href="{{ url()->previous() }}" class="btn btn-default btn-sm">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                        <a href="{{ route('payments.download-receipt', $payment->payment_id) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-download"></i> Download PDF
                        </a>
                        <button onclick="window.print()" class="btn btn-info btn-sm">
                            <i class="fas fa-print"></i> Print
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="receipt-content">
                        <div class="row">
                            <div class="col-md-12 text-center mb-3">
                                <img src="{{ asset('images/school-logo.png') }}" alt="School Logo" style="max-height: 80px;">
                                <h2 class="mt-2">{{ config('app.name') }}</h2>
                                <p>School Address, City, State, ZIP</p>
                                <h4 class="mt-3">OFFICIAL RECEIPT</h4>
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <p><strong>Receipt No:</strong> {{ $payment->receipt_number }}</p>
                                <p><strong>Date:</strong> {{ $payment->payment_date->format('d/m/Y') }}</p>
                                <p><strong>Payment Method:</strong> {{ ucfirst($payment->payment_method) }}</p>
                                @if($payment->transaction_id)
                                    <p><strong>Transaction ID:</strong> {{ $payment->transaction_id }}</p>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <p><strong>Student ID:</strong> {{ $payment->studentFee->student->student_id }}</p>
                                <p><strong>Student Name:</strong> {{ $payment->studentFee->student->first_name }} {{ $payment->studentFee->student->last_name }}</p>
                                <p><strong>Class:</strong> {{ $payment->studentFee->student->currentClass->class_name }} {{ $payment->studentFee->student->currentClass->section }}</p>
                            </div>
                        </div>
                        
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <form action="{{ route('payments.index') }}" method="GET" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Payment Method</label>
                                    <select name="payment_method" class="form-control">
                                        <option value="">All Methods</option>
                                        <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                                        <option value="check" {{ request('payment_method') == 'check' ? 'selected' : '' }}>Check</option>
                                        <option value="bank_transfer" {{ request('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                        <option value="online" {{ request('payment_method') == 'online' ? 'selected' : '' }}>Online Payment</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Start Date</label>
                                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>End Date</label>
                                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="btn btn-primary form-control">
                                        <i class="fas fa-search"></i> Filter
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Receipt No.</th>
                                    <th>Student</th>
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
                                        <td>{{ $payment->studentFee->student->first_name }} {{ $payment->studentFee->student->last_name }}</td>
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
                                            <a href="{{ route('payments.show', $payment->payment_id) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                            <a href="{{ route('payments.receipt', $payment->payment_id) }}" class="btn btn-sm btn-success">
                                                <i class="fas fa-receipt"></i> Receipt
                                            </a>
                                            <a href="{{ route('payments.delete', $payment->payment_id) }}" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i> Delete
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No payment records found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    {{ $payments->appends(request()->except('page'))->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection