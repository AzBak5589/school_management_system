<!-- resources/views/fees/reminders/individual.blade.php -->
@extends('layouts.app')

@section('title', 'Send Individual Fee Reminder')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Send Individual Fee Reminder</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.students.fees', $student->student_id) }}" class="btn btn-default btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Student Fees
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

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Student Information</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table">
                                        <tr>
                                            <th style="width: 150px;">Student ID</th>
                                            <td>{{ $student->student_id }}</td>
                                        </tr>
                                        <tr>
                                            <th>Name</th>
                                            <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Class</th>
                                            <td>{{ $student->currentClass->class_name }} {{ $student->currentClass->section }}</td>
                                        </tr>
                                        <tr>
                                            <th>Email</th>
                                            <td>
                                                @if($student->email)
                                                    {{ $student->email }}
                                                @else
                                                    <span class="text-danger">No email address available.</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Phone</th>
                                            <td>
                                                @if($student->phone)
                                                    {{ $student->phone }}
                                                @else
                                                    <span class="text-muted">No phone number available.</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Fee Summary</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Fee Type</th>
                                                    <th>Due Date</th>
                                                    <th>Amount</th>
                                                    <th>Balance</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php 
                                                    $totalAmount = 0;
                                                    $totalBalance = 0;
                                                @endphp
                                                @foreach($fees as $fee)
                                                    @php
                                                        $paidAmount = $fee->payments->sum('amount_paid');
                                                        $balance = $fee->amount - $paidAmount;
                                                        $totalAmount += $fee->amount;
                                                        $totalBalance += $balance;
                                                    @endphp
                                                    <tr class="{{ $fee->is_overdue ? 'table-danger' : '' }}">
                                                        <td>{{ $fee->feeStructure->feeType->type_name }}</td>
                                                        <td>{{ $fee->due_date->format('d-m-Y') }}</td>
                                                        <td>{{ number_format($fee->amount, 2) }}</td>
                                                        <td>{{ number_format($balance, 2) }}</td>
                                                        <td>
                                                            @if($fee->status == 'paid')
                                                                <span class="badge badge-success">Paid</span>
                                                            @elseif($fee->status == 'partially_paid')
                                                                <span class="badge badge-warning">Partial</span>
                                                            @elseif($fee->is_overdue)
                                                                <span class="badge badge-danger">Overdue</span>
                                                            @else
                                                                <span class="badge badge-secondary">Unpaid</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                <tr class="font-weight-bold">
                                                    <td colspan="2">Total</td>
                                                    <td>{{ number_format($totalAmount, 2) }}</td>
                                                    <td>{{ number_format($totalBalance, 2) }}</td>
                                                    <td></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($student->email)
                        <form action="{{ route('fees.reminders.student.process', $student->student_id) }}" method="POST">
                            @csrf
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Compose Reminder</h5>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="subject">Email Subject</label>
                                        <input type="text" name="subject" id="subject" class="form-control" required value="Fee Payment Reminder - {{ config('app.name') }}">
                                    </div>

                                    <div class="form-group">
                                        <label for="message">Message</label>
                                        <textarea name="message" id="message" rows="5" class="form-control" required>Dear Parent/Guardian of {{ $student->first_name }} {{ $student->last_name }},

This is a reminder that there are pending fee payments amounting to {{ number_format($totalBalance, 2) }} for your child. Please arrange to clear the outstanding balance at your earliest convenience.

If you have already made the payment, please disregard this reminder.

Thank you for your cooperation.

Regards,
{{ config('app.name') }}</textarea>
                                    </div>

                                    <div class="form-check mb-3">
                                        <input type="checkbox" class="form-check-input" id="send_sms" name="send_sms" {{ $student->phone ? '' : 'disabled' }}>
                                        <label class="form-check-label" for="send_sms">Also send SMS reminder</label>
                                        @if(!$student->phone)
                                            <small class="text-muted d-block">No phone number available for SMS.</small>
                                        @endif
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-paper-plane"></i> Send Reminder
                                    </button>
                                </div>
                            </div>
                        </form>
                    @else
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> Cannot send email reminder. The student does not have an email address on file.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection