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
        Schema::create('fdr', function (Blueprint $table) {
            $table->id();
            $table->string('fdr_id', 255)->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('fdr_plan_id');
            $table->decimal('amount', 10, 2)->nullable()->default(0);
            $table->integer('increment_count')->default(0);
            $table->integer('decrement_count')->default(0);
            $table->date('end_date')->nullable();
            $table->dateTime('cancel_date')->nullable();
            $table->decimal('cancel_fee')->nullable();
            $table->enum('status', ['running', 'closed', 'completed'])->default('running');
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
        Schema::dropIfExists('fdr');
    }
};
