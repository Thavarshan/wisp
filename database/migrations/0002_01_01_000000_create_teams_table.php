<?php

use App\Models\Organisation;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->string('uid')->nullable()->unique();
            $table->foreignIdFor(Organisation::class)
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();
            $table->string('name');
            $table->boolean('personal_team')->default(false);
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teams');
    }
};
