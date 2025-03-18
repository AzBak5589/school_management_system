<!-- resources/views/fees/payments/index.blade.php -->
@extends('layouts.app')

@section('title', 'Payment Records')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Payment Records</h3>
                    <div class="card-tools">
                        <a href="{{ route('fees.payments.reports') }}" class="btn btn-info btn-sm mr-2">
                            <i class="fas fa-chart-bar"></i> Payment Reports
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
                                @forelse($payments ?? [] as $payment)
                                    <tr>
                                        <td>{{ $payment->receipt_number }}</td>
                                        <td>{{ $payment->studentFee->student->first_name ?? '' }} {{ $payment->studentFee->student->last_name ?? '' }}</td>
                                        <td>{{ $payment->studentFee->feeStructure->feeType->type_name ?? '' }}</td>
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
                    
                    @if(isset($payments) && method_exists($payments, 'links'))
                        {{ $payments->appends(request()->except('page'))->links() }}
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection