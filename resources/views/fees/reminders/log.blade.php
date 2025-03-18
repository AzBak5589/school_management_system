<!-- resources/views/fees/reminders/log.blade.php -->
@extends('layouts.app')

@section('title', 'Fee Reminder Log')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Fee Reminder Log</h3>
                    <div class="card-tools">
                        <a href="{{ route('fees.reminders.index') }}" class="btn btn-default btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Reminders
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Date & Time</th>
                                    <th>Student</th>
                                    <th>Type</th>
                                    <th>Amount Due</th>
                                    <th>Sent By</th>
                                    <th>Method</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reminders as $reminder)
                                    <tr>
                                        <td>{{ $reminder->reminder_date->format('d-m-Y H:i') }}</td>
                                        <td>
                                            <a href="{{ route('admin.students.fees', $reminder->student->student_id) }}">
                                                {{ $reminder->student->first_name }} {{ $reminder->student->last_name }}
                                            </a>
                                        </td>
                                        <td>
                                            @if($reminder->reminder_type == 'overdue')
                                                <span class="badge badge-danger">Overdue</span>
                                            @elseif($reminder->reminder_type == 'upcoming')
                                                <span class="badge badge-warning">Upcoming</span>
                                            @elseif($reminder->reminder_type == 'individual')
                                                <span class="badge badge-info">Individual</span>
                                            @else
                                                <span class="badge badge-secondary">All</span>
                                            @endif
                                        </td>
                                        <td>{{ number_format($reminder->amount_due, 2) }}</td>
                                        <td>{{ $reminder->sentBy->name }}</td>
                                        <td>{{ $reminder->sent_via }}</td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#viewMessageModal{{ $reminder->reminder_id }}">
                                                <i class="fas fa-eye"></i> View Message
                                            </button>
                                            <a href="{{ route('fees.reminders.student', $reminder->student->student_id) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-paper-plane"></i> Send Again
                                            </a>
                                        </td>
                                    </tr>
                                    
                                    <!-- View Message Modal -->
                                    <div class="modal fade" id="viewMessageModal{{ $reminder->reminder_id }}" tabindex="-1" role="dialog" aria-labelledby="viewMessageModalLabel{{ $reminder->reminder_id }}" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="viewMessageModalLabel{{ $reminder->reminder_id }}">Reminder Message</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label>Sent on:</label>
                                                        <p>{{ $reminder->reminder_date->format('d-m-Y H:i:s') }}</p>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Message:</label>
                                                        <div class="p-3 bg-light">
                                                            {!! nl2br(e($reminder->message)) !!}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No reminder logs found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    {{ $reminders->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection