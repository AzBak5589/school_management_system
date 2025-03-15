<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'payments';
    protected $primaryKey = 'payment_id';
    
    protected $fillable = [
        'fee_id',
        'amount_paid',
        'payment_date',
        'payment_method',
        'transaction_id',
        'received_by',
        'receipt_number',
        'remarks',
    ];
    
    protected $casts = [
        'amount_paid' => 'decimal:2',
        'payment_date' => 'date',
    ];
    
    public function studentFee()
    {
        return $this->belongsTo(StudentFee::class, 'fee_id');
    }
    
    public function receiver()
    {
        return $this->belongsTo(User::class, 'received_by');
    }
    
    // Generate unique receipt number
    public static function generateReceiptNumber()
    {
        $prefix = 'RCT-';
        $year = date('Y');
        $month = date('m');
        
        // Get the last receipt number for this month and year
        $lastReceipt = self::where('receipt_number', 'like', $prefix . $year . $month . '%')
                           ->orderBy('receipt_number', 'desc')
                           ->first();
        
        if ($lastReceipt) {
            // Extract the numeric part and increment
            $lastNumber = intval(substr($lastReceipt->receipt_number, -4));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        // Format with leading zeros
        return $prefix . $year . $month . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
}