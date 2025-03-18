<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeReminder extends Model
{
    public $timestamps = false;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'fee_reminders';
    
    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'reminder_id';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'student_id',
        'reminder_type',
        'reminder_date',
        'amount_due',
        'sent_by',
        'sent_via',
        'message',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'reminder_date' => 'datetime',
        'amount_due' => 'decimal:2',
    ];
    
    /**
     * Get the student that the reminder was sent to.
     */
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
    
    /**
     * Get the user who sent the reminder.
     */
    public function sentBy()
    {
        return $this->belongsTo(User::class, 'sent_by');
    }
}