<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentAttendance extends Model
{
    protected $table = 'student_attendance';
    protected $primaryKey = 'attendance_id';
    
    protected $fillable = [
        'student_id',
        'class_id',
        'date',
        'status',
        'remarks',
        'recorded_by',
    ];
    
    protected $casts = [
        'date' => 'date',
    ];
    
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
    
    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }
    
    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}