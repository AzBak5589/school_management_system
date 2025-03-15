@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Timetable: {{ $class->class_name }} - {{ $class->section }}</h1>
        <div>
            @if(Auth::user()->role === 'admin')
                <a href="{{ route('timetable.create', $class->class_id) }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add Class Period
                </a>
            @endif
            <a href="{{ route('timetable.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Classes
            </a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                Weekly Schedule for {{ $class->class_name }} - {{ $class->section }}
                ({{ $class->academicYear->year_name }})
                @if($class->academicYear->is_current)
                    <span class="badge bg-success">Current</span>
                @endif
            </h6>
        </div>
        <div class="card-body">
            @if(count($timetableByDay) > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                        <thead class="bg-light">
                            <tr>
                                <th>Time</th>
                                <th>Subject</th>
                                <th>Teacher</th>
                                <th>Room</th>
                                @if(Auth::user()->role === 'admin')
                                    <th>Actions</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @for($day = 1; $day <= 7; $day++)
                                <tr class="bg-primary text-white">
                                    <th colspan="{{ Auth::user()->role === 'admin' ? 5 : 4 }}">
                                        @php
                                            $dayNames = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                                            echo $dayNames[$day - 1];
                                        @endphp
                                    </th>
                                </tr>
                                
                                @if(isset($timetableByDay[$day]) && count($timetableByDay[$day]) > 0)
                                    @foreach($timetableByDay[$day] as $entry)
                                        <tr>
                                            <td>{{ $entry->start_time->format('h:i A') }} - {{ $entry->end_time->format('h:i A') }}</td>
                                            <td>{{ $entry->classSubject->subject->subject_name }}</td>
                                            <td>{{ $entry->classSubject->teacher->full_name }}</td>
                                            <td>{{ $entry->room ?: 'Not specified' }}</td>
                                            @if(Auth::user()->role === 'admin')
                                                <td>
                                                    <a href="{{ route('timetable.edit', [$class->class_id, $entry->timetable_id]) }}" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('timetable.destroy', [$class->class_id, $entry->timetable_id]) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this timetable entry?')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="{{ Auth::user()->role === 'admin' ? 5 : 4 }}" class="text-center">No classes scheduled for this day.</td>
                                    </tr>
                                @endif
                            @endfor
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info">
                    No timetable entries found for this class.
                    @if(Auth::user()->role === 'admin')
                        <a href="{{ route('timetable.create', $class->class_id) }}">Add a class period now</a>.
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection