<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model
{
    protected $table = 'academic_years';
    protected $primaryKey = 'academic_year_id';
    
    protected $fillable = [
        'year_name',
        'start_date',
        'end_date',
        'is_current',
    ];
    
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current' => 'boolean',
    ];
    
    public function classes()
    {
        return $this->hasMany(Classes::class, 'academic_year_id');
    }
}