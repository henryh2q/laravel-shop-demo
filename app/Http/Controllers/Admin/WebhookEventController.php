<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WebhookEvent;

class WebhookEventController extends Controller
{
    public function index()
    {
        return view('admin.webhooks.index', ['events' => WebhookEvent::latest()->paginate(25)]);
    }

    public function show(WebhookEvent $webhookEvent)
    {
        return view('admin.webhooks.show', ['event' => $webhookEvent]);
    }
}
