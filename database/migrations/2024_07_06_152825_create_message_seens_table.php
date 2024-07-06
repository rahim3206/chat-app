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
        Schema::create('message_seens', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('sender_id')->nullable();
            $table->bigInteger('receiver_id')->nullable();
            $table->bigInteger('message_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('message_seens');
    }
};
