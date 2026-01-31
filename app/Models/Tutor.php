<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tutor extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'name',
        'specialization',
        'qualifications',
        'experience_years',
        'bio',
        'profile_image',
        'status',
    ];

    public function units()
    {
        return $this->belongsToMany(Unit::class, 'tutor_unit')->withTimestamps();
    }
}
