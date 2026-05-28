<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gallery_items', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['before_after', 'portfolio', 'testimonial']);
            $table->foreignId('service_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('stylist_id')->nullable()->constrained()->nullOnDelete();
            $table->string('before_image_path')->nullable();
            $table->string('after_image_path')->nullable();
            $table->string('image_path')->nullable();
            $table->string('client_name')->nullable();
            $table->text('review_text')->nullable();
            $table->unsignedTinyInteger('rating')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['is_featured', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gallery_items');
    }
};
