<!-- resources/views/fees/types/create.blade.php -->
@extends('layouts.app')

@section('title', 'Add Fee Type')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Add New Fee Type</h3>
                    <div class="card-tools">
                        <a href="{{ route('fees.types.index') }}" class="btn btn-default btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Fee Types
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('fees.types.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="type_name">Fee Type Name<span class="text-danger">*</span></label>
                            <input type="text" name="type_name" id="type_name" class="form-control @error('type_name') is-invalid @enderror" value="{{ old('type_name') }}" required>
                            @error('type_name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Fee Type
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection