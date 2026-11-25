<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory, HasTranslations;

    protected $fillable = [
        'category_id', 'slug', 'sku', 'name', 'description', 'price', 'stock', 'image_url', 'image_credit', 'is_active',
    ];

    protected $casts = [
        'name' => 'array',
        'description' => 'array',
        'price' => 'integer',
        'stock' => 'integer',
        'is_active' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /** Sized image URL; Unsplash images are resized by their CDN. */
    public function imageSrc(int $size = 600): ?string
    {
        if (! $this->image_url) {
            return null;
        }

        return str_starts_with($this->image_url, 'https://images.unsplash.com/')
            ? $this->image_url."?w={$size}&h={$size}&fit=crop&auto=format&q=75"
            : $this->image_url;
    }

    /** @return array{name: string, url: string}|null */
    public function imageCredit(): ?array
    {
        if (! $this->image_credit) {
            return null;
        }

        [$name, $username] = array_pad(explode('|', $this->image_credit, 2), 2, null);

        return ['name' => $name, 'url' => 'https://unsplash.com/@'.$username.'?utm_source=laravel_shop_demo&utm_medium=referral'];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
