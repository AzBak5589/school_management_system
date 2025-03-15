<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StudentFee;
use App\Models\Student;
use App\Models\AcademicYear;
use App\Mail\FeeReminderMail;
use Illuminate\Support\Facades\Mail;

class FeeReminderController extends Controller
{
    /**
     * Display the fee reminder page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Only admin can access fee reminders
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        // Get current academic year
        $currentYear = AcademicYear::where('is_current', true)->first();
        
        if (!$currentYear) {
            return redirect()->route('academic-years.index')
                ->with('error', 'No current academic year set. Please set a current academic year first.');
        }
        
        // Get classes for the current academic year
        $classes = \App\Models\Classes::where('academic_year_id', $currentYear->academic_year_id)
                            ->orderBy('class_name')
                            ->get();
        
        // Get fee types for filter
        $feeTypes = \App\Models\FeeType::orderBy('type_name')->get();
        
        // Get overdue fee count
        $overdueFees = StudentFee::whereHas('feeStructure', function($query) use ($currentYear) {
                $query->where('academic_year_id', $currentYear->academic_year_id);
            })
            ->where('status', '!=', 'paid')
            ->where('due_date', '<', now())
            ->count();
        
        // Get upcoming due count (due in next 7 days)
        $upcomingDueFees = StudentFee::whereHas('feeStructure', function($query) use ($currentYear) {
                $query->where('academic_year_id', $currentYear->academic_year_id);
            })
            ->where('status', '!=', 'paid')
            ->where('due_date', '>=', now())
            ->where('due_date', '<=', now()->addDays(7))
            ->count();
        
        return view('fees.reminders.index', compact(
            'classes',
            'feeTypes',
            'overdueFees',
            'upcomingDueFees'
        ));
    }
    
    /**
     * Send fee reminders to the selected students.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function send(Request $request)
    {
        // Only admin can send fee reminders
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $request->validate([
            'reminder_type' => 'required|in:overdue,upcoming,all',
            'class_id' => 'nullable|exists:classes,class_id',
            'fee_type_id' => 'nullable|exists:fee_types,fee_type_id',
            'subject' => 'required|string|max:100',
            'message' => 'required|string',
        ]);
        
        // Get current academic year
        $currentYear = AcademicYear::where('is_current', true)->first();
        
        if (!$currentYear) {
            return redirect()->route('academic-years.index')
                ->with('error', 'No current academic year set. Please set a current academic year first.');
        }
        
        // Build query based on selected filters
        $query = StudentFee::with(['student', 'feeStructure.feeType'])
            ->whereHas('feeStructure', function($q) use ($currentYear) {
                $q->where('academic_year_id', $currentYear->academic_year_id);
            })
            ->where('status', '!=', 'paid');
        
        // Filter by reminder type
        if ($request->reminder_type === 'overdue') {
            $query->where('due_date', '<', now());
        } elseif ($request->reminder_type === 'upcoming') {
            $query->where('due_date', '>=', now())
                ->where('due_date', '<=', now()->addDays(7));
        }
        
        // Filter by class
        if ($request->class_id) {
            $query->whereHas('student', function($q) use ($request) {
                $q->where('current_class_id', $request->class_id);
            });
        }
        
        // Filter by fee type
        if ($request->fee_type_id) {
            $query->whereHas('feeStructure', function($q) use ($request) {
                $q->where('fee_type_id', $request->fee_type_id);
            });
        }
        
        // Get student fees that need reminders
        $studentFees = $query->get();
        
        if ($studentFees->isEmpty()) {
            return redirect()->back()
                ->with('error', 'No matching student fees found for the selected criteria.');
        }
        
        // Group fees by student
        $groupedFees = $studentFees->groupBy('student_id');
        
        // Initialize counters
        $totalStudents = 0;
        $totalEmails = 0;
        $totalSMS = 0;
        
        // Send reminders
        foreach ($groupedFees as $studentId => $fees) {
            $student = $fees->first()->student;
            $totalAmount = $fees->sum('amount');
            $totalPaid = $fees->sum(function($fee) {
                return $fee->payments->sum('amount_paid');
            });
            $balance = $totalAmount - $totalPaid;
            
            // Skip if the student has zero balance (just in case)
            if ($balance <= 0) {
                continue;
            }
            
            $totalStudents++;
            
            // Prepare data for email
            $emailData = [
                'student' => $student,
                'fees' => $fees,
                'total_amount' => $totalAmount,
                'total_paid' => $totalPaid,
                'balance' => $balance,
                'subject' => $request->subject,
                'message' => $request->message,
            ];
            
            // Send email reminder if student has email
            if ($student->email) {
                try {
                    Mail::to($student->email)->send(new FeeReminderMail($emailData));
                    $totalEmails++;
                } catch (\Exception $e) {
                    \Log::error('Failed to send fee reminder email to ' . $student->email . ': ' . $e->getMessage());
                }
            }
            
            // Send SMS reminder if student has phone number and SMS setting is enabled
            if ($request->has('send_sms') && $student->phone) {
                try {
                    // Implement SMS sending logic here
                    // This could use a third-party SMS API like Twilio, Nexmo, etc.
                    // Example:
                    // $this->sendSMS($student->phone, $message);
                    $totalSMS++;
                } catch (\Exception $e) {
                    \Log::error('Failed to send fee reminder SMS to ' . $student->phone . ': ' . $e->getMessage());
                }
            }
            
            // Log the reminder
            \App\Models\FeeReminder::create([
                'student_id' => $student->student_id,
                'reminder_type' => $request->reminder_type,
                'reminder_date' => now(),
                'amount_due' => $balance,
                'sent_by' => auth()->id(),
                'sent_via' => $request->has('send_sms') ? 'email,sms' : 'email',
                'message' => $request->message,
            ]);
        }
        
        return redirect()->route('fee-reminders.index')
            ->with('success', "Fee reminders sent successfully to {$totalStudents} students. ({$totalEmails} emails, {$totalSMS} SMS)");
    }
    
    /**
     * Display the reminder log.
     *
     * @return \Illuminate\Http\Response
     */
    public function log()
    {
        // Only admin can view reminder logs
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $reminders = \App\Models\FeeReminder::with(['student', 'sentBy'])
            ->orderBy('reminder_date', 'desc')
            ->paginate(20);
        
        return view('fees.reminders.log', compact('reminders'));
    }
    
    /**
     * Send an individual reminder to a specific student.
     *
     * @param  int  $studentId
     * @return \Illuminate\Http\Response
     */
    public function sendIndividual($studentId)
    {
        // Only admin can send fee reminders
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $student = Student::findOrFail($studentId);
        
        // Get unpaid fees for this student
        $fees = StudentFee::with(['feeStructure.feeType'])
            ->where('student_id', $studentId)
            ->where('status', '!=', 'paid')
            ->get();
        
        if ($fees->isEmpty()) {
            return redirect()->back()
                ->with('error', 'No unpaid fees found for this student.');
        }
        
        return view('fees.reminders.individual', compact('student', 'fees'));
    }
    
    /**
     * Process sending an individual reminder.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $studentId
     * @return \Illuminate\Http\Response
     */
    public function processIndividual(Request $request, $studentId)
    {
        // Only admin can send fee reminders
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $request->validate([
            'subject' => 'required|string|max:100',
            'message' => 'required|string',
        ]);
        
        $student = Student::findOrFail($studentId);
        
        // Get unpaid fees for this student
        $fees = StudentFee::with(['feeStructure.feeType', 'payments'])
            ->where('student_id', $studentId)
            ->where('status', '!=', 'paid')
            ->get();
        
        if ($fees->isEmpty()) {
            return redirect()->back()
                ->with('error', 'No unpaid fees found for this student.');
        }
        
        $totalAmount = $fees->sum('amount');
        $totalPaid = $fees->sum(function($fee) {
            return $fee->payments->sum('amount_paid');
        });
        $balance = $totalAmount - $totalPaid;
        
        // Prepare data for email
        $emailData = [
            'student' => $student,
            'fees' => $fees,
            'total_amount' => $totalAmount,
            'total_paid' => $totalPaid,
            'balance' => $balance,
            'subject' => $request->subject,
            'message' => $request->message,
        ];
        
        // Send email reminder if student has email
        if ($student->email) {
            try {
                Mail::to($student->email)->send(new FeeReminderMail($emailData));
                
                // Log the reminder
                \App\Models\FeeReminder::create([
                    'student_id' => $student->student_id,
                    'reminder_type' => 'individual',
                    'reminder_date' => now(),
                    'amount_due' => $balance,
                    'sent_by' => auth()->id(),
                    'sent_via' => $request->has('send_sms') ? 'email,sms' : 'email',
                    'message' => $request->message,
                ]);
                
                return redirect()->route('students.fees', $studentId)
                    ->with('success', 'Fee reminder sent successfully to ' . $student->first_name . ' ' . $student->last_name);
            } catch (\Exception $e) {
                \Log::error('Failed to send fee reminder email to ' . $student->email . ': ' . $e->getMessage());
                
                return redirect()->back()
                    ->with('error', 'Failed to send email. Please try again later.');
            }
        } else {
            return redirect()->back()
                ->with('error', 'Student does not have an email address.');
        }
    }
}