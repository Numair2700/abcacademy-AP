<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->date('registration_date');
            $table->string('session'); // e.g., 22-23; validate via regex
            $table->string('btec_number')->unique();
            $table->unique(['student_id', 'course_id', 'session']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};


