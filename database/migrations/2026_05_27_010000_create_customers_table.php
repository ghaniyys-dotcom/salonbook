<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('phone', 20)->unique();
            $table->string('name');
            $table->string('email')->nullable();
            $table->date('birthday')->nullable();
            $table->dateTime('first_booking_at')->nullable();
            $table->dateTime('last_booking_at')->nullable();
            $table->unsignedInteger('total_bookings')->default(0);
            $table->unsignedBigInteger('total_spent')->default(0);
            $table->foreignId('preferred_stylist_id')->nullable()->constrained('stylists')->nullOnDelete();
            $table->text('internal_notes')->nullable();
            $table->timestamps();

            $table->index('phone');
            $table->index('last_booking_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
