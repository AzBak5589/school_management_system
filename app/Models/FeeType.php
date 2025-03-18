<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeType extends Model
{
    public $timestamps = false;
    protected $table = 'fee_types';
    protected $primaryKey = 'fee_type_id';
    
    protected $fillable = [
        'type_name',
        'description',
    ];
    
    public function feeStructures()
    {
        return $this->hasMany(FeeStructure::class, 'fee_type_id');
    }
}