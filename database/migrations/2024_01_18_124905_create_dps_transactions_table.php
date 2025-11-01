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
        Schema::create('dps_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dps_id');
            $table->date('installment_date');
            $table->date('given_date')->nullable();
            $table->integer('deferment')->default(0);
            $table->decimal('paid_amount')->default(0);
            $table->decimal('charge')->default(0);
            $table->decimal('final_amount')->default(0);
            $table->timestamps();
            $table->foreign('dps_id')->references('id')->on('dps')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dps_transactions');
    }
};
