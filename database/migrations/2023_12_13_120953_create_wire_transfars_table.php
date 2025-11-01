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
        Schema::create('wire_transfars', function (Blueprint $table) {
            $table->id();
            $table->decimal('minimum_transfer', 20, 2)->default(0);
            $table->decimal('maximum_transfer', 20, 2)->default(0);
            $table->integer('charge')->nullable();
            $table->string('charge_type')->nullable();
            $table->decimal('daily_limit_maximum_amount', 20, 2)->default(0);
            $table->integer('daily_limit_maximum_count')->default(0);
            $table->decimal('monthly_limit_maximum_amount', 20, 2)->default(0);
            $table->integer('monthly_limit_maximum_count')->default(0);
            $table->text('instructions')->nullable();
            $table->json('field_options')->nullable();
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
        Schema::dropIfExists('wire_transfars');
    }
};
