<?php

namespace App\Support;

class Money
{
    public static function format(int $minor, string $currency = 'usd'): string
    {
        return strtoupper($currency) === 'USD'
            ? '$'.number_format($minor / 100, 2)
            : number_format($minor / 100, 2).' '.strtoupper($currency);
    }
}
