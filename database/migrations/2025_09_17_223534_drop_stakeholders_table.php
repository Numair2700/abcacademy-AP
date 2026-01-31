<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('stakeholders');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('stakeholders', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['Board Member', 'Partner', 'Sponsor', 'Advisor', 'Industry Expert'])->default('Board Member');
            $table->string('organization')->nullable();
            $table->string('position')->nullable();
            $table->text('bio')->nullable();
            $table->string('image')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('website')->nullable();
            $table->boolean('featured')->default(false);
            $table->timestamps();
        });
    }
};
