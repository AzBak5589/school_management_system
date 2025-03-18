<?php

namespace App\Http\Controllers;

use App\Models\FeeStructure;
use App\Models\FeeType;
use App\Models\AcademicYear;
use App\Models\Classes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeeStructureController extends Controller
{
    public function index(Request $request)
    {
        // Only admin can access fee structure management
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $academicYearId = $request->input('academic_year');
        $classId = $request->input('class_id');
        
        // Get all academic years for filter
        $academicYears = AcademicYear::orderBy('year_name', 'desc')->get();
        
        // If no academic year is selected, use the current one
        if (!$academicYearId && $academicYears->count() > 0) {
            $currentYear = $academicYears->where('is_current', true)->first();
            $academicYearId = $currentYear ? $currentYear->academic_year_id : $academicYears->first()->academic_year_id;
        }
        
        // Get classes for the selected academic year
        $classes = Classes::where('academic_year_id', $academicYearId)
                         ->orderBy('class_name')
                         ->get();
        
        // Query builder for fee structures
        $query = FeeStructure::with(['academicYear', 'class', 'feeType']);
        
        if ($academicYearId) {
            $query->where('academic_year_id', $academicYearId);
        }
        
        if ($classId) {
            $query->where('class_id', $classId);
        }
        
        // Get fee structures
        $feeStructures = $query->orderBy('class_id')->paginate(15);
        
        return view('fees.structures.index', compact('feeStructures', 'academicYears', 'classes', 'academicYearId', 'classId'));
    }
    
    public function create()
    {
        // Only admin can create fee structures
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        // Get all academic years
        $academicYears = AcademicYear::orderBy('year_name', 'desc')->get();
        
        // Get all fee types
        $feeTypes = FeeType::orderBy('type_name')->get();
        
        return view('fees.structures.create', compact('academicYears', 'feeTypes'));
    }
    
    public function getClasses($academicYearId)
    {
        // Only admin can access this API
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $classes = Classes::where('academic_year_id', $academicYearId)
                         ->orderBy('class_name')
                         ->get()
                         ->map(function($class) {
                             return [
                                 'id' => $class->class_id,
                                 'name' => $class->class_name . ' - ' . $class->section,
                             ];
                         });
        
        return response()->json($classes);
    }
    
    public function store(Request $request)
    {
        // Only admin can create fee structures
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $request->validate([
            'academic_year_id' => 'required|exists:academic_years,academic_year_id',
            'class_id' => 'required|exists:classes,class_id',
            'fee_type_id' => 'required|exists:fee_types,fee_type_id',
            'amount' => 'required|numeric|min:0',
        ]);
        
        // Check if a fee structure for this combination already exists
        $exists = FeeStructure::where('academic_year_id', $request->academic_year_id)
                             ->where('class_id', $request->class_id)
                             ->where('fee_type_id', $request->fee_type_id)
                             ->exists();
        
        if ($exists) {
            return redirect()->back()->withInput()
                ->with('error', 'A fee structure for this combination already exists');
        }
        
        FeeStructure::create([
            'academic_year_id' => $request->academic_year_id,
            'class_id' => $request->class_id,
            'fee_type_id' => $request->fee_type_id,
            'amount' => $request->amount,
        ]);
        
        return redirect()->route('fee-structures.index', ['academic_year' => $request->academic_year_id])
            ->with('success', 'Fee structure created successfully');
    }
    
    public function edit($id)
    {
        // Only admin can edit fee structures
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $feeStructure = FeeStructure::with(['academicYear', 'class', 'feeType'])->findOrFail($id);
        
        // Get all fee types
        $feeTypes = FeeType::orderBy('type_name')->get();
        
        return view('fees.structures.edit', compact('feeStructure', 'feeTypes'));
    }
    
    public function update(Request $request, $id)
    {
        // Only admin can update fee structures
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $feeStructure = FeeStructure::findOrFail($id);
        
        $request->validate([
            'fee_type_id' => 'required|exists:fee_types,fee_type_id',
            'amount' => 'required|numeric|min:0',
        ]);
        
        // Check if another fee structure with this combination exists
        $exists = FeeStructure::where('academic_year_id', $feeStructure->academic_year_id)
                             ->where('class_id', $feeStructure->class_id)
                             ->where('fee_type_id', $request->fee_type_id)
                             ->where('fee_structure_id', '!=', $id)
                             ->exists();
        
        if ($exists) {
            return redirect()->back()->withInput()
                ->with('error', 'Another fee structure for this combination already exists');
        }
        
        $feeStructure->update([
            'fee_type_id' => $request->fee_type_id,
            'amount' => $request->amount,
        ]);
        
        return redirect()->route('fee-structures.index', ['academic_year' => $feeStructure->academic_year_id])
            ->with('success', 'Fee structure updated successfully');
    }
    
    public function destroy($id)
    {
        // Only admin can delete fee structures
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $feeStructure = FeeStructure::findOrFail($id);
        
        // Check if the fee structure is being used in any student fees
        if ($feeStructure->studentFees()->count() > 0) {
            return redirect()->route('fee-structures.index', ['academic_year' => $feeStructure->academic_year_id])
                ->with('error', 'Cannot delete fee structure that is being used for student fees');
        }
        
        $feeStructure->delete();
        
        return redirect()->route('fee-structures.index', ['academic_year' => $feeStructure->academic_year_id])
            ->with('success', 'Fee structure deleted successfully');
    }
    
    public function applyToStudents($id)
    {
        // Only admin can apply fee structures to students
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $feeStructure = FeeStructure::with(['class', 'feeType'])->findOrFail($id);
        
        return view('fees.structures.apply', compact('feeStructure'));
    }
    
    public function processApplyToStudents(Request $request, $id)
    {
        // Only admin can apply fee structures to students
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $feeStructure = FeeStructure::with(['class'])->findOrFail($id);
        
        $request->validate([
            'due_date' => 'required|date',
        ]);
        
        // Get all students in the class
        $students = \App\Models\Student::where('current_class_id', $feeStructure->class_id)->get();
        
        $count = 0;
        
        // Create fee entries for each student
        foreach ($students as $student) {
            // Check if a fee entry already exists
            $exists = \App\Models\StudentFee::where('student_id', $student->student_id)
                                          ->where('fee_structure_id', $id)
                                          ->exists();
            
            if (!$exists) {
                \App\Models\StudentFee::create([
                    'student_id' => $student->student_id,
                    'fee_structure_id' => $id,
                    'amount' => $feeStructure->amount,
                    'due_date' => $request->due_date,
                    'status' => 'unpaid',
                ]);
                
                $count++;
            }
        }
        
        return redirect()->route('fees.structures.index', ['academic_year' => $feeStructure->academic_year_id])
            ->with('success', "Fee applied to {$count} students successfully");
    }
}