<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin account
        User::updateOrCreate(
            ['email' => 'admin@abcacademy.test'],
            [
                'name' => 'Admin',
                'password' => Hash::make('Password123!'),
                'role' => 'Admin',
            ]
        );

        // Example student account
        $student = User::updateOrCreate(
            ['email' => 'student@abcacademy.test'],
            [
                'name' => 'Sample Student',
                'password' => Hash::make('Password123!'),
                'role' => 'Student',
            ]
        );

        // Create student profile for the sample student
        if ($student && !$student->student) {
            \App\Models\Student::create([
                'user_id' => $student->id,
                'first_name' => 'Sample',
                'last_name' => 'Student',
                'center_ref' => 'A01B300C',
            ]);
        }
    }
}
