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
        Schema::create('dayoff_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 60)->unique('unique_name');
            $table->string('description', 255)->nullable();
            $table->string('color', 7)->default('#000000');
            $table->integer('default_days_per_year')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dayoff_types');
    }
};
