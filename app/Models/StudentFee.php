<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentFee extends Model
{
    protected $table = 'student_fees';
    protected $primaryKey = 'fee_id';
    
    protected $fillable = [
        'student_id',
        'fee_structure_id',
        'amount',
        'due_date',
        'status',
        'remarks',
    ];
    
    protected $casts = [
        'amount' => 'decimal:2',
        'due_date' => 'date',
    ];
    
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
    
    public function feeStructure()
    {
        return $this->belongsTo(FeeStructure::class, 'fee_structure_id');
    }
    
    public function payments()
    {
        return $this->hasMany(Payment::class, 'fee_id');
    }
    
    // Calculate total paid amount
    public function getPaidAmountAttribute()
    {
        return $this->payments->sum('amount_paid');
    }
    
    // Calculate balance due
    public function getBalanceAttribute()
    {
        return $this->amount - $this->paid_amount;
    }
    
    // Check if fee is fully paid
    public function getIsFullyPaidAttribute()
    {
        return $this->status === 'paid' || $this->balance <= 0;
    }
    
    // Check if fee is overdue
    public function getIsOverdueAttribute()
    {
        return $this->status !== 'paid' && $this->due_date < now() && $this->balance > 0;
    }
}