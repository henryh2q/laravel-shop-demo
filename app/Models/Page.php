<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasTranslations;

    protected $fillable = ['slug', 'title', 'body', 'is_published'];

    protected $casts = [
        'title' => 'array',
        'body' => 'array',
        'is_published' => 'boolean',
    ];
}
