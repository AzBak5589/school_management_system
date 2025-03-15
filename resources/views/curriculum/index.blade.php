@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Curriculum</h1>
        @if(Auth::user()->role === 'admin' || Auth::user()->role === 'teacher')
            <a href="{{ route('curriculum.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Curriculum Item
            </a>
        @endif
    </div>

    <!-- Filter Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Curriculum</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('curriculum.index') }}" method="GET" class="row">
                <div class="col-md-5 mb-3">
                    <label for="class_id" class="form-label">Class</label>
                    <select class="form-select" id="class_id" name="class_id">
                        <option value="">All Classes</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->class_id }}" {{ $classId == $class->class_id ? 'selected' : '' }}>
                                {{ $class->class_name }} - {{ $class->section }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-5 mb-3">
                    <label for="subject_id" class="form-label">Subject</label>
                    <select class="form-select" id="subject_id" name="subject_id">
                        <option value="">All Subjects</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->subject_id }}" {{ $subjectId == $subject->subject_id ? 'selected' : '' }}>
                                {{ $subject->subject_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-2 mb-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Curriculum Items</h6>
        </div>
        <div class="card-body">
            @if($curriculumItems->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Class</th>
                                <th>Subject</th>
                                <th>Teacher</th>
                                <th>Created By</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($curriculumItems as $item)
                                <tr>
                                    <td>{{ $item->title }}</td>
                                    <td>{{ $item->classSubject->class->class_name }} - {{ $item->classSubject->class->section }}</td>
                                    <td>{{ $item->classSubject->subject->subject_name }}</td>
                                    <td>{{ $item->classSubject->teacher->full_name }}</td>
                                    <td>{{ $item->creator->username }}</td>
                                    <td>
                                        <a href="{{ route('curriculum.show', $item->curriculum_id) }}" class="btn btn-sm btn-info text-white">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        @if(Auth::user()->role === 'admin' || 
                                            (Auth::user()->role === 'teacher' && 
                                            (Auth::user()->teacher->teacher_id === $item->classSubject->teacher_id || 
                                            Auth::id() === $item->created_by)))
                                            <a href="{{ route('curriculum.edit', $item->curriculum_id) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            
                                            <form action="{{ route('curriculum.destroy', $item->curriculum_id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this curriculum item?')">
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
                    {{ $curriculumItems->appends(['class_id' => $classId, 'subject_id' => $subjectId])->links() }}
                </div>
            @else
                <div class="alert alert-info">
                    No curriculum items found. 
                    @if(Auth::user()->role === 'admin' || Auth::user()->role === 'teacher')
                        <a href="{{ route('curriculum.create') }}">Create one now</a>.
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection