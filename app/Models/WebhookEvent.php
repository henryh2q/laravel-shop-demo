<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebhookEvent extends Model
{
    protected $fillable = ['gateway', 'event_id', 'type', 'payload', 'status', 'error'];

    protected $casts = ['payload' => 'array'];
}
