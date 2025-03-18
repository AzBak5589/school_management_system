<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classes extends Model
{
    public $timestamps = false;

    protected $table = 'classes';
    protected $primaryKey = 'class_id';
    
    protected $fillable = [
        'academic_year_id',
        'class_name',
        'section',
        'capacity',
        'class_teacher_id',
    ];
    
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }
    
    public function classTeacher()
    {
        return $this->belongsTo(Teacher::class, 'class_teacher_id');
    }
    
    public function students()
    {
        return $this->hasMany(Student::class, 'current_class_id');
    }
    
    public function subjects()
    {
        return $this->hasMany(ClassSubject::class, 'class_id');
    }
    
    public function enrollments()
    {
        return $this->hasMany(StudentEnrollment::class, 'class_id');
    }
    
    public function getFullNameAttribute()
    {
        return "{$this->class_name} - {$this->section}";
    }
}