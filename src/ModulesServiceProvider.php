<?php

namespace Kgoofori\LaravelModules;

use Illuminate\Support\ServiceProvider;

class ModulesServiceProvider extends ServiceProvider
{

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/config/module-generator.php', 'module-generator'
        );
    }

    /**
     * Bootstrap any package services.
     */
    public function boot(): void
    {

        $this->publishes([
            __DIR__.'/config/module-generator.php' => config_path('module-generator.php'),
        ]);

        if ($this->app->runningInConsole()) {
            $this->commands([
                GenerateCommand::class,
            ]);
        }
    }
}