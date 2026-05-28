<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GalleryItem extends Model
{
    protected $fillable = [
        'type',
        'service_id',
        'stylist_id',
        'before_image_path',
        'after_image_path',
        'image_path',
        'client_name',
        'review_text',
        'rating',
        'is_featured',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'rating' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function stylist(): BelongsTo
    {
        return $this->belongsTo(Stylist::class);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Get the primary display image path.
     */
    public function displayImage(): ?string
    {
        return $this->image_path ?? $this->after_image_path;
    }

    /**
     * Get star rating as visual string.
     */
    public function ratingStars(): string
    {
        if (!$this->rating) return '';
        return str_repeat('★', $this->rating) . str_repeat('☆', 5 - $this->rating);
    }
}
