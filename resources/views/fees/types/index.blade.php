<!-- resources/views/fees/types/index.blade.php -->
@extends('layouts.app')

@section('title', 'Fee Types')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Fee Types</h3>
                    <div class="card-tools">
                        <a href="{{ route('fees.types.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Add New Fee Type
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

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Fee Type</th>
                                    <th>Description</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($feeTypes as $feeType)
                                    <tr>
                                        <td>{{ $feeType->fee_type_id }}</td>
                                        <td>{{ $feeType->type_name }}</td>
                                        <td>{{ $feeType->description }}</td>
                                        <td>
                                            <a href="{{ route('fees.types.edit', $feeType->fee_type_id) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <form action="{{ route('fees.types.destroy', $feeType->fee_type_id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this fee type?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">No fee types found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    {{ $feeTypes->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

