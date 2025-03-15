@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Academic Years</h1>
        <a href="{{ route('academic-years.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Academic Year
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">All Academic Years</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Year Name</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($academicYears as $year)
                            <tr>
                                <td>{{ $year->academic_year_id }}</td>
                                <td>{{ $year->year_name }}</td>
                                <td>{{ $year->start_date->format('M d, Y') }}</td>
                                <td>{{ $year->end_date->format('M d, Y') }}</td>
                                <td>
                                    @if($year->is_current)
                                        <span class="badge bg-success">Current</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('academic-years.edit', $year->academic_year_id) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('academic-years.destroy', $year->academic_year_id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this academic year?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                {{ $academicYears->links() }}
            </div>
        </div>
    </div>
</div>
@endsection