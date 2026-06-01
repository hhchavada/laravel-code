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
        if (!Schema::hasTable('language_codes')) {
            Schema::create('language_codes', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
                $table->string('code')->nullable();
                // Adding indexes
                $table->index('name');
                $table->index('code');
            });
        }else{
            Schema::table('language_codes', function (Blueprint $table) {
                if (!Schema::hasColumn('language_codes', 'status')) {
                    $table->tinyInteger('status')->default(1);
                }else{
                    DB::statement('ALTER TABLE language_codes MODIFY COLUMN status TINYINT(1) DEFAULT 1');
                }
            });            
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('language_codes');
    }
};
