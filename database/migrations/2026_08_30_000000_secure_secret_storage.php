<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Existing links contain bearer tokens in their paths and cannot be
        // safely migrated to the fragment-based protocol.
        DB::table('secrets')->delete();

        Schema::table('secrets', function (Blueprint $table): void {
            $table->mediumText('content')->change();
        });
    }

    public function down(): void
    {
        throw new RuntimeException(
            'Secure secret storage is irreversible; restore the database backup instead.',
        );
    }
};
