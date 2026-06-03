<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('blog_bookmarks', function (Blueprint $table) {
            if (!Schema::hasColumn('blog_bookmarks', 'fcm_token')) {
                $table->string('fcm_token', 255)->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('blog_bookmarks', function (Blueprint $table) {
            if (Schema::hasColumn('blog_bookmarks', 'fcm_token')) {
                $table->dropColumn('fcm_token');
            }
        });
    }
};
