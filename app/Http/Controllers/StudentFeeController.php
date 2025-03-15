<?php

namespace App\Http\Controllers;

use App\Models\StudentFee;
use App\Models\Student;
use App\Models\Payment;
use App\Models\AcademicYear;
use App\Models\Classes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class StudentFeeController extends Controller
{
    public function index(Request $request)
    {
        // Only admin can access all student fees
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $classId = $request->input('class_id');
        $status = $request->input('status', 'all');
        
        // Get current academic year
        $currentYear = AcademicYear::where('is_current', true)->first();
        
        if (!$currentYear) {
            return redirect()->route('academic-years.index')
                ->with('error', 'No current academic year set. Please set a current academic year first.');
        }
        
        // Get classes for filter
        $classes = Classes::where('academic_year_id', $currentYear->academic_year_id)
                         ->orderBy('class_name')
                         ->get();
        
        // If no class is selected, show class selection view
        if (!$classId) {
            return view('fees.students.select_class', compact('classes'));
        }
        
        $class = Classes::findOrFail($classId);
        
        // Get students in this class
        $students = Student::where('current_class_id', $classId)
                          ->orderBy('first_name')
                          ->paginate(15);
        
        // Get fee data for these students
        $studentIds = $students->pluck('student_id')->toArray();
        
        // Query for student fees based on status filter
        $query = StudentFee::with(['student', 'feeStructure.feeType'])
                          ->whereIn('student_id', $studentIds);
        
        if ($status !== 'all') {
            $query->where('status', $status);
        }
        
        $studentFees = $query->get()
                            ->groupBy('student_id');
        
        return view('fees.students.index', compact('class', 'students', 'studentFees', 'status'));
    }
    
    public function show($id)
    {
        $studentFee = StudentFee::with(['student', 'feeStructure.feeType', 'payments'])
                               ->findOrFail($id);
        
        // If it's a student, only allow viewing their own fees
        if (Auth::user()->role === 'student') {
            if (Auth::user()->student->student_id !== $studentFee->student_id) {
                abort(403, 'Unauthorized action.');
            }
        }
        
        return view('fees.students.show', compact('studentFee'));
    }
    
    public function edit($id)
    {
        // Only admin can edit student fees
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $studentFee = StudentFee::with(['student', 'feeStructure.feeType'])
                               ->findOrFail($id);
        
        return view('fees.students.edit', compact('studentFee'));
    }
    
    public function update(Request $request, $id)
    {
        // Only admin can update student fees
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $studentFee = StudentFee::findOrFail($id);
        
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'due_date' => 'required|date',
            'status' => 'required|in:paid,partially_paid,unpaid',
            'remarks' => 'nullable|string',
        ]);
        
        $studentFee->update([
            'amount' => $request->amount,
            'due_date' => $request->due_date,
            'status' => $request->status,
            'remarks' => $request->remarks,
        ]);
        
        return redirect()->route('student-fees.show', $id)
            ->with('success', 'Student fee updated successfully');
    }
    
    public function destroy($id)
    {
        // Only admin can delete student fees
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $studentFee = StudentFee::findOrFail($id);
        
        // Check if there are any payments for this fee
        if ($studentFee->payments()->count() > 0) {
            return redirect()->route('student-fees.show', $id)
                ->with('error', 'Cannot delete fee with recorded payments');
        }
        
        $studentFee->delete();
        
        return redirect()->route('student-fees.index', ['class_id' => $studentFee->student->current_class_id])
            ->with('success', 'Student fee deleted successfully');
    }
    
    public function recordPayment($id)
    {
        // Only admin can record payments
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $studentFee = StudentFee::with(['student', 'feeStructure.feeType', 'payments'])
                               ->findOrFail($id);
        
        // Calculate remaining balance
        $paidAmount = $studentFee->payments->sum('amount_paid');
        $remainingBalance = $studentFee->amount - $paidAmount;
        
        return view('fees.students.payment', compact('studentFee', 'paidAmount', 'remainingBalance'));
    }
    
    public function storePayment(Request $request, $id)
    {
        // Only admin can record payments
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $studentFee = StudentFee::with(['payments'])->findOrFail($id);
        
        $request->validate([
            'amount_paid' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:cash,check,bank_transfer,online',
            'transaction_id' => 'nullable|string|max:100',
            'remarks' => 'nullable|string',
        ]);
        
        // Calculate current balance
        $paidAmount = $studentFee->payments->sum('amount_paid');
        $remainingBalance = $studentFee->amount - $paidAmount;
        
        // Validate payment amount
        if ($request->amount_paid > $remainingBalance) {
            return redirect()->back()->withInput()
                ->with('error', 'Payment amount cannot exceed the remaining balance');
        }
        
        // Generate receipt number
        $receiptNumber = Payment::generateReceiptNumber();
        
        // Create payment record
        $payment = Payment::create([
            'fee_id' => $id,
            'amount_paid' => $request->amount_paid,
            'payment_date' => $request->payment_date,
            'payment_method' => $request->payment_method,
            'transaction_id' => $request->transaction_id,
            'received_by' => Auth::id(),
            'receipt_number' => $receiptNumber,
            'remarks' => $request->remarks,
        ]);
        
        // Update student fee status
        $newPaidAmount = $paidAmount + $request->amount_paid;
        
        if ($newPaidAmount >= $studentFee->amount) {
            $studentFee->update(['status' => 'paid']);
        } elseif ($newPaidAmount > 0) {
            $studentFee->update(['status' => 'partially_paid']);
        }
        
        return redirect()->route('payments.receipt', $payment->payment_id)
            ->with('success', 'Payment recorded successfully');
    }
    
    public function studentFees()
    {
        // For students to view their own fees
        if (Auth::user()->role !== 'student') {
            abort(403, 'Unauthorized action.');
        }
        
        $student = Auth::user()->student;
        
        // Get all fees for this student
        $studentFees = StudentFee::with(['feeStructure.feeType', 'payments'])
                               ->where('student_id', $student->student_id)
                               ->orderBy('due_date')
                               ->get();
        
        // Calculate summary statistics
        $totalFees = $studentFees->sum('amount');
        $totalPaid = $studentFees->reduce(function ($carry, $fee) {
            return $carry + $fee->paid_amount;
        }, 0);
        $totalBalance = $totalFees - $totalPaid;
        
        return view('fees.students.student_fees', compact('student', 'studentFees', 'totalFees', 'totalPaid', 'totalBalance'));
    }
}