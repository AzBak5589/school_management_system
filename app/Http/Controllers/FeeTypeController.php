<?php

namespace App\Http\Controllers;

use App\Models\FeeType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeeTypeController extends Controller
{
    public function index()
    {
        // Only admin can access fee type management
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $feeTypes = FeeType::orderBy('type_name')->paginate(10);
        
        return view('fees.types.index', compact('feeTypes'));
    }
    
    public function create()
    {
        // Only admin can create fee types
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        return view('fees.types.create');
    }
    
    public function store(Request $request)
    {
        // Only admin can create fee types
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $request->validate([
            'type_name' => 'required|string|max:100|unique:fee_types',
            'description' => 'nullable|string',
        ]);
        
        FeeType::create([
            'type_name' => $request->type_name,
            'description' => $request->description,
        ]);
        
        return redirect()->route('fees.types.index')
            ->with('success', 'Fee type created successfully');
    }
    
    public function edit($id)
    {
        // Only admin can edit fee types
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $feeType = FeeType::findOrFail($id);
        
        return view('fees.types.edit', compact('feeType'));
    }
    
    public function update(Request $request, $id)
    {
        // Only admin can update fee types
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $feeType = FeeType::findOrFail($id);
        
        $request->validate([
            'type_name' => 'required|string|max:100|unique:fee_types,type_name,' . $id . ',fee_type_id',
            'description' => 'nullable|string',
        ]);
        
        $feeType->update([
            'type_name' => $request->type_name,
            'description' => $request->description,
        ]);
        
        return redirect()->route('fee-types.index')
            ->with('success', 'Fee type updated successfully');
    }
    
    public function destroy($id)
    {
        // Only admin can delete fee types
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $feeType = FeeType::findOrFail($id);
        
        // Check if the fee type is being used in any fee structures
        if ($feeType->feeStructures()->count() > 0) {
            return redirect()->route('fee-types.index')
                ->with('error', 'Cannot delete fee type that is being used in fee structures');
        }
        
        $feeType->delete();
        
        return redirect()->route('fee-types.index')
            ->with('success', 'Fee type deleted successfully');
    }
}