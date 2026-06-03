<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ads', function (Blueprint $table) {
            // Check if column does NOT exist before adding
            if (!Schema::hasColumn('ads', 'is_repeated')) {
                $table->tinyInteger('is_repeated')
                      ->default(0)
                      ->after('source_link')
                      ->comment('Indicates if ad is repeated');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ads', function (Blueprint $table) {
            // Remove column only if it exists
            if (Schema::hasColumn('ads', 'is_repeated')) {
                $table->dropColumn('is_repeated');
            }
        });
    }
};