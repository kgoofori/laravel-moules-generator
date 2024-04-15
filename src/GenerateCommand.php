<?php

namespace Kgoofori\LaravelModules;

use Illuminate\Console\Command;

class GenerateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:generate {module} {--force : Overwrite existing views by default}';

    protected $module;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates module folder structure and scaffolding for laravel';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->module = ucwords($this->argument('module'));

        $directories = [
            'database',
            'database/migrations',
            'database/factories',
            'database/seeders',
            'Events',
            'Http',
            'Http/Controllers',
            'Http/Middlewares',
            'Http/Requests',
            'Http/Resources',
            'Jobs',
            'Listeners',
            'Models',
            'Providers',
            'routes',
            'tests'
        ];

        //create directories
        foreach ($directories as $dir) {
            if (! is_dir($directory = $this->modulePath($dir))) {
                mkdir($directory, 0755, true);
            }
        }

        //copy base controller
        $baseController = $this->modulePath('Http/Controllers/Controller.php');

        if (file_exists($baseController) && ! $this->option('force')) {
            if ($this->components->confirm("The [Controller.php] file already exists. Do you want to replace it?", true)) {
                file_put_contents($baseController, $this->compileStub('Http/Controllers/Controller'));
            }
        } else {
            file_put_contents($baseController, $this->compileStub('Http/Controllers/Controller'));
        }

        //copy testcase
        // $appServiceProvider = $this->modulePath("tests/{$this->module}TestCase.php");

        // if (file_exists($appServiceProvider) && ! $this->option('force')) {
        //     if ($this->components->confirm("The [{$this->module}TestCase.php] file already exists. Do you want to replace it?", true)) {
        //         file_put_contents(
        //             $appServiceProvider, 
        //             str_replace('{{sample}}', $this->module,$this->compileStub('tests/SampleTestCase') )
        //         );
        //     }
        // } else {
        //     file_put_contents($appServiceProvider, str_replace('{{sample}}', $this->module,$this->compileStub('tests/SampleTestCase')));
        // }

        //copy providers
        $appServiceProvider = $this->modulePath("Providers/{$this->module}ServiceProvider.php");

        if (file_exists($appServiceProvider) && ! $this->option('force')) {
            if ($this->components->confirm("The [{$this->module}ServiceProvider.php] file already exists. Do you want to replace it?", true)) {
                file_put_contents(
                    $appServiceProvider, 
                    str_replace('{{sample}}', $this->module,$this->compileStub('Providers/SampleServiceProvider') )
                );
            }
        } else {
            file_put_contents($appServiceProvider, str_replace('{{sample}}', $this->module,$this->compileStub('Providers/SampleServiceProvider')));
        }


        $providers = ['EventServiceProvider', 'RouteServiceProvider'];
        foreach ($providers as $provider) {

            $providerPath = $this->modulePath("Providers/{$provider}.php");

            if (file_exists($providerPath) && ! $this->option('force')) {
                if ($this->components->confirm("The [{$provider}.php] file already exists. Do you want to replace it?", true)) {
                    file_put_contents($providerPath, $this->compileStub('Providers/'.$provider));
                }
            } else {
                file_put_contents($providerPath, $this->compileStub('Providers/'.$provider));
            }
        }

        //copy routes
        $routes = ['api', 'web'];
        foreach ($routes as $route) {

            $routePath = $this->modulePath("routes/{$route}.php");
            if (file_exists($routePath) && ! $this->option('force')) {
                if ($this->components->confirm("The [{$route}.php] file already exists. Do you want to replace it?", true)) {
                    file_put_contents($routePath, "<?php\n");
                }
            } else {
                file_put_contents($routePath, "<?php\n");
            }
        }
    }

    function modulePath($file) : string 
    {
        return app_path('../'.config('module-generator.modules_path'). "/{$this->module}/$file");
    }

    protected function compileStub($stub)
    {

        return str_replace(
            '{{nameSpace}}',
            config('module-generator.namespace_prefix').'\\'.$this->module,
            file_get_contents(__DIR__.'/stubs/'.$stub.'.stub')
        );
    }
}
