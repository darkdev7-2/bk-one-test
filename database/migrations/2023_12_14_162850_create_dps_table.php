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
        Schema::create('dps', function (Blueprint $table) {
            $table->id();
            $table->string('dps_id')->unique();
            $table->foreignId('plan_id');
            $table->foreignId('user_id');
            $table->integer('per_installment');
            $table->integer('given_installment')->default(0);
            $table->dateTime('cancel_date')->nullable();
            $table->decimal('cancel_fee')->nullable();
            $table->integer('increment_count')->default(0);
            $table->integer('decrement_count')->default(0);
            $table->enum('status', ['due', 'running', 'closed', 'mature'])->default('running');
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
        Schema::dropIfExists('dps');
    }
};
