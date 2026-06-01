<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('rss_feeds', function (Blueprint $table) {
            if (!Schema::hasColumn('rss_feeds', 'post_auto_publish')) {
                $table->boolean('post_auto_publish')->default(0);
            }

            if (!Schema::hasColumn('rss_feeds', 'auto_publish_hour')) {
                $table->integer('auto_publish_hour')->nullable();
            }

            if (!Schema::hasColumn('rss_feeds', 'auto_publish_post_count')) {
                $table->integer('auto_publish_post_count')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('rss_feeds', function (Blueprint $table) {
            if (Schema::hasColumn('rss_feeds', 'post_auto_publish')) {
                $table->dropColumn('post_auto_publish');
            }

            if (Schema::hasColumn('rss_feeds', 'auto_publish_hour')) {
                $table->dropColumn('auto_publish_hour');
            }

            if (Schema::hasColumn('rss_feeds', 'auto_publish_post_count')) {
                $table->dropColumn('auto_publish_post_count');
            }
        });
    }
};
