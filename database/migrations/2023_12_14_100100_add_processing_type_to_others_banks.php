<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('others_banks', function (Blueprint $table) {
            $table->enum('processing_type', ['days', 'hours'])->after('processing_time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('others_banks', function (Blueprint $table) {
            $table->dropColumn('processing_type');
        });
    }
};
