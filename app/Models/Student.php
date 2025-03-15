<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;
    
    protected $table = 'students';
    protected $primaryKey = 'student_id';
    
    protected $fillable = [
        'user_id',
        'admission_number',
        'first_name',
        'last_name',
        'gender',
        'date_of_birth',
        'admission_date',
        'current_class_id',
        'phone',
        'address',
        'emergency_contact_name',
        'emergency_contact_phone',
    ];
    
    protected $casts = [
        'date_of_birth' => 'date',
        'admission_date' => 'date',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }
    
    public function currentClass()
    {
        return $this->belongsTo(Classes::class, 'current_class_id');
    }
    
    public function enrollments()
    {
        return $this->hasMany(StudentEnrollment::class, 'student_id');
    }
    
    public function attendance()
    {
        return $this->hasMany(StudentAttendance::class, 'student_id');
    }
    
    public function fees()
    {
        return $this->hasMany(StudentFee::class, 'student_id');
    }
}