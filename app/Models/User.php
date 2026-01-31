<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
   
    use HasFactory, Notifiable;


    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];


    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function student()
    {
        return $this->hasOne(Student::class);
    }

    public function favoriteCourses()
    {
        return $this->belongsToMany(Course::class, 'favorites')->withTimestamps();
    }

    public function ensureStudentProfile()
    {
        if ($this->role === 'Student' && !$this->student) {
            Student::create([
                'user_id' => $this->id,
                'first_name' => explode(' ', $this->name)[0],
                'last_name' => explode(' ', $this->name, 2)[1] ?? '',
                'center_ref' => 'A' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT) . 'B' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT) . 'C',
            ]);
            $this->refresh(); // Refresh the relationship
        }
        return $this;
    }
}
// app/Models/User.php
