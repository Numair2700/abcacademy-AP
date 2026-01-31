<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    protected $fillable = [
        'btec_code',
        'title',
        'credit',
        'published',
    ];

    public function courses()
    {
        return $this->belongsToMany(Course::class)->withTimestamps();
    }

    public function tutors()
    {
        return $this->belongsToMany(Tutor::class, 'tutor_unit')->withTimestamps();
    }
}


