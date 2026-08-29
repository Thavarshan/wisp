<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('secrets', function (Blueprint $table): void {
            $table->string('access_token_hash', 64)->nullable()->unique()->after('id');
            $table->string('revocation_token_hash', 64)->nullable()->unique()->after('access_token_hash');
            $table->index('expired_at');
        });

        // Legacy rows have no token hashes and are intentionally inaccessible
        // through the new lookup path. Their data remains eligible for pruning.
        Schema::table('secrets', function (Blueprint $table): void {
            $table->dropUnique(['uid']);
            $table->dropColumn(['uid', 'name']);
        });
    }

    public function down(): void
    {
        Schema::table('secrets', function (Blueprint $table): void {
            $table->dropUnique(['access_token_hash']);
            $table->dropUnique(['revocation_token_hash']);
            $table->dropIndex(['expired_at']);
            $table->dropColumn(['access_token_hash', 'revocation_token_hash']);
            $table->string('uid')->nullable();
            $table->string('name')->nullable();
        });
    }
};
