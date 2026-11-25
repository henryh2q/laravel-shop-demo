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
    public function handle(): int
    {
        if ($this->option('fresh')) {
            $this->call('migrate:fresh', ['--force' => true, '--seed' => true]);

            return self::SUCCESS;
        }

        $this->call('migrate', ['--force' => true]);

        if (Product::count() === 0) {
            $this->call('db:seed', ['--force' => true]);
        }

        return self::SUCCESS;
    }
}
