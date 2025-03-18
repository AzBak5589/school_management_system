<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamResult extends Model
{
    public $timestamps = false;
    protected $table = 'exam_results';
    protected $primaryKey = 'result_id';
    
    protected $fillable = [
        'exam_id',
        'student_id',
        'subject_id',
        'marks_obtained',
        'total_marks',
        'grade',
        'remarks',
        'entered_by',
    ];
    
    protected $casts = [
        'marks_obtained' => 'decimal:2',
        'total_marks' => 'decimal:2',
    ];
    
    public function examination()
    {
        return $this->belongsTo(Examination::class, 'exam_id');
    }
    
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
    
    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }
    
    public function enteredBy()
    {
        return $this->belongsTo(User::class, 'entered_by');
    }
    
    // Calculate percentage
    public function getPercentageAttribute()
    {
        if ($this->total_marks == 0) return 0;
        return round(($this->marks_obtained / $this->total_marks) * 100, 2);
    }
}