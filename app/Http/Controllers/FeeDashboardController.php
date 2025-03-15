<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StudentFee;
use App\Models\Payment;
use App\Models\FeeType;
use App\Models\AcademicYear;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FeeDashboardController extends Controller
{
    /**
     * Display the fee management dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Only admin can access the dashboard
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        // Get current academic year
        $currentYear = AcademicYear::where('is_current', true)->first();
        
        if (!$currentYear) {
            return redirect()->route('academic-years.index')
                ->with('error', 'No current academic year set. Please set a current academic year first.');
        }
        
        // Get total fees for current academic year
        $totalFees = StudentFee::whereHas('feeStructure', function($query) use ($currentYear) {
                $query->where('academic_year_id', $currentYear->academic_year_id);
            })->sum('amount');
        
        // Get total collected for current academic year
        $totalCollected = Payment::whereHas('studentFee.feeStructure', function($query) use ($currentYear) {
                $query->where('academic_year_id', $currentYear->academic_year_id);
            })->sum('amount_paid');
        
        // Calculate collection percentage
        $collectionPercentage = ($totalFees > 0) ? round(($totalCollected / $totalFees) * 100, 1) : 0;
        
        // Calculate outstanding balance
        $outstandingBalance = $totalFees - $totalCollected;
        
        // Get count of overdue fees
        $overdueCount = StudentFee::whereHas('feeStructure', function($query) use ($currentYear) {
                $query->where('academic_year_id', $currentYear->academic_year_id);
            })
            ->where('status', '!=', 'paid')
            ->where('due_date', '<', now())
            ->count();
        
        // Get recent payments
        $recentPayments = Payment::with(['studentFee.student', 'studentFee.feeStructure.feeType'])
            ->orderBy('payment_date', 'desc')
            ->take(10)
            ->get();
        
        // Get monthly collection data for chart
        $monthlyCollection = Payment::whereHas('studentFee.feeStructure', function($query) use ($currentYear) {
                $query->where('academic_year_id', $currentYear->academic_year_id);
            })
            ->select(DB::raw('MONTH(payment_date) as month'), DB::raw('SUM(amount_paid) as total'))
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        
        $monthlyLabels = [];
        $monthlyData = [];
        
        for ($i = 1; $i <= 12; $i++) {
            $monthlyLabels[] = date('F', mktime(0, 0, 0, $i, 1));
            $monthlyData[] = $monthlyCollection->where('month', $i)->first()->total ?? 0;
        }
        
        // Get fee distribution by fee type
        $feeDistribution = StudentFee::whereHas('feeStructure', function($query) use ($currentYear) {
                $query->where('academic_year_id', $currentYear->academic_year_id);
            })
            ->join('fee_structure', 'student_fees.fee_structure_id', '=', 'fee_structure.fee_structure_id')
            ->join('fee_types', 'fee_structure.fee_type_id', '=', 'fee_types.fee_type_id')
            ->select('fee_types.type_name', DB::raw('SUM(student_fees.amount) as total'))
            ->groupBy('fee_types.type_name')
            ->orderBy('total', 'desc')
            ->get();
        
        $feeTypeLabels = $feeDistribution->pluck('type_name')->toArray();
        $feeTypeData = $feeDistribution->pluck('total')->toArray();
        
        // Get payment methods distribution
        $paymentMethods = Payment::whereHas('studentFee.feeStructure', function($query) use ($currentYear) {
                $query->where('academic_year_id', $currentYear->academic_year_id);
            })
            ->select('payment_method', DB::raw('SUM(amount_paid) as total'))
            ->groupBy('payment_method')
            ->orderBy('total', 'desc')
            ->get();
        
        $paymentMethodLabels = $paymentMethods->map(function($method) {
                return ucfirst($method->payment_method);
            })->toArray();
        
        $paymentMethodData = $paymentMethods->pluck('total')->toArray();
        
        return view('fees.dashboard', compact(
            'totalFees',
            'totalCollected',
            'collectionPercentage',
            'outstandingBalance',
            'overdueCount',
            'recentPayments',
            'monthlyLabels',
            'monthlyData',
            'feeTypeLabels',
            'feeTypeData',
            'paymentMethodLabels',
            'paymentMethodData'
        ));
    }
}