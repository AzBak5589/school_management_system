<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\StudentFee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        // Only admin can view all payments
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $paymentMethod = $request->input('payment_method');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        
        // Query builder
        $query = Payment::with(['studentFee.student', 'studentFee.feeStructure.feeType']);
        
        if ($paymentMethod) {
            $query->where('payment_method', $paymentMethod);
        }
        
        if ($startDate) {
            $query->whereDate('payment_date', '>=', $startDate);
        }
        
        if ($endDate) {
            $query->whereDate('payment_date', '<=', $endDate);
        }
        
        $payments = $query->orderBy('payment_date', 'desc')
                         ->paginate(15);
        
        return view('fees.payments.index', compact('payments', 'paymentMethod', 'startDate', 'endDate'));
    }
    
    public function show($id)
    {
        $payment = Payment::with(['studentFee.student', 'studentFee.feeStructure.feeType', 'receiver'])
                         ->findOrFail($id);
        
        // If it's a student, only allow viewing their own payments
        if (Auth::user()->role === 'student') {
            if (Auth::user()->student->student_id !== $payment->studentFee->student_id) {
                abort(403, 'Unauthorized action.');
            }
        }
        
        return view('fees.payments.show', compact('payment'));
    }
    
    public function receipt($id)
    {
        $payment = Payment::with(['studentFee.student', 'studentFee.feeStructure.feeType', 'receiver'])
                         ->findOrFail($id);
        
        // If it's a student, only allow viewing their own receipts
        if (Auth::user()->role === 'student') {
            if (Auth::user()->student->student_id !== $payment->studentFee->student_id) {
                abort(403, 'Unauthorized action.');
            }
        }
        
        return view('fees.payments.receipt', compact('payment'));
    }
    
    public function delete($id)
    {
        // Only admin can delete payments
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $payment = Payment::with(['studentFee'])->findOrFail($id);
        
        return view('fees.payments.delete', compact('payment'));
    }
    
    public function destroy(Request $request, $id)
    {
        // Only admin can delete payments
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $payment = Payment::with(['studentFee'])->findOrFail($id);
        $studentFee = $payment->studentFee;
        
        // Confirm with password
        $request->validate([
            'password' => 'required|string',
            'reason' => 'required|string',
        ]);
        
        // Check password
        if (!Auth::validate(['email' => Auth::user()->email, 'password' => $request->password])) {
            return redirect()->back()
                ->with('error', 'Invalid password. Payment deletion canceled.');
        }
        
        // Log the payment deletion
        \Log::warning("Payment #{$payment->payment_id} deleted by User ID: " . Auth::id() . 
                     " | Reason: {$request->reason} | Amount: {$payment->amount_paid} | " .
                     "Receipt: {$payment->receipt_number}");
        
        // Delete the payment
        $payment->delete();
        
        // Update the student fee status
        $paidAmount = $studentFee->payments()->sum('amount_paid');
        
        if ($paidAmount <= 0) {
            $studentFee->update(['status' => 'unpaid']);
        } elseif ($paidAmount < $studentFee->amount) {
            $studentFee->update(['status' => 'partially_paid']);
        } else {
            $studentFee->update(['status' => 'paid']);
        }
        
        return redirect()->route('student-fees.show', $studentFee->fee_id)
            ->with('success', 'Payment was successfully deleted and student fee status updated.');
    }
    
    public function studentPayments()
    {
        // For students to view their own payment history
        if (Auth::user()->role !== 'student') {
            abort(403, 'Unauthorized action.');
        }
        
        $student = Auth::user()->student;
        
        // Get all fees for this student
        $studentFees = StudentFee::where('student_id', $student->student_id)->pluck('fee_id');
        
        // Get all payments for these fees
        $payments = Payment::with(['studentFee.feeStructure.feeType'])
                          ->whereIn('fee_id', $studentFees)
                          ->orderBy('payment_date', 'desc')
                          ->paginate(10);
        
        return view('fees.payments.student_payments', compact('student', 'payments'));
    }
    
    public function downloadReceipt($id)
    {
        $payment = Payment::with(['studentFee.student', 'studentFee.feeStructure.feeType', 'receiver'])
                         ->findOrFail($id);
        
        // If it's a student, only allow downloading their own receipts
        if (Auth::user()->role === 'student') {
            if (Auth::user()->student->student_id !== $payment->studentFee->student_id) {
                abort(403, 'Unauthorized action.');
            }
        }
        
        // Generate PDF receipt
        $pdf = \PDF::loadView('fees.payments.receipt_pdf', compact('payment'));
        
        return $pdf->download('receipt-' . $payment->receipt_number . '.pdf');
    }
    
    public function reports(Request $request)
    {
        // Only admin can view payment reports
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));
        
        // Get summary statistics
        $totalCollected = Payment::whereBetween('payment_date', [$startDate, $endDate])->sum('amount_paid');
        $paymentsByMethod = Payment::whereBetween('payment_date', [$startDate, $endDate])
                                  ->select('payment_method', \DB::raw('SUM(amount_paid) as total'))
                                  ->groupBy('payment_method')
                                  ->get();
        
        // Get daily collection totals for chart
        $dailyTotals = Payment::whereBetween('payment_date', [$startDate, $endDate])
                             ->select(\DB::raw('DATE(payment_date) as date'), \DB::raw('SUM(amount_paid) as total'))
                             ->groupBy('date')
                             ->orderBy('date')
                             ->get();
        
        return view('fees.payments.reports', compact('startDate', 'endDate', 'totalCollected', 
                                                    'paymentsByMethod', 'dailyTotals'));
    }
    public function exportReport(Request $request)
{
    // Validate and prepare filter parameters
    $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
    $endDate = $request->input('end_date', now()->format('Y-m-d'));
    $paymentMethod = $request->input('payment_method');

    // Query payments based on filters
    $query = Payment::with(['studentFee.student', 'studentFee.feeStructure.feeType'])
        ->whereBetween('payment_date', [$startDate, $endDate]);

    if ($paymentMethod) {
        $query->where('payment_method', $paymentMethod);
    }

    $payments = $query->get();

    // Generate Excel export
    return Excel::download(new PaymentExport($payments), 'payments_report_' . now()->format('YmdHis') . '.xlsx');
}

public function printReport(Request $request)
{
    // Similar query as exportReport
    $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
    $endDate = $request->input('end_date', now()->format('Y-m-d'));
    $paymentMethod = $request->input('payment_method');

    $query = Payment::with(['studentFee.student', 'studentFee.feeStructure.feeType'])
        ->whereBetween('payment_date', [$startDate, $endDate]);

    if ($paymentMethod) {
        $query->where('payment_method', $paymentMethod);
    }

    $payments = $query->get();

    // Generate printable view
    return view('fees.payments.print_report', compact('payments', 'startDate', 'endDate', 'paymentMethod'));
}

public function pdfReport(Request $request)
{
    // Similar query as exportReport
    $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
    $endDate = $request->input('end_date', now()->format('Y-m-d'));
    $paymentMethod = $request->input('payment_method');

    $query = Payment::with(['studentFee.student', 'studentFee.feeStructure.feeType'])
        ->whereBetween('payment_date', [$startDate, $endDate]);

    if ($paymentMethod) {
        $query->where('payment_method', $paymentMethod);
    }

    $payments = $query->get();

    // Generate PDF
    $pdf = PDF::loadView('fees.payments.pdf_report', compact('payments', 'startDate', 'endDate', 'paymentMethod'));
    return $pdf->download('payments_report_' . now()->format('YmdHis') . '.pdf');
}
}