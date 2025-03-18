<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    public $timestamps = false;

    protected $table = 'subjects';
    protected $primaryKey = 'subject_id';
    
    protected $fillable = [
        'subject_code',
        'subject_name',
        'description',
    ];
    
    public function classes()
    {
        return $this->hasMany(ClassSubject::class, 'subject_id');
    }
}