@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Examinations</h1>
        @if(Auth::user()->role === 'admin')
            <a href="{{ route('examinations.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Examination
            </a>
        @endif
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">All Examinations</h6>
        </div>
        <div class="card-body">
            @if($examinations->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Examination</th>
                                <th>Academic Year</th>
                                <th>Period</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($examinations as $exam)
                                <tr>
                                    <td>{{ $exam->exam_name }}</td>
                                    <td>{{ $exam->academicYear->year_name }}</td>
                                    <td>{{ $exam->start_date->format('M d, Y') }} - {{ $exam->end_date->format('M d, Y') }}</td>
                                    <td>
                                        @php
                                            $now = \Carbon\Carbon::now();
                                            if ($now->lt($exam->start_date)) {
                                                echo '<span class="badge bg-info">Upcoming</span>';
                                            } elseif ($now->gt($exam->end_date)) {
                                                echo '<span class="badge bg-success">Completed</span>';
                                            } else {
                                                echo '<span class="badge bg-warning text-dark">In Progress</span>';
                                            }
                                        @endphp
                                    </td>
                                    <td>
                                        <a href="{{ route('examinations.show', $exam->exam_id) }}" class="btn btn-sm btn-info text-white">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        @if(Auth::user()->role === 'admin')
                                            <a href="{{ route('examinations.schedules', $exam->exam_id) }}" class="btn btn-sm btn-secondary">
                                                <i class="fas fa-calendar-alt"></i>
                                            </a>
                                        @endif
                                        
                                        <a href="{{ route('examinations.results', $exam->exam_id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-chart-bar"></i>
                                        </a>
                                        
                                        @if(Auth::user()->role === 'admin')
                                            <a href="{{ route('examinations.edit', $exam->exam_id) }}" class="btn btn-sm btn-warning text-dark">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            
                                            <form action="{{ route('examinations.destroy', $exam->exam_id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this examination?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-4">
                    {{ $examinations->links() }}
                </div>
            @else
                <div class="alert alert-info">
                    No examinations found.
                    @if(Auth::user()->role === 'admin')
                        <a href="{{ route('examinations.create') }}">Create one now</a>.
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection