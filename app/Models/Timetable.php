<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Timetable extends Model
{
    public $timestamps = false;
    protected $table = 'timetable';
    protected $primaryKey = 'timetable_id';
    
    protected $fillable = [
        'class_subject_id',
        'day_of_week',
        'start_time',
        'end_time',
        'room',
    ];
    
    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];
    
    public function classSubject()
    {
        return $this->belongsTo(ClassSubject::class, 'class_subject_id');
    }
    
    // Accessor to get the day name
    public function getDayNameAttribute()
    {
        $days = [
            'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'
        ];
        return $days[$this->day_of_week - 1] ?? 'Unknown';
    }
}