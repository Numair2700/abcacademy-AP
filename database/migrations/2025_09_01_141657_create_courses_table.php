<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\SoftDeletes;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('code')->unique();
            $table->string('title');
            $table->integer('level')->nullable();
            $table->string('language'); // consider enum in future
            $table->decimal('price', 10, 2);
            $table->boolean('published')->default(true);
            $table->softDeletes();
            $table->foreignId('program_id')->constrained('programs')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
