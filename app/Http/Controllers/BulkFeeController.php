<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AcademicYear;
use App\Models\Classes;
use App\Models\Student;
use App\Models\FeeType;
use App\Models\FeeStructure;
use App\Models\StudentFee;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BulkFeeController extends Controller
{
    /**
     * Display the bulk fee operations page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Only admin can access bulk fee operations
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        // Get current academic year
        $currentYear = AcademicYear::where('is_current', true)->first();
        
        if (!$currentYear) {
            return redirect()->route('academic-years.index')
                ->with('error', 'No current academic year set. Please set a current academic year first.');
        }
        
        // Get classes for the current academic year
        $classes = Classes::where('academic_year_id', $currentYear->academic_year_id)
                        ->orderBy('class_name')
                        ->get();
        
        // Get fee types
        $feeTypes = FeeType::orderBy('type_name')->get();
        
        return view('fees.bulk.index', compact('currentYear', 'classes', 'feeTypes'));
    }
    
    /**
     * Process bulk fee assignment.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function process(Request $request)
    {
        // Only admin can process bulk fee operations
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $request->validate([
            'operation_type' => 'required|in:assign,update,remove',
            'class_id' => 'required_if:target_type,class|exists:classes,class_id',
            'fee_type_id' => 'required_if:operation_type,assign|exists:fee_types,fee_type_id',
            'amount' => 'required_if:operation_type,assign|numeric|min:0',
            'due_date' => 'required_if:operation_type,assign|date',
            'update_field' => 'required_if:operation_type,update|in:amount,due_date,status',
            'new_amount' => 'required_if:update_field,amount|numeric|min:0',
            'new_due_date' => 'required_if:update_field,due_date|date',
            'new_status' => 'required_if:update_field,status|in:paid,partially_paid,unpaid',
        ]);
        
        try {
            DB::beginTransaction();
            
            $operationType = $request->operation_type;
            $classId = $request->class_id;
            
            // Get students in the selected class
            $students = Student::where('current_class_id', $classId)->get();
            
            if ($students->isEmpty()) {
                return redirect()->back()
                    ->with('error', 'No students found in the selected class.');
            }
            
            $count = 0;
            
            switch ($operationType) {
                case 'assign':
                    // Assign new fee to all students in the class
                    $feeTypeId = $request->fee_type_id;
                    $amount = $request->amount;
                    $dueDate = $request->due_date;
                    
                    // Get fee structure for this class and fee type
                    $currentYear = AcademicYear::where('is_current', true)->first();
                    
                    $feeStructure = FeeStructure::where('academic_year_id', $currentYear->academic_year_id)
                                              ->where('class_id', $classId)
                                              ->where('fee_type_id', $feeTypeId)
                                              ->first();
                    
                    if (!$feeStructure) {
                        // Create fee structure if it doesn't exist
                        $feeStructure = FeeStructure::create([
                            'academic_year_id' => $currentYear->academic_year_id,
                            'class_id' => $classId,
                            'fee_type_id' => $feeTypeId,
                            'amount' => $amount,
                        ]);
                    }
                    
                    // Assign fee to each student
                    foreach ($students as $student) {
                        // Check if this fee is already assigned to the student
                        $exists = StudentFee::where('student_id', $student->student_id)
                                         ->where('fee_structure_id', $feeStructure->fee_structure_id)
                                         ->exists();
                        
                        if (!$exists) {
                            StudentFee::create([
                                'student_id' => $student->student_id,
                                'fee_structure_id' => $feeStructure->fee_structure_id,
                                'amount' => $amount,
                                'due_date' => $dueDate,
                                'status' => 'unpaid',
                            ]);
                            
                            $count++;
                        }
                    }
                    
                    $message = "Fee assigned to {$count} students successfully.";
                    break;
                    
                case 'update':
                    // Update existing fees for all students in the class
                    $updateField = $request->update_field;
                    $feeTypeId = $request->fee_type_id ?? null;
                    
                    // Get all student fees for this class
                    $query = StudentFee::whereHas('student', function($q) use ($classId) {
                            $q->where('current_class_id', $classId);
                        });
                    
                    // Filter by fee type if provided
                    if ($feeTypeId) {
                        $query->whereHas('feeStructure', function($q) use ($feeTypeId) {
                            $q->where('fee_type_id', $feeTypeId);
                        });
                    }
                    
                    $studentFees = $query->get();
                    
                    if ($studentFees->isEmpty()) {
                        return redirect()->back()
                            ->with('error', 'No matching fees found for the selected criteria.');
                    }
                    
                    // Update field based on selection
                    foreach ($studentFees as $fee) {
                        switch ($updateField) {
                            case 'amount':
                                $fee->amount = $request->new_amount;
                                break;
                                
                            case 'due_date':
                                $fee->due_date = $request->new_due_date;
                                break;
                                
                            case 'status':
                                $fee->status = $request->new_status;
                                break;
                        }
                        
                        $fee->save();
                        $count++;
                    }
                    
                    $message = "{$count} fee records updated successfully.";
                    break;
                    
                case 'remove':
                    // Remove fees for all students in the class
                    $feeTypeId = $request->fee_type_id ?? null;
                    
                    // Get all student fees for this class
                    $query = StudentFee::whereHas('student', function($q) use ($classId) {
                            $q->where('current_class_id', $classId);
                        });
                    
                    // Filter by fee type if provided
                    if ($feeTypeId) {
                        $query->whereHas('feeStructure', function($q) use ($feeTypeId) {
                            $q->where('fee_type_id', $feeTypeId);
                        });
                    }
                    
                    // Only remove fees that don't have payments
                    $feesToRemove = $query->whereDoesntHave('payments')->get();
                    
                    if ($feesToRemove->isEmpty()) {
                        return redirect()->back()
                            ->with('error', 'No eligible fees found for removal. Fees with payments cannot be removed.');
                    }
                    
                    foreach ($feesToRemove as $fee) {
                        $fee->delete();
                        $count++;
                    }
                    
                    $message = "{$count} fee records removed successfully.";
                    break;
            }
            
            DB::commit();
            
            return redirect()->route('fees.bulk.index')
                ->with('success', $message);
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'An error occurred during the operation: ' . $e->getMessage());
        }
    }
    
    /**
     * Show the form for importing fees.
     *
     * @return \Illuminate\Http\Response
     */
    public function importForm()
    {
        // Only admin can import fees
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        // Get current academic year
        $currentYear = AcademicYear::where('is_current', true)->first();
        
        if (!$currentYear) {
            return redirect()->route('academic-years.index')
                ->with('error', 'No current academic year set. Please set a current academic year first.');
        }
        
        return view('fees.bulk.import', compact('currentYear'));
    }
    
    /**
     * Process the imported fee data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function importProcess(Request $request)
    {
        // Only admin can import fees
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $request->validate([
            'fee_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);
        
        try {
            DB::beginTransaction();
            
            // Process the uploaded CSV file
            $file = $request->file('fee_file');
            $filePath = $file->getRealPath();
            
            $data = array_map('str_getcsv', file($filePath));
            $headers = array_shift($data);
            
            // Validate headers
            $requiredHeaders = ['student_id', 'fee_type', 'amount', 'due_date'];
            $missingHeaders = array_diff($requiredHeaders, array_map('strtolower', $headers));
            
            if (!empty($missingHeaders)) {
                return redirect()->back()
                    ->with('error', 'CSV file is missing required headers: ' . implode(', ', $missingHeaders));
            }
            
            // Map headers to column indices
            $headerMap = [];
            foreach ($headers as $index => $header) {
                $headerMap[strtolower($header)] = $index;
            }
            
            $currentYear = AcademicYear::where('is_current', true)->first();
            $successCount = 0;
            $errorCount = 0;
            $errors = [];
            
            // Process each row
            foreach ($data as $rowIndex => $row) {
                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }
                
                // Validate row data
                $studentId = $row[$headerMap['student_id']] ?? null;
                $feeType = $row[$headerMap['fee_type']] ?? null;
                $amount = $row[$headerMap['amount']] ?? null;
                $dueDate = $row[$headerMap['due_date']] ?? null;
                
                if (!$studentId || !$feeType || !$amount || !$dueDate) {
                    $errorCount++;
                    $errors[] = "Row " . ($rowIndex + 2) . ": Missing required field(s).";
                    continue;
                }
                
                // Find student
                $student = Student::find($studentId);
                if (!$student) {
                    $errorCount++;
                    $errors[] = "Row " . ($rowIndex + 2) . ": Student ID {$studentId} not found.";
                    continue;
                }
                
                // Find or create fee type
                $feeTypeModel = FeeType::firstOrCreate(['type_name' => $feeType], ['description' => $feeType]);
                
                // Find or create fee structure
                $feeStructure = FeeStructure::firstOrCreate([
                    'academic_year_id' => $currentYear->academic_year_id,
                    'class_id' => $student->current_class_id,
                    'fee_type_id' => $feeTypeModel->fee_type_id,
                ], [
                    'amount' => $amount,
                ]);
                
                // Check if fee already exists for this student
                $exists = StudentFee::where('student_id', $studentId)
                                 ->where('fee_structure_id', $feeStructure->fee_structure_id)
                                 ->exists();
                
                if (!$exists) {
                    // Try to parse the due date
                    try {
                        $parsedDueDate = \Carbon\Carbon::createFromFormat('Y-m-d', $dueDate);
                    } catch (\Exception $e) {
                        try {
                            $parsedDueDate = \Carbon\Carbon::createFromFormat('d/m/Y', $dueDate);
                        } catch (\Exception $e) {
                            $errorCount++;
                            $errors[] = "Row " . ($rowIndex + 2) . ": Invalid date format '{$dueDate}'. Use YYYY-MM-DD or DD/MM/YYYY.";
                            continue;
                        }
                    }
                    
                    // Create student fee
                    StudentFee::create([
                        'student_id' => $studentId,
                        'fee_structure_id' => $feeStructure->fee_structure_id,
                        'amount' => $amount,
                        'due_date' => $parsedDueDate,
                        'status' => 'unpaid',
                    ]);
                    
                    $successCount++;
                } else {
                    $errorCount++;
                    $errors[] = "Row " . ($rowIndex + 2) . ": Fee already exists for Student ID {$studentId} and Fee Type '{$feeType}'.";
                }
            }
            
            DB::commit();
            
            $message = "{$successCount} fees imported successfully.";
            if ($errorCount > 0) {
                $message .= " {$errorCount} rows had errors.";
            }
            
            return redirect()->route('fees.bulk.index')
                ->with('success', $message)
                ->with('import_errors', $errors);
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'An error occurred during import: ' . $e->getMessage());
        }
    }
    public function downloadTemplate()
{
    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="fee_import_template.csv"',
    ];
    
    $content = "student_id,fee_type,amount,due_date\n";
    $content .= "1001,Tuition Fee,500.00,2025-04-15\n";
    $content .= "1002,Library Fee,100.00,2025-04-15\n";
    $content .= "1003,Lab Fee,150.00,2025-04-15\n";
    
    return response($content, 200, $headers);
}
}