<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('card_holder_id')->constrained()->onDelete('cascade');
            $table->string('card_id')->nullable();
            $table->string('provider');
            $table->string('currency', 3)->default('USD');
            $table->string('type')->default('virtual');
            $table->enum('status', ['active', 'inactive'])->default('inactive');
            $table->decimal('amount')->default(0);
            $table->string('card_number');
            $table->string('cvc');
            $table->integer('expiration_month');
            $table->integer('expiration_year');
            $table->string('last_four_digits', 4);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cards');
    }
};
