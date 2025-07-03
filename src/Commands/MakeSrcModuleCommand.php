<?php

namespace Samrat415\LaravelDdd\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Config;

class MakeSrcModuleCommand extends Command
{
    protected $signature = 'ddd:src {name : ModuleName (e.g. Admin)}';
    protected $description = 'Create a new DDD module inside src/<module>';

    public function handle(): int
    {
        $module = $this->argument('name');
        $module = trim($module, " /");

        $basePath = base_path("src/$module");

        if (is_dir($basePath)) {
            $this->error("❌ Module $module already exists in src/");
            return self::FAILURE;
        }

        $paths = [
            "$basePath/Controllers",
            "$basePath/DTO",
            "$basePath/routes",
            "$basePath/Views",
            "$basePath/Models",
            "$basePath/Service",
            "$basePath/Enums",
        ];

        if (Config::get('laravel-ddd.livewire_support', false)) {
            $paths[] = "$basePath/Livewire";
        }

        foreach ($paths as $path) {
            File::makeDirectory($path, 0755, true);
        }

        // Create a routes stub file if not exists
        $routesPath = "$basePath/routes/web.php";
        if (!file_exists($routesPath)) {
            File::put($routesPath, "<?php\n\nuse Illuminate\\Support\\Facades\\Route;\n\n// Define web routes here\n");
        }

        $this->info("✅ Src module created at: src/$module");

        return self::SUCCESS;
    }
}
