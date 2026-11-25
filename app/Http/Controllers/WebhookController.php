<?php

namespace App\Http\Controllers;

use App\Payments\InvalidWebhookSignature;
use App\Payments\PaymentGateway;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function __invoke(Request $request, PaymentGateway $gateway, PaymentService $payments)
    {
        try {
            $event = $gateway->parseWebhook($request);
        } catch (InvalidWebhookSignature $e) {
            Log::warning('Rejected webhook with invalid signature', ['gateway' => $gateway->name()]);

            return response()->json(['error' => 'invalid signature'], 400);
        }

        $fresh = $payments->handle($event);

        return response()->json(['received' => true, 'duplicate' => ! $fresh]);
    }
}
