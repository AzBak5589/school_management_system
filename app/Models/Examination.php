<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Examination extends Model
{
    public $timestamps = false;
    protected $table = 'examinations';
    protected $primaryKey = 'exam_id';
    
    protected $fillable = [
        'academic_year_id',
        'exam_name',
        'description',
        'start_date',
        'end_date',
        'created_by',
    ];
    
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];
    
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }
    
    public function schedules()
    {
        return $this->hasMany(ExamSchedule::class, 'exam_id');
    }
    
    public function results()
    {
        return $this->hasMany(ExamResult::class, 'exam_id');
    }
    
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}