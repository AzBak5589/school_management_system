@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Classes</h1>
        <a href="{{ route('classes.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Class
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">All Classes</h6>
            
            <!-- Academic Year Filter -->
            <form action="{{ route('classes.index') }}" method="GET" class="form-inline">
                <div class="input-group">
                    <select class="form-select" name="academic_year" onchange="this.form.submit()">
                        <option value="">All Academic Years</option>
                        @foreach($academicYears as $year)
                            <option value="{{ $year->academic_year_id }}" {{ $academicYearId == $year->academic_year_id ? 'selected' : '' }}>
                                {{ $year->year_name }} {{ $year->is_current ? '(Current)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Class</th>
                            <th>Section</th>
                            <th>Academic Year</th>
                            <th>Capacity</th>
                            <th>Class Teacher</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($classes as $class)
                            <tr>
                                <td>{{ $class->class_name }}</td>
                                <td>{{ $class->section }}</td>
                                <td>
                                    {{ $class->academicYear->year_name }}
                                    @if($class->academicYear->is_current)
                                        <span class="badge bg-success">Current</span>
                                    @endif
                                </td>
                                <td>{{ $class->capacity }}</td>
                                <td>
                                    @if($class->classTeacher)
                                        {{ $class->classTeacher->full_name }}
                                    @else
                                        <em>Not assigned</em>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('classes.students', $class->class_id) }}" class="btn btn-sm btn-info text-white">
                                        <i class="fas fa-users"></i> Students
                                    </a>
                                    <a href="{{ route('classes.subjects.index', $class->class_id) }}" class="btn btn-sm btn-success text-white">
                                        <i class="fas fa-book"></i> Subjects
                                    </a>
                                    <a href="{{ route('classes.edit', $class->class_id) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('classes.destroy', $class->class_id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this class?')">
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
                {{ $classes->appends(['academic_year' => $academicYearId])->links() }}
            </div>
        </div>
    </div>
</div>
@endsection