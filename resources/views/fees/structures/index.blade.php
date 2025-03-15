<!-- resources/views/fees/structures/index.blade.php -->
@extends('layouts.app')

@section('title', 'Fee Structures')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Fee Structures</h3>
                    <div class="card-tools">
                        <a href="{{ route('fees.structures.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Add New Fee Structure
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

                    <form action="{{ route('fees.structures.index') }}" method="GET" class="mb-4">
                        <div class="row">
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="academic_year">Academic Year</label>
                                    <select name="academic_year" id="academic_year" class="form-control" onchange="this.form.submit()">
                                        @foreach($academicYears as $year)
                                            <option value="{{ $year->academic_year_id }}" {{ $academicYearId == $year->academic_year_id ? 'selected' : '' }}>
                                                {{ $year->year_name }} {{ $year->is_current ? '(Current)' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="class_id">Class</label>
                                    <select name="class_id" id="class_id" class="form-control" onchange="this.form.submit()">
                                        <option value="">All Classes</option>
                                        @foreach($classes as $class)
                                            <option value="{{ $class->class_id }}" {{ $classId == $class->class_id ? 'selected' : '' }}>
                                                {{ $class->class_name }} - {{ $class->section }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="btn btn-primary form-control">
                                        <i class="fas fa-filter"></i> Filter
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Class</th>
                                    <th>Fee Type</th>
                                    <th>Amount</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($feeStructures as $structure)
                                    <tr>
                                        <td>{{ $structure->fee_structure_id }}</td>
                                        <td>{{ $structure->class->class_name }} - {{ $structure->class->section }}</td>
                                        <td>{{ $structure->feeType->type_name }}</td>
                                        <td>{{ number_format($structure->amount, 2) }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('fees.structures.edit', $structure->fee_structure_id) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                <a href="{{ route('fees.structures.apply-to-students', $structure->fee_structure_id) }}" class="btn btn-sm btn-success">
                                                    <i class="fas fa-users"></i> Apply to Students
                                                </a>
                                                <form action="{{ route('fees.structures.destroy', $structure->fee_structure_id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this fee structure?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No fee structures found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    {{ $feeStructures->appends(request()->except('page'))->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection