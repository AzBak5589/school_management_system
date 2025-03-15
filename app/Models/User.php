<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'user_id';

    protected $fillable = [
        'username',
        'email',
        'password',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'reset_token',
    ];

    protected $casts = [
        'last_login' => 'datetime',
        'reset_token_expiry' => 'datetime',
        'is_active' => 'boolean',
    ];
    
    public function administrator()
    {
        return $this->hasOne(Administrator::class, 'user_id');
    }
    
    public function teacher()
    {
        return $this->hasOne(Teacher::class, 'user_id');
    }
    
    public function student()
    {
        return $this->hasOne(Student::class, 'user_id');
    }
    
    public function isAdmin()
    {
        return $this->role === 'admin';
    }
    
    public function isTeacher()
    {
        return $this->role === 'teacher';
    }
    
    public function isStudent()
    {
        return $this->role === 'student';
    }
    
    public function profile()
    {
        return match($this->role) {
            'admin' => $this->administrator,
            'teacher' => $this->teacher,
            'student' => $this->student,
            default => null,
        };
    }
}