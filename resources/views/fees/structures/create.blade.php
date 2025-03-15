<!-- resources/views/fees/structures/create.blade.php -->
@extends('layouts.app')

@section('title', 'Add Fee Structure')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Add New Fee Structure</h3>
                    <div class="card-tools">
                        <a href="{{ route('fees.structures.index') }}" class="btn btn-default btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Fee Structures
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('fees.structures.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="academic_year_id">Academic Year<span class="text-danger">*</span></label>
                            <select name="academic_year_id" id="academic_year_id" class="form-control @error('academic_year_id') is-invalid @enderror" required onchange="loadClasses()">
                                <option value="">Select Academic Year</option>
                                @foreach($academicYears as $year)
                                    <option value="{{ $year->academic_year_id }}" {{ old('academic_year_id') == $year->academic_year_id ? 'selected' : '' }}>
                                        {{ $year->year_name }} {{ $year->is_current ? '(Current)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('academic_year_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="class_id">Class<span class="text-danger">*</span></label>
                            <select name="class_id" id="class_id" class="form-control @error('class_id') is-invalid @enderror" required>
                                <option value="">Select Class</option>
                                <!-- Classes will be loaded dynamically -->
                            </select>
                            @error('class_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="fee_type_id">Fee Type<span class="text-danger">*</span></label>
                            <select name="fee_type_id" id="fee_type_id" class="form-control @error('fee_type_id') is-invalid @enderror" required>
                                <option value="">Select Fee Type</option>
                                @foreach($feeTypes as $type)
                                    <option value="{{ $type->fee_type_id }}" {{ old('fee_type_id') == $type->fee_type_id ? 'selected' : '' }}>
                                        {{ $type->type_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('fee_type_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="amount">Amount<span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">$</span>
                                </div>
                                <input type="number" name="amount" id="amount" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount') }}" step="0.01" min="0" required>
                            </div>
                            @error('amount')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Fee Structure
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function loadClasses() {
        var academicYearId = document.getElementById('academic_year_id').value;
        var classDropdown = document.getElementById('class_id');
        
        // Clear existing options
        classDropdown.innerHTML = '<option value="">Select Class</option>';
        
        if (academicYearId) {
            // Fetch classes for the selected academic year
            fetch('{{ url("fees/structures/get-classes") }}/' + academicYearId)
                .then(response => response.json())
                .then(data => {
                    data.forEach(function(item) {
                        var option = document.createElement('option');
                        option.value = item.id;
                        option.textContent = item.name;
                        classDropdown.appendChild(option);
                    });
                })
                .catch(error => console.error('Error loading classes:', error));
        }
    }
    
    // Load classes if academic year is already selected
    document.addEventListener('DOMContentLoaded', function() {
        if (document.getElementById('academic_year_id').value) {
            loadClasses();
        }
    });
</script>
@endpush
@endsection