<!-- resources/views/fees/bulk/import.blade.php -->
@extends('layouts.app')

@section('title', 'Import Fees')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Import Fees from CSV</h3>
                    <div class="card-tools">
                        <a href="{{ route('fees.bulk.index') }}" class="btn btn-default btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Bulk Operations
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

                    <div class="alert alert-info">
                        <h5><i class="icon fas fa-info"></i> Instructions</h5>
                        <p>To import fees, prepare a CSV file with the following columns:</p>
                        <ul>
                            <li><strong>student_id</strong> - The ID of the student</li>
                            <li><strong>fee_type</strong> - The name of the fee type</li>
                            <li><strong>amount</strong> - The fee amount</li>
                            <li><strong>due_date</strong> - The due date in YYYY-MM-DD format</li>
                        </ul>
                        <p>You can download a <a href="{{ route('fees.bulk.download-template') }}">sample template</a> to get started.</p>
                    </div>

                    <form action="{{ route('fees.bulk.import-process') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="fee_file">CSV File</label>
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="fee_file" name="fee_file" accept=".csv,.txt" required>
                                    <label class="custom-file-label" for="fee_file">Choose file</label>
                                </div>
                            </div>
                            @error('fee_file')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                            <small class="text-muted">Maximum file size: 2MB. Accepted formats: CSV, TXT.</small>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="skip_duplicates" name="skip_duplicates" checked>
                                <label class="custom-control-label" for="skip_duplicates">Skip duplicate fees (same student and fee type)</label>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-upload"></i> Upload and Import
                            </button>
                        </div>
                    </form>

                    <div class="mt-4">
                        <h4>Sample CSV Format</h4>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>student_id</th>
                                        <th>fee_type</th>
                                        <th>amount</th>
                                        <th>due_date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1001</td>
                                        <td>Tuition Fee</td>
                                        <td>500.00</td>
                                        <td>2025-04-15</td>
                                    </tr>
                                    <tr>
                                        <td>1002</td>
                                        <td>Library Fee</td>
                                        <td>100.00</td>
                                        <td>2025-04-15</td>
                                    </tr>
                                    <tr>
                                        <td>1003</td>
                                        <td>Lab Fee</td>
                                        <td>150.00</td>
                                        <td>2025-04-15</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Update file input label with selected filename
    $(document).ready(function () {
        bsCustomFileInput.init();
    });
</script>
@endpush
@endsection