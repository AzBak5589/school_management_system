<!-- resources/views/fees/bulk/index.blade.php -->
@extends('layouts.app')

@section('title', 'Bulk Fee Operations')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Bulk Fee Operations</h3>
                    <div class="card-tools">
                        <a href="{{ route('fees.bulk.import-form') }}" class="btn btn-info btn-sm">
                            <i class="fas fa-upload"></i> Import Fees
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
                    @if(session('import_errors'))
                        <div class="alert alert-warning">
                            <h5><i class="icon fas fa-exclamation-triangle"></i> Import Errors</h5>
                            <ul>
                                @foreach(session('import_errors') as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('fees.bulk.process') }}" method="POST">
                        @csrf
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Bulk Operations</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="operation_type">Operation Type</label>
                                    <select name="operation_type" id="operation_type" class="form-control" required onchange="showOperationFields()">
                                        <option value="">Select Operation</option>
                                        <option value="assign">Assign New Fees</option>
                                        <option value="update">Update Existing Fees</option>
                                        <option value="remove">Remove Fees</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="class_id">Class</label>
                                    <select name="class_id" id="class_id" class="form-control" required>
                                        <option value="">Select Class</option>
                                        @foreach($classes as $class)
                                            <option value="{{ $class->class_id }}">{{ $class->class_name }} - {{ $class->section }}</option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">The operation will be applied to all students in the selected class.</small>
                                </div>

                                <div class="form-group" id="fee_type_group">
                                    <label for="fee_type_id">Fee Type</label>
                                    <select name="fee_type_id" id="fee_type_id" class="form-control">
                                        <option value="">All Fee Types</option>
                                        @foreach($feeTypes as $type)
                                            <option value="{{ $type->fee_type_id }}">{{ $type->type_name }}</option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted" id="fee_type_help">For update/remove operations, you can select a specific fee type or leave blank to apply to all fee types.</small>
                                </div>

                                <!-- Fields for Assign operation -->
                                <div id="assign_fields" style="display: none;">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="amount">Fee Amount</label>
                                                <input type="number" name="amount" id="amount" class="form-control" step="0.01" min="0">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="due_date">Due Date</label>
                                                <input type="date" name="due_date" id="due_date" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Fields for Update operation -->
                                <div id="update_fields" style="display: none;">
                                    <div class="form-group">
                                        <label for="update_field">Field to Update</label>
                                        <select name="update_field" id="update_field" class="form-control" onchange="showUpdateField()">
                                            <option value="">Select Field</option>
                                            <option value="amount">Amount</option>
                                            <option value="due_date">Due Date</option>
                                            <option value="status">Status</option>
                                        </select>
                                    </div>

                                    <div id="new_amount_group" style="display: none;">
                                        <div class="form-group">
                                            <label for="new_amount">New Amount</label>
                                            <input type="number" name="new_amount" id="new_amount" class="form-control" step="0.01" min="0">
                                        </div>
                                    </div>

                                    <div id="new_due_date_group" style="display: none;">
                                        <div class="form-group">
                                            <label for="new_due_date">New Due Date</label>
                                            <input type="date" name="new_due_date" id="new_due_date" class="form-control">
                                        </div>
                                    </div>

                                    <div id="new_status_group" style="display: none;">
                                        <div class="form-group">
                                            <label for="new_status">New Status</label>
                                            <select name="new_status" id="new_status" class="form-control">
                                                <option value="unpaid">Unpaid</option>
                                                <option value="partially_paid">Partially Paid</option>
                                                <option value="paid">Paid</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Fields for Remove operation -->
                                <div id="remove_fields" style="display: none;">
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        <b>Warning!</b> This will remove all fee records for the selected class/fee type.
                                        Only fees without any payment records will be removed.
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary" id="submit_btn" disabled>
                                    <i class="fas fa-cogs"></i> Process Bulk Operation
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function showOperationFields() {
        var operation = document.getElementById('operation_type').value;
        var assignFields = document.getElementById('assign_fields');
        var updateFields = document.getElementById('update_fields');
        var removeFields = document.getElementById('remove_fields');
        var feeTypeGroup = document.getElementById('fee_type_group');
        var feeTypeHelp = document.getElementById('fee_type_help');
        var submitBtn = document.getElementById('submit_btn');
        var feeTypeSelect = document.getElementById('fee_type_id');
        
        // Hide all fields first
        assignFields.style.display = 'none';
        updateFields.style.display = 'none';
        removeFields.style.display = 'none';
        
        // Reset any sub-selections
        document.getElementById('update_field').value = '';
        document.getElementById('new_amount_group').style.display = 'none';
        document.getElementById('new_due_date_group').style.display = 'none';
        document.getElementById('new_status_group').style.display = 'none';
        
        // Enable submit button if an operation is selected
        submitBtn.disabled = (operation === '');
        
        // Show fields based on operation
        if (operation === 'assign') {
            assignFields.style.display = 'block';
            feeTypeSelect.required = true;
            feeTypeHelp.textContent = 'Select the fee type to assign.';
        } else if (operation === 'update') {
            updateFields.style.display = 'block';
            feeTypeSelect.required = false;
            feeTypeHelp.textContent = 'For update operations, you can select a specific fee type or leave blank to apply to all fee types.';
        } else if (operation === 'remove') {
            removeFields.style.display = 'block';
            feeTypeSelect.required = false;
            feeTypeHelp.textContent = 'For remove operations, you can select a specific fee type or leave blank to remove all fee types.';
        }
    }
    
    function showUpdateField() {
        var field = document.getElementById('update_field').value;
        var amountGroup = document.getElementById('new_amount_group');
        var dueDateGroup = document.getElementById('new_due_date_group');
        var statusGroup = document.getElementById('new_status_group');
        
        // Hide all fields first
        amountGroup.style.display = 'none';
        dueDateGroup.style.display = 'none';
        statusGroup.style.display = 'none';
        
        // Show the selected field
        if (field === 'amount') {
            amountGroup.style.display = 'block';
        } else if (field === 'due_date') {
            dueDateGroup.style.display = 'block';
        } else if (field === 'status') {
            statusGroup.style.display = 'block';
        }
    }
</script>
@endpush
@endsection