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
        Schema::create('dps_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('interval');
            $table->integer('total_installment');
            $table->integer('per_installment');
            $table->double('interest_rate', 15, 8);
            $table->decimal('total_deposit');
            $table->decimal('user_profit', 15, 2);
            $table->decimal('total_mature_amount', 15, 2);
            $table->integer('delay_days')->nullable();
            $table->boolean('increment_charge_type')->default(false);
            $table->enum('increment_type', ['unlimited', 'fixed'])->default('unlimited');
            $table->boolean('decrement_charge_type')->default(false);
            $table->enum('decrement_type', ['unlimited', 'fixed'])->default('unlimited');
            $table->decimal('min_increment_amount', 16, 2)->nullable();
            $table->decimal('max_increment_amount', 16, 2)->nullable();
            $table->decimal('min_decrement_amount', 16, 2)->nullable();
            $table->decimal('max_decrement_amount', 16, 2)->nullable();
            $table->integer('increment_times')->default(0);
            $table->integer('decrement_times')->default(0);
            $table->integer('increment_fee')->default(0);
            $table->integer('decrement_fee')->default(0);
            $table->boolean('can_cancel')->default(false);
            $table->enum('cancel_type', ['anytime', 'fixed'])->nullable();
            $table->string('cancel_days')->nullable();
            $table->string('cancel_fee')->nullable();
            $table->enum('cancel_fee_type', ['fixed', 'percentage'])->nullable();
            $table->string('charge')->nullable();
            $table->enum('charge_type', ['fixed', 'percentage']);
            $table->string('badge')->nullable();
            $table->boolean('add_maturity_platform_fee')->default(false);
            $table->integer('maturity_platform_fee')->nullable();
            $table->boolean('featured')->default(false);
            $table->boolean('is_upgrade')->default(false);
            $table->boolean('is_downgrade')->default(false);
            $table->boolean('status')->default(0);
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
        Schema::dropIfExists('dps_plans');
    }
};
