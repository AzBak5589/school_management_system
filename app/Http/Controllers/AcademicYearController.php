<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AcademicYearController extends Controller
{
    public function index(Request $request)
    {
        // Check if user is admin
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $academicYears = AcademicYear::orderBy('start_date', 'desc')->paginate(10);
        
        return view('admin.academic_years.index', compact('academicYears'));
    }
    
    public function create()
    {
        // Check if user is admin
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        return view('admin.academic_years.create');
    }
    
    public function store(Request $request)
    {
        // Check if user is admin
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $request->validate([
            'year_name' => 'required|string|max:50|unique:academic_years',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_current' => 'boolean',
        ]);
        
        // If setting this academic year as current, update all others
        if ($request->is_current) {
            AcademicYear::where('is_current', true)->update(['is_current' => false]);
        }
        
        AcademicYear::create([
            'year_name' => $request->year_name,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_current' => $request->is_current ? true : false,
        ]);
        
        return redirect()->route('academic-years.index')
            ->with('success', 'Academic year created successfully');
    }
    
    public function edit($id)
    {
        // Check if user is admin
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $academicYear = AcademicYear::findOrFail($id);
        
        return view('admin.academic_years.edit', compact('academicYear'));
    }
    
    public function update(Request $request, $id)
    {
        // Check if user is admin
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $academicYear = AcademicYear::findOrFail($id);
        
        $request->validate([
            'year_name' => 'required|string|max:50|unique:academic_years,year_name,' . $id . ',academic_year_id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_current' => 'boolean',
        ]);
        
        // If setting this academic year as current, update all others
        if ($request->is_current && !$academicYear->is_current) {
            AcademicYear::where('is_current', true)->update(['is_current' => false]);
        }
        
        $academicYear->update([
            'year_name' => $request->year_name,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_current' => $request->is_current ? true : false,
        ]);
        
        return redirect()->route('academic-years.index')
            ->with('success', 'Academic year updated successfully');
    }
    
    public function destroy($id)
    {
        // Check if user is admin
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $academicYear = AcademicYear::findOrFail($id);
        
        // Check if there are classes associated with this academic year
        if ($academicYear->classes()->count() > 0) {
            return redirect()->route('academic-years.index')
                ->with('error', 'Cannot delete academic year with associated classes');
        }
        
        $academicYear->delete();
        
        return redirect()->route('academic-years.index')
            ->with('success', 'Academic year deleted successfully');
    }
}