<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamSchedule extends Model
{
    public $timestamps = false;
    protected $table = 'exam_schedules';
    protected $primaryKey = 'schedule_id';
    
    protected $fillable = [
        'exam_id',
        'class_id',
        'subject_id',
        'exam_date',
        'start_time',
        'end_time',
        'room',
    ];
    
    protected $casts = [
        'exam_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];
    
    public function examination()
    {
        return $this->belongsTo(Examination::class, 'exam_id');
    }
    
    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }
    
    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }
}