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
        Schema::create('bill_services', function (Blueprint $table) {
            $table->id();
            $table->string('method');
            $table->string('name');
            $table->string('currency');
            $table->unsignedBigInteger('api_id');
            $table->string('country');
            $table->string('country_code');
            $table->string('code');
            $table->string('type');
            $table->string('label');
            $table->json('data');
            $table->integer('amount')->default(0);
            $table->integer('min_amount')->default(0);
            $table->integer('max_amount')->default(0);
            $table->float('charge')->default(0);
            $table->enum('charge_type', ['fixed', 'percentage'])->default('fixed');
            $table->boolean('status')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bill_services');
    }
};
