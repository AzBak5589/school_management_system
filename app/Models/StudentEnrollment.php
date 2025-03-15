<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentEnrollment extends Model
{
    protected $table = 'student_enrollments';
    protected $primaryKey = 'enrollment_id';
    
    protected $fillable = [
        'student_id',
        'class_id',
        'enrollment_date',
        'status',
        'remarks',
    ];
    
    protected $casts = [
        'enrollment_date' => 'date',
    ];
    
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
    
    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }
}