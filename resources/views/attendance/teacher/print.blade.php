<!-- resources/views/attendance/teacher/print.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Attendance Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ddd;
        }
        .school-name {
            font-size: 24px;
            font-weight: bold;
            margin: 5px 0;
        }
        .report-title {
            font-size: 18px;
            margin: 5px 0;
        }
        .report-period {
            font-size: 14px;
            margin: 5px 0;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .summary-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .summary-box {
            width: 23%;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            color: white;
        }
        .present {
            background-color: #28a745;
        }
        .absent {
            background-color: #dc3545;
        }
        .late {
            background-color: #ffc107;
            color: #333;
        }
        .leave {
            background-color: #17a2b8;
        }
        .badge {
            display: inline-block;
            padding: 3px 7px;
            border-radius: 4px;
            font-size: 12px;
        }
        .badge-success {
            background-color: #28a745;
            color: white;
        }
        .badge-danger {
            background-color: #dc3545;
            color: white;
        }
        .badge-warning {
            background-color: #ffc107;
            color: #333;
        }
        .badge-info {
            background-color: #17a2b8;
            color: white;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        @media print {
            body {
                padding: 0;
                margin: 0;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="school-name">{{ config('app.name') }}</h1>
        <h2 class="report-title">Teacher Attendance Report</h2>
        <p class="report-period">Period: {{ $startDate->format('d-m-Y') }} to {{ $endDate->format('d-m-Y') }}</p>
        
        @if($teacherId)
            <p>Teacher: {{ $selectedTeacher->user->name }} ({{ $selectedTeacher->department }})</p>
        @endif
        
        @if($status)
            <p>Status Filter: {{ ucfirst($status) }}</p>
        @endif
    </div>

    @if($teacherId && $selectedTeacher)
        <div class="summary-container">
            <div class="summary-box present">
                <h3>Present</h3>
                <div style="font-size: 24px; font-weight: bold;">{{ $summary['present'] ?? 0 }}</div>
                <div>{{ $summary['present_percentage'] ?? 0 }}%</div>
            </div>
            <div class="summary-box absent">
                <h3>Absent</h3>
                <div style="font-size: 24px; font-weight: bold;">{{ $summary['absent'] ?? 0 }}</div>
                <div>{{ $summary['absent_percentage'] ?? 0 }}%</div>
            </div>
            <div class="summary-box late">
                <h3>Late</h3>
                <div style="font-size: 24px; font-weight: bold;">{{ $summary['late'] ?? 0 }}</div>
                <div>{{ $summary['late_percentage'] ?? 0 }}%</div>
            </div>
            <div class="summary-box leave">
                <h3>Leave</h3>
                <div style="font-size: 24px; font-weight: bold;">{{ $summary['leave'] ?? 0 }}</div>
                <div>{{ $summary['leave_percentage'] ?? 0 }}%</div>
            </div>
        </div>
    @endif

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Teacher</th>
                <th>Department</th>
                <th>Status</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            @forelse($attendanceRecords as $record)
                <tr>
                    <td>{{ $record->date->format('d-m-Y') }}</td>
                    <td>{{ $record->teacher->user->name }}</td>
                    <td>{{ $record->teacher->department }}</td>
                    <td>
                        @if($record->status == 'present')
                            <span class="badge badge-success">Present</span>
                        @elseif($record->status == 'absent')
                            <span class="badge badge-danger">Absent</span>
                        @elseif($record->status == 'late')
                            <span class="badge badge-warning">Late</span>
                        @elseif($record->status == 'leave')
                            <span class="badge badge-info">Leave</span>
                        @endif
                    </td>
                    <td>{{ $record->remarks }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center;">No attendance records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if(!$teacherId && count($overallSummary) > 0)
        <h3>Summary by Teacher</h3>
        <table>
            <thead>
                <tr>
                    <th>Teacher</th>
                    <th>Department</th>
                    <th>Present</th>
                    <th>Absent</th>
                    <th>Late</th>
                    <th>Leave</th>
                    <th>Attendance Rate</th>
                </tr>
            </thead>
            <tbody>
                @foreach($overallSummary as $teacherName => $data)
                    <tr>
                        <td>{{ $teacherName }}</td>
                        <td>{{ $data['department'] ?? '' }}</td>
                        <td>{{ $data['present'] ?? 0 }}</td>
                        <td>{{ $data['absent'] ?? 0 }}</td>
                        <td>{{ $data['late'] ?? 0 }}</td>
                        <td>{{ $data['leave'] ?? 0 }}</td>
                        <td>
                            @php
                                $total = ($data['present'] ?? 0) + ($data['absent'] ?? 0) + ($data['late'] ?? 0) + ($data['leave'] ?? 0);
                                $attendanceRate = $total > 0 ? round((($data['present'] ?? 0) / $total) * 100, 1) : 0;
                            @endphp
                            {{ $attendanceRate }}%
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="footer">
        <p>Report generated on {{ date('d-m-Y H:i:s') }}</p>
        <p>{{ config('app.name') }} - Teacher Attendance Report</p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>