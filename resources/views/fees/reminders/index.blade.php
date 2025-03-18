<!-- resources/views/fees/reminders/index.blade.php -->
@extends('layouts.app')

@section('title', 'Fee Reminders')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Fee Reminders</h3>
                    <div class="card-tools">
                        <a href="{{ route('fees.reminders.log') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-history"></i> View Reminder Log
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
                        <div class="col-md-4">
                            <div class="small-box bg-danger">
                                <div class="inner">
                                    <h3>{{ $overdueFees }}</h3>
                                    <p>Overdue Fees</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-exclamation-circle"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3>{{ $upcomingDueFees }}</h3>
                                    <p>Due in Next 7 Days</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3>{{ $overdueFees + $upcomingDueFees }}</h3>
                                    <p>Total Pending</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-tasks"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('fees.reminders.send') }}" method="POST">
                        @csrf
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Send Reminders</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="reminder_type">Reminder Type</label>
                                            <select name="reminder_type" id="reminder_type" class="form-control" required>
                                                <option value="overdue">Overdue Fees</option>
                                                <option value="upcoming">Upcoming Fees (Due in 7 days)</option>
                                                <option value="all">All Pending Fees</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="class_id">Class (Optional)</label>
                                            <select name="class_id" id="class_id" class="form-control">
                                                <option value="">All Classes</option>
                                                @foreach($classes as $class)
                                                    <option value="{{ $class->class_id }}">{{ $class->class_name }} - {{ $class->section }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="fee_type_id">Fee Type (Optional)</label>
                                            <select name="fee_type_id" id="fee_type_id" class="form-control">
                                                <option value="">All Fee Types</option>
                                                @foreach($feeTypes as $type)
                                                    <option value="{{ $type->fee_type_id }}">{{ $type->type_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="subject">Email Subject</label>
                                    <input type="text" name="subject" id="subject" class="form-control" required value="Fee Payment Reminder - {{ config('app.name') }}">
                                </div>

                                <div class="form-group">
                                    <label for="message">Message</label>
                                    <textarea name="message" id="message" rows="5" class="form-control" required>Dear Parent/Guardian,

We would like to remind you that there are pending fee payments for your child. Please arrange to clear the outstanding balance at your earliest convenience.

If you have already made the payment, please disregard this reminder.

Thank you for your cooperation.

Regards,
{{ config('app.name') }}</textarea>
                                </div>

                                <div class="form-check mb-3">
                                    <input type="checkbox" class="form-check-input" id="send_sms" name="send_sms">
                                    <label class="form-check-label" for="send_sms">Also send SMS reminder (if available)</label>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary" onclick="return confirm('Are you sure you want to send these reminders?');">
                                    <i class="fas fa-paper-plane"></i> Send Reminders
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection