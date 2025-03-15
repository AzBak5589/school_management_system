<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;
    
    protected $table = 'teachers';
    protected $primaryKey = 'teacher_id';
    
    protected $fillable = [
        'user_id',
        'employee_id',
        'first_name',
        'last_name',
        'gender',
        'date_of_birth',
        'join_date',
        'qualification',
        'phone',
        'address',
    ];
    
    protected $casts = [
        'date_of_birth' => 'date',
        'join_date' => 'date',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }
    
    public function classSubjects()
    {
        return $this->hasMany(ClassSubject::class, 'teacher_id');
    }
}