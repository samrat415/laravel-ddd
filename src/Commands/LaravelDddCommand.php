<?php

namespace Samrat415\LaravelDdd\Commands;

use Illuminate\Console\Command;

class LaravelDddCommand extends Command
{
    protected $signature = 'laravel-ddd';
    protected $description = 'Laravel DDD Tools and Installer';

    public function handle(): int
    {
        $this->info('📦 Laravel DDD Toolkit');
        $this->line('');
        $this->line('This package helps you structure Laravel projects using Domain-Driven Design (DDD).');
        $this->line('');

        $this->info('Available Commands:');
        $this->line('  👉  php artisan ddd:install     Set up DDD folder structure (src/, domains/, frontend/)');
        $this->line('  🛠️   More tools coming soon...');

        $this->line('');
        $this->comment('✅ Ready to build your next modular Laravel app!');

        return self::SUCCESS;
    }
}
