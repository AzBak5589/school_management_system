<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Curriculum extends Model
{
    public $timestamps = false;
    protected $table = 'curriculum';
    protected $primaryKey = 'curriculum_id';
    
    protected $fillable = [
        'class_subject_id',
        'title',
        'description',
        'resource_link',
        'created_by',
    ];
    
    
    public function classSubject()
    {
        return $this->belongsTo(ClassSubject::class, 'class_subject_id');
    }
    
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}