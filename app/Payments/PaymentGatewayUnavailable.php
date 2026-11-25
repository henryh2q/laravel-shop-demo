<?php

namespace App\Payments;

use RuntimeException;

/** The payment provider could not create a checkout (bad keys, outage, network). */
class PaymentGatewayUnavailable extends RuntimeException {}
