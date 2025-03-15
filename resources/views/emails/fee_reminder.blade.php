<!-- resources/views/emails/fee_reminder.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Fee Reminder</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        .logo {
            max-height: 80px;
            margin-bottom: 10px;
        }
        .school-name {
            font-size: 24px;
            font-weight: bold;
            margin: 0;
        }
        .subject {
            font-size: 20px;
            margin-bottom: 20px;
            color: #444;
        }
        .greeting {
            font-weight: bold;
            margin-bottom: 20px;
        }
        .message {
            margin-bottom: 20px;
        }
        .fee-details {
            margin-bottom: 20px;
            border-collapse: collapse;
            width: 100%;
        }
        .fee-details th, .fee-details td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .fee-details th {
            background-color: #f2f2f2;
        }
        .total-row {
            font-weight: bold;
        }
        .cta {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 15px;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            text-align: center;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ asset('images/school-logo.png') }}" alt="School Logo" class="logo">
            <h1 class="school-name">{{ config('app.name') }}</h1>
        </div>
        
        <h2 class="subject">{{ $subject }}</h2>
        
        <p class="greeting">Dear Parent/Guardian of {{ $student->first_name }} {{ $student->last_name }},</p>
        
        <div class="message">
            {!! nl2br(e($message)) !!}
        </div>
        
        <h3>Fee Details:</h3>
        <table class="fee-details">
            <thead>
                <tr>
                    <th>Fee Type</th>
                    <th>Due Date</th>
                    <th>Amount</th>
                    <th>Paid</th>
                    <th>Balance</th>
                </tr>
            </thead>
            <tbody>
                @foreach($fees as $fee)
                @php
                    $paidAmount = $fee->payments->sum('amount_paid');
                    $feeBalance = $fee->amount - $paidAmount;
                @endphp
                <tr>
                    <td>{{ $fee->feeStructure->feeType->type_name }}</td>
                    <td>{{ $fee->due_date->format('d-m-Y') }}</td>
                    <td>{{ number_format($fee->amount, 2) }}</td>
                    <td>{{ number_format($paidAmount, 2) }}</td>
                    <td>{{ number_format($feeBalance, 2) }}</td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="2"><strong>Total</strong></td>
                    <td>{{ number_format($total_amount, 2) }}</td>
                    <td>{{ number_format($total_paid, 2) }}</td>
                    <td>{{ number_format($balance, 2) }}</td>
                </tr>
            </tbody>
        </table>
        
        <p>Please make the payment at your earliest convenience to avoid any late payment penalties.</p>
        
        <p>If you have already made the payment, please disregard this reminder and accept our thanks.</p>
        
        <a href="{{ route('login') }}" class="cta">Login to Make Payment</a>
        
        <div class="footer">
            <p>© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            <p>School Address, City, Postal Code | Phone: +xxx-xxx-xxxx | Email: info@school.com</p>
            <p>This is an automated message. Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>