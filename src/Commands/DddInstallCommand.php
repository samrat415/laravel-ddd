<?php
namespace Samrat415\LaravelDdd\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class DddInstallCommand extends Command
{
    protected $signature = 'ddd:install';
    protected $description = 'Set up DDD folders like src/, domains/, frontend/ and optional portals';

    public function handle()
    {
        $this->info('🧱 Setting up your Laravel DDD folder structure...');

        // Explanatory notes
        $this->line('');
        $this->info('📂 `src/`         → Your core application modules (services, adapters, integrations)');
        $this->info('📂 `domains/`     → Where you write your API logic (Domain-oriented services & routes)');
        $this->info('📂 `frontend/`    → Livewire + Blade-based frontend portals');
        $this->info('⚠️  Inertia.js support is not yet here — it will be available soon.');
        $this->line('');

        $this->createBaseFolders();

        // Optional Domain portals
        if ($this->confirm('❓ Do you want to create domain portals (e.g. Admin, User)?')) {
            $domains = $this->askWithMultiple('Enter domain portal names (comma separated)', []);
            foreach ($domains as $domain) {
                $this->createDomainsPortal("domains/$domain");
            }
        }

        // Optional Frontend portals
        if ($this->confirm('❓ Do you want to create frontend portals (e.g. Customer, Vendor)?')) {
            $frontends = $this->askWithMultiple('Enter frontend portal names (comma separated)', []);
            foreach ($frontends as $frontend) {
                $this->createPortal("frontend/$frontend");
            }
        }

        $this->info('');
        $this->info('✅ Laravel DDD setup complete!');
    }

    protected function createBaseFolders()
    {
        foreach (['src', 'domains', 'frontend'] as $folder) {
            if (!is_dir(base_path($folder))) {
                File::makeDirectory(base_path($folder), 0755, true);
                $this->info("📁 Created: $folder/");
            } else {
                $this->line("✔️  $folder/ already exists");
            }
        }
    }

    protected function createPortal(string $path)
    {
        $fullPath = base_path($path);
        $structure = ['routes', 'Views', 'Livewire', 'Middleware', 'config', 'lang'];

        foreach ($structure as $sub) {
            File::makeDirectory("$fullPath/$sub", 0755, true);
        }

        $this->info("📦 Portal structure created at: $path/");
    }
    protected function createDomainsPortal(string $path)
    {
        $fullPath = base_path($path);
        $structure = ['routes', 'Api','Resources'];

        foreach ($structure as $sub) {
            File::makeDirectory("$fullPath/$sub", 0755, true);
        }

        $this->info("📦 Portal structure created at: $path/");
    }

    protected function askWithMultiple(string $question, array $default): array
    {
        $answer = $this->ask($question);
        return array_filter(array_map('trim', explode(',', $answer)));
    }
}
