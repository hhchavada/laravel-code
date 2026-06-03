<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up()
    {
        if (!Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {

                $table->id();

                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedBigInteger('blog_id')->nullable();

                $table->string('type', 255)->nullable();
                $table->string('title', 255);
                $table->text('message');
                $table->tinyInteger('is_read')->default(0);
                $table->tinyInteger('is_remove')->default(0);
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });

            // Table charset & collation
            Schema::table('notifications', function (Blueprint $table) {
                $table->charset = 'utf8mb4';
                $table->collation = 'utf8mb4_unicode_ci';
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('notifications');
    }
};
