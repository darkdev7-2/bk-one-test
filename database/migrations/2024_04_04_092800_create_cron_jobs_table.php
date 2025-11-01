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
        Schema::create('cron_jobs', function (Blueprint $table) {
            $table->id();
            $table->text('name');
            $table->timestamp('next_run_at');
            $table->timestamp('last_run_at')->nullable();
            $table->integer('schedule');
            $table->enum('type', ['system', 'custom']);
            $table->enum('status', ['running', 'paused']);
            $table->text('reserved_method')->nullable();
            $table->text('url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cron_jobs');
    }
};
