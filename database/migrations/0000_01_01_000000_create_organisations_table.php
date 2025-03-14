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
        Schema::create('organisations', function (Blueprint $table) {
            $table->id();
            $table->string('uid')->nullable()->unique();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('email')->nullable()->unique();
            $table->string('phone')->nullable();
            $table->string('website')->nullable();
            $table->string('logo', 1024)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organisations');
    }
};
