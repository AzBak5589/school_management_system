<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Administrator extends Model
{
    use HasFactory;
    
    protected $table = 'administrators';
    protected $primaryKey = 'admin_id';
    
    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'phone',
        'address',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }
}