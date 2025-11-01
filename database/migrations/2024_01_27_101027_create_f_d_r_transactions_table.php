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
        Schema::create('f_d_r_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fdr_id');
            $table->date('given_date')->nullable();
            $table->decimal('given_amount')->default(0);
            $table->decimal('paid_amount')->default(0);
            $table->decimal('charge')->default(0);
            $table->decimal('final_amount')->default(0);
            $table->timestamps();
            $table->foreign('fdr_id')->references('id')->on('fdr')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('f_d_r_transactions');
    }
};
