<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('coinbase_webhook_calls', function (Blueprint $table) {
            $table->id();
            $table->string('type')->nullable();
            $table->text('payload')->nullable();
            $table->text('exception')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coinbase_webhook_calls');
    }
};
