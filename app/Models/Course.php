<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'program_id',
        'code',
        'title',
        'language',
        'price',
        'published',
    ];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function units()
    {
        return $this->belongsToMany(Unit::class)->withTimestamps();
    }

    protected static function boot()
    {
        parent::boot();

        // When a course is soft deleted, also delete related favorites
        static::deleted(function ($course) {
            if ($course->isForceDeleting()) {
                // Hard delete - let foreign key constraints handle it
                return;
            }
            
            // Soft delete - manually delete favorites from pivot table
            \DB::table('favorites')->where('course_id', $course->id)->delete();
        });
    }

    public function favoriteUsers()
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }
}