<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherAttendance extends Model
{
    protected $table = 'teacher_attendance';
    protected $primaryKey = 'attendance_id';
    
    protected $fillable = [
        'teacher_id',
        'date',
        'status',
        'remarks',
        'recorded_by',
    ];
    
    protected $casts = [
        'date' => 'date',
    ];
    
    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }
    
    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}