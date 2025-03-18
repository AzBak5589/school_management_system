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
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    @if(session('info'))
                        <div class="alert alert-info">{{ session('info') }}</div>
                    @endif
                    
                    <form action="{{ route('fees.payments.reports') }}" method="GET" class="mb-4">
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
@endsection