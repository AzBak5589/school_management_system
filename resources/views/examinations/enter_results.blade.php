@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Enter Examination Results</h1>
        <div>
            <a href="{{ route('examinations.class_results', [$examination->exam_id, $class->class_id]) }}" class="btn btn-info text-white">
                <i class="fas fa-chart-bar"></i> View Class Results
            </a>
            <a href="{{ route('examinations.results', $examination->exam_id) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Classes
            </a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                {{ $examination->exam_name }} - {{ $class->class_name }} - {{ $class->section }} | Subject: {{ $subject->subject_name }}
            </h6>
        </div>
        <div class="card-body">
            @if($students->count() > 0)
                <form action="{{ route('examinations.results.store', [$examination->exam_id, $class->class_id, $subject->subject_id]) }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="total_marks" class="form-label">Total Marks for this Exam *</label>
                        <input type="number" class="form-control @error('total_marks') is-invalid @enderror" id="total_marks" name="total_marks" value="{{ old('total_marks', $results->count() > 0 ? $results->first()->total_marks : 100) }}" required min="1">
                        @error('total_marks')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Admission Number</th>
                                    <th>Marks Obtained</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($students as $student)
                                    <tr>
                                        <td>{{ $student->full_name }}</td>
                                        <td>{{ $student->admission_number }}</td>
                                        <td>
                                            <input type="number" class="form-control @error('marks.' . $student->student_id) is-invalid @enderror" 
                                                name="marks[{{ $student->student_id }}]" 
                                                value="{{ old('marks.' . $student->student_id, isset($results[$student->student_id]) ? $results[$student->student_id]->marks_obtained : '') }}"
                                                min="0" required>
                                            @error('marks.' . $student->student_id)
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" 
                                                name="remarks[{{ $student->student_id }}]" 
                                                value="{{ old('remarks.' . $student->student_id, isset($results[$student->student_id]) ? $results[$student->student_id]->remarks : '') }}"
                                                placeholder="Optional remarks">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Results
                        </button>
                    </div>
                </form>
            @else
                <div class="alert alert-info">
                    No students are enrolled in this class.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection