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
        if (!Schema::hasTable('short_video_translations')) {
            Schema::create('short_video_translations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('short_video_id')->default(0);
                $table->string('language_code')->nullable();
                $table->string('title')->nullable();
                $table->text('description')->nullable();
                $table->timestamps();
                $table->softDeletes();

                // Foreign key constraint: short_videos.id -> short_video_translations.short_video_id
                $table->foreign('short_video_id')->references('id')->on('short_videos')->onDelete('cascade');

                // Adding indexes
                $table->index('language_code');
                $table->index('title');
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
        Schema::dropIfExists('short_video_translations');
    }
};