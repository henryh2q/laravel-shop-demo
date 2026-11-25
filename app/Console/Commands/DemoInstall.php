<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('demo:install {--fresh : Wipe all data and reseed (used to reset the public demo)}')]
#[Description('Run migrations and seed demo data when the database is empty.')]
class DemoInstall extends Command
{
    private const CATALOG_MARKER = 'ceremonial-matcha-uji';

    public function handle(): int
    {
        if ($this->option('fresh')) {
            $this->call('migrate:fresh', ['--force' => true, '--seed' => true]);

            return self::SUCCESS;
        }

        $this->call('migrate', ['--force' => true]);

        if (Product::count() === 0) {
            $this->call('db:seed', ['--force' => true]);
        } elseif (Product::where('slug', self::CATALOG_MARKER)->doesntExist()) {
            // The demo catalog changed: start again from fresh sample data.
            $this->call('migrate:fresh', ['--force' => true, '--seed' => true]);
        }

        return self::SUCCESS;
    }
}
