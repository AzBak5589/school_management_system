<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassSubject extends Model
{
    public $timestamps = false;

    protected $table = 'class_subjects';
    protected $primaryKey = 'class_subject_id';
    
    protected $fillable = [
        'class_id',
        'subject_id',
        'teacher_id',
    ];
    
    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }
    
    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }
    
    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }
}