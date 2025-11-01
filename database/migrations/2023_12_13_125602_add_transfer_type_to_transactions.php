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
        Schema::table('transactions', function (Blueprint $table) {
            $table->enum('transfer_type', ['wire_transfer', 'other_bank_transfer', 'own_bank_transfer'])->after('status')->nullable();
            $table->unsignedBigInteger('bank_id')->after('transfer_type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->enum('transfer_type', ['wire_transfer', 'other_bank_transfer', 'own_bank_transfer'])->after('status')->nullable();
            $table->unsignedBigInteger('bank_id')->after('transfer_type')->nullable();
        });
    }
};
