<!-- resources/views/fees/dashboard.blade.php -->
@extends('layouts.app')

@section('title', 'Fee Management Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Info boxes -->
    <div class="row">
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-info"><i class="fas fa-money-bill-alt"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Fees (Current Year)</span>
                    <span class="info-box-number">{{ number_format($totalFees, 2) }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-success"><i class="fas fa-cash-register"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Collected</span>
                    <span class="info-box-number">{{ number_format($totalCollected, 2) }}</span>
                    <span class="progress-description">
                        {{ $collectionPercentage }}% of total
                    </span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-warning"><i class="fas fa-exclamation-triangle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Outstanding Balance</span>
                    <span class="info-box-number">{{ number_format($outstandingBalance, 2) }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-danger"><i class="fas fa-clock"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Overdue Payments</span>
                    <span class="info-box-number">{{ $overdueCount }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <!-- Collection Chart -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Monthly Collection</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart">
                        <canvas id="collectionChart" height="300"></canvas>
                    </div>
                </div>
            </div>

            <!-- Recent Payments -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Recent Payments</h3>
                    <div class="card-tools">
                        <a href="{{ route('payments.index') }}" class="btn btn-tool">
                            <i class="fas fa-list"></i> View All
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Receipt</th>
                                    <th>Student</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                    <th>Method</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentPayments as $payment)
                                <tr>
                                    <td>
                                        <a href="{{ route('payments.receipt', $payment->payment_id) }}">
                                            {{ $payment->receipt_number }}
                                        </a>
                                    </td>
                                    <td>{{ $payment->studentFee->student->first_name }} {{ $payment->studentFee->student->last_name }}</td>
                                    <td>{{ number_format($payment->amount_paid, 2) }}</td>
                                    <td>{{ $payment->payment_date->format('d-m-Y') }}</td>
                                    <td>{{ ucfirst($payment->payment_method) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Fee Types Chart -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Fee Distribution</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart">
                        <canvas id="feeDistributionChart" height="250"></canvas>
                    </div>
                </div>
            </div>

            <!-- Payment Methods Chart -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Payment Methods</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart">
                        <canvas id="paymentMethodsChart" height="250"></canvas>
                    </div>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Quick Actions</h3>
                </div>
                <div class="card-body">
    <div class="list-group">
        <a href="{{ route('fees.types.index') }}" class="list-group-item list-group-item-action">
            <i class="fas fa-tags mr-2"></i> Manage Fee Types
        </a>
        <a href="{{ route('fees.structures.index') }}" class="list-group-item list-group-item-action">
            <i class="fas fa-sitemap mr-2"></i> Fee Structures
        </a>
        <a href="{{ route('fees.students.index') }}" class="list-group-item list-group-item-action">
            <i class="fas fa-users mr-2"></i> Student Fees
        </a>
        <a href="{{ route('fees.payments.index') }}" class="list-group-item list-group-item-action">
            <i class="fas fa-money-check-alt mr-2"></i> Payment Records
        </a>
        <a href="{{ route('fees.payments.reports') }}" class="list-group-item list-group-item-action">
            <i class="fas fa-chart-bar mr-2"></i> Reports & Analytics
        </a>
        <a href="{{ route('fees.reminders.index') }}" class="list-group-item list-group-item-action">
            <i class="fas fa-bell mr-2"></i> Send Fee Reminders
        </a>
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
        // Monthly Collection Chart
        var collectionCtx = document.getElementById('collectionChart').getContext('2d');
        var collectionChart = new Chart(collectionCtx, {
            type: 'bar',
            data: {
                labels: @json($monthlyLabels),
                datasets: [{
                    label: 'Monthly Collections',
                    data: @json($monthlyData),
                    backgroundColor: 'rgba(60, 141, 188, 0.7)',
                    borderColor: 'rgba(60, 141, 188, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString();
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return '$' + context.parsed.y.toLocaleString();
                            }
                        }
                    }
                }
            }
        });

        // Fee Distribution Chart
        var feeDistributionCtx = document.getElementById('feeDistributionChart').getContext('2d');
        var feeDistributionChart = new Chart(feeDistributionCtx, {
            type: 'doughnut',
            data: {
                labels: @json($feeTypeLabels),
                datasets: [{
                    data: @json($feeTypeData),
                    backgroundColor: [
                        'rgba(60, 141, 188, 0.7)',
                        'rgba(255, 193, 7, 0.7)',
                        'rgba(40, 167, 69, 0.7)',
                        'rgba(220, 53, 69, 0.7)',
                        'rgba(108, 117, 125, 0.7)',
                        'rgba(23, 162, 184, 0.7)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                var label = context.label || '';
                                var value = '$' + context.parsed.toLocaleString();
                                return label + ': ' + value;
                            }
                        }
                    }
                }
            }
        });

        // Payment Methods Chart
        var paymentMethodsCtx = document.getElementById('paymentMethodsChart').getContext('2d');
        var paymentMethodsChart = new Chart(paymentMethodsCtx, {
            type: 'pie',
            data: {
                labels: @json($paymentMethodLabels),
                datasets: [{
                    data: @json($paymentMethodData),
                    backgroundColor: [
                        'rgba(40, 167, 69, 0.7)',
                        'rgba(23, 162, 184, 0.7)',
                        'rgba(108, 117, 125, 0.7)',
                        'rgba(255, 193, 7, 0.7)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                var label = context.label || '';
                                var value = '$' + context.parsed.toLocaleString();
                                var percentage = (context.parsed / context.dataset.data.reduce((a, b) => a + b, 0) * 100).toFixed(1) + '%';
                                return label + ': ' + value + ' (' + percentage + ')';
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