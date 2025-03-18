<!DOCTYPE html>
<html>
<head>
    <title>Payment Report</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Payment Report</h1>
    <p>From {{ $startDate }} to {{ $endDate }}</p>
    @if($paymentMethod)
        <p>Payment Method: {{ ucfirst($paymentMethod) }}</p>
    @endif

    <table>
        <thead>
            <tr>
                <th>Receipt No</th>
                <th>Student Name</th>
                <th>Fee Type</th>
                <th>Amount</th>
                <th>Payment Method</th>
                <th>Payment Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payments as $payment)
                <tr>
                    <td>{{ $payment->receipt_number }}</td>
                    <td>{{ $payment->studentFee->student->full_name ?? 'N/A' }}</td>
                    <td>{{ $payment->studentFee->feeStructure->feeType->type_name ?? 'N/A' }}</td>
                    <td>{{ number_format($payment->amount_paid, 2) }}</td>
                    <td>{{ $payment->payment_method }}</td>
                    <td>{{ $payment->payment_date->format('Y-m-d') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>