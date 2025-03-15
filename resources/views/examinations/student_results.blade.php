@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">My Examination Results</h1>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Student Information</h6>
                </div>
                <div class="card-body">
                    <h5 class="font-weight-bold">{{ $student->full_name }}</h5>
                    <p><strong>Admission Number:</strong> {{ $student->admission_number }}</p>
                    
                    <hr>
                    
                    <div class="mb-3">
                        <label for="exam_id" class="form-label">Select Examination</label>
                        <form action="{{ route('examinations.student_results') }}" method="GET">
                            <select class="form-select" id="exam_id" name="exam_id" onchange="this.form.submit()">
                                @foreach($examinations as $exam)
                                    <option value="{{ $exam->exam_id }}" {{ isset($examination) && $examination->exam_id == $exam->exam_id ? 'selected' : '' }}>
                                        {{ $exam->exam_name }} ({{ $exam->academicYear->year_name }})
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    </div>
                    
                    @if(isset($examination) && isset($totalPercentage))
                        <div class="text-center mt-4">
                            <div class="progress mb-2" style="height: 25px;">
                                <div class="progress-bar {{ $totalPercentage >= 60 ? 'bg-success' : ($totalPercentage >= 40 ? 'bg-warning' : 'bg-danger') }}" 
                                    role="progressbar" 
                                    style="width: {{ $totalPercentage }}%;" 
                                    aria-valuenow="{{ $totalPercentage }}" 
                                    aria-valuemin="0" 
                                    aria-valuemax="100">
                                    {{ $totalPercentage }}%
                                </div>
                            </div>
                            <p class="mb-0"><strong>Overall Performance:</strong> {{ $totalMarksObtained }} / {{ $totalMaxMarks }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        @if(isset($examination))
                            {{ $examination->exam_name }} Results
                        @else
                            Examination Results
                        @endif
                    </h6>
                </div>
                <div class="card-body">
                    @if(isset($examination))
                        @if($results->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Subject</th>
                                            <th>Marks Obtained</th>
                                            <th>Total Marks</th>
                                            <th>Percentage</th>
                                            <th>Grade</th>
                                            <th>Remarks</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($results as $result)
                                            <tr>
                                                <td>{{ $result->subject->subject_name }}</td>
                                                <td>{{ $result->marks_obtained }}</td>
                                                <td>{{ $result->total_marks }}</td>
                                                <td>{{ round(($result->marks_obtained / $result->total_marks) * 100, 2) }}%</td>
                                                <td>
                                                    <span class="badge {{ 
                                                        $result->grade === 'A+' || $result->grade === 'A' ? 'bg-success' : 
                                                        ($result->grade === 'B+' || $result->grade === 'B' ? 'bg-primary' : 
                                                        ($result->grade === 'C+' || $result->grade === 'C' ? 'bg-info' : 
                                                        ($result->grade === 'D' ? 'bg-warning' : 'bg-danger'))) 
                                                    }}">
                                                        {{ $result->grade }}
                                                    </span>
                                                </td>
                                                <td>{{ $result->remarks ?: 'No remarks' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info">
                                No results have been entered for you in this examination yet.
                            </div>
                        @endif
                    @else
                        <div class="alert alert-info">
                            {{ $info ?? 'Please select an examination to view results.' }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection