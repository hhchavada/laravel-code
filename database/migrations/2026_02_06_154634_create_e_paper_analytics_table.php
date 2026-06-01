<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up()
    {
        if (!Schema::hasTable('e_paper_analytics')) {
            Schema::create('e_paper_analytics', function (Blueprint $table) {

                $table->id();
                $table->unsignedBigInteger('e_paper_id')->nullable();
                $table->string('type', 10)->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });

            // Table charset & collation
            Schema::table('e_paper_analytics', function (Blueprint $table) {
                $table->charset = 'utf8mb4';
                $table->collation = 'utf8mb4_unicode_ci';
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('e_paper_analytics');
    }
};
