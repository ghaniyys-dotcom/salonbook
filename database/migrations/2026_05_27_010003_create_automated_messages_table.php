<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('automated_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained()->cascadeOnDelete();
            $table->enum('type', ['reminder', 'thank_you', 'rebooking', 'birthday']);
            $table->dateTime('scheduled_at');
            $table->dateTime('sent_at')->nullable();
            $table->enum('channel', ['whatsapp', 'email'])->default('whatsapp');
            $table->enum('status', ['pending', 'sent', 'failed'])->default('pending');
            $table->text('message_content')->nullable();
            $table->timestamps();

            $table->index(['type', 'status', 'scheduled_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('automated_messages');
    }
};
