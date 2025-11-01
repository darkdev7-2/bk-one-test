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
        Schema::create('loan_plans', function (Blueprint $table) {
            $table->id();
            $table->text('name');
            $table->decimal('minimum_amount', 10, 2)->default(0.00);
            $table->decimal('maximum_amount', 10, 2)->default(0.00);
            $table->float('installment_rate')->default(0.00);
            $table->integer('installment_intervel')->default(0);
            $table->integer('total_installment')->default(0);
            $table->float('admin_profit')->default(0.00);
            $table->text('instructions')->nullable();
            $table->integer('delay_days');
            $table->float('charge');
            $table->enum('charge_type', ['fixed', 'percentage']);
            $table->integer('loan_fee')->nullable();
            $table->json('field_options');
            $table->string('badge')->nullable();
            $table->boolean('featured')->default(false);
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
        Schema::dropIfExists('loan_plans');
    }
};
