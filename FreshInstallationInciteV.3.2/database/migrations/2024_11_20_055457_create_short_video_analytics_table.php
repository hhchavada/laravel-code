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
        if (!Schema::hasTable('short_video_analytics')) {
            Schema::create('short_video_analytics', function (Blueprint $table) {
                $table->id();
                $table->enum('type', ['view','like','share'])->default('view');
                $table->integer('user_id')->default(0);
                $table->string('player_id', 255)->nullable();
                $table->string('fcm_token', 255)->nullable();
                $table->unsignedBigInteger('short_video_id')->default(0);
                $table->timestamps();
                 // Adding indexes
                $table->index('type');
                $table->index('user_id');
                $table->index('short_video_id');
                $table->index('player_id');
                $table->index('fcm_token');
                $table->index('created_at');
                $table->index('updated_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('short_video_analytics');
    }
};
