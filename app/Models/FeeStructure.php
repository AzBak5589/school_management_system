<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeStructure extends Model
{
    protected $table = 'fee_structure';
    protected $primaryKey = 'fee_structure_id';
    
    protected $fillable = [
        'academic_year_id',
        'class_id',
        'fee_type_id',
        'amount',
    ];
    
    protected $casts = [
        'amount' => 'decimal:2',
    ];
    
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }
    
    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }
    
    public function feeType()
    {
        return $this->belongsTo(FeeType::class, 'fee_type_id');
    }
    
    public function studentFees()
    {
        return $this->hasMany(StudentFee::class, 'fee_structure_id');
    }
}