<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasTranslations;

    protected $fillable = ['slug', 'name', 'sort_order'];

    protected $casts = ['name' => 'array'];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
