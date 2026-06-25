<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('queue');
            $table->index('user_id');
        });

        Schema::table('failed_jobs', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('queue');
            $table->index('user_id');
        });
    }

    public function down()
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
        Schema::table('failed_jobs', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
    }
};
