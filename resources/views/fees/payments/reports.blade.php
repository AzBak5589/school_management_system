<!-- resources/views/fees/payments/reports.blade.php -->
@extends('layouts.app')

@section('title', 'Payment Reports')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Payment Reports</h3>
                    <div class="card-tools">
                        <a href="{{ route('payments.index') }}" class="btn btn-default btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Payments
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('payments.reports') }}" method="GET" class="mb-4">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Start Date</label>
                                    <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>End Date</label>
                                    <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="btn btn-primary form-control">
                                        <i class="fas fa-sync"></i> Update Report
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Summary</h5>
                                </div>
                                <div class="card-body">
                                    <h3 class="text-center">{{ number_format($totalCollected, 2) }}</h3>
                                    <p class="text-center">Total Amount Collected</p>
                                    
                                    <div class="table-responsive mt-4">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Payment Method</th>
                                                    <th class="text-right">Amount</th>
                                                    <th>Percentage</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($paymentsByMethod as $payment)
                                                    <tr>
                                                        <td>{{ ucfirst($payment->payment_method) }}</td>
                                                        <td class="text-right">{{ number_format($payment->total, 2) }}</td>
                                                        <td>
                                                            <div class="progress">
                                                                <div class="progress-bar" role="progressbar" style="width: {{ ($payment->total / $totalCollected) * 100 }}%" aria-valuenow="{{ ($payment->total / $totalCollected) * 100 }}" aria-valuemin="0" aria-valuemax="100">
                                                                    {{ number_format(($payment->total / $totalCollected) * 100, 1) }}%
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Daily Collection</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="collectionChart" height="250"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="card-title">Actions</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <a href="{{ route('payments.export-report', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-success btn-block">
                                        <i class="fas fa-file-excel"></i> Export to Excel
                                    </a>
                                </div>
                                <div class="col-md-4">
                                    <a href="{{ route('payments.print-report', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-info btn-block" target="_blank">
                                        <i class="fas fa-print"></i> Print Report
                                    </a>
                                </div>
                                <div class="col-md-4">
                                    <a href="{{ route('payments.pdf-report', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-danger btn-block">
                                        <i class="fas fa-file-pdf"></i> Generate PDF
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var ctx = document.getElementById('collectionChart').getContext('2d');
        
        var dailyTotals = @json($dailyTotals);
        var labels = dailyTotals.map(function(item) {
            return item.date;
        });
        var data = dailyTotals.map(function(item) {
            return item.total;
        });
        
        var myChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Daily Collection Amount',
                    data: data,
                    backgroundColor: 'rgba(60, 141, 188, 0.2)',
                    borderColor: 'rgba(60, 141, 188, 1)',
                    borderWidth: 2,
                    pointRadius: 3,
                    pointBackgroundColor: 'rgba(60, 141, 188, 1)',
                    pointBorderColor: '#fff',
                    pointHoverRadius: 5,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value.toFixed(2);
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
@endsection="alert alert-success">{{ session('success') }}</div>
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