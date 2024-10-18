<?php

namespace Glint\Glint;

use Illuminate\Support\ServiceProvider;

class GlintServiceProvider extends ServiceProvider
{

    private function getConfig(): string
    {
        return __DIR__.'/../config/glint.php';
    }

    public function register(): void
    {
        $this->mergeConfigFrom($this->getConfig(), 'glint');
    }

    public function boot(): void
    {
        $config = $this->getConfig();

        $this->publishes([
            $config => config_path('glint.php')
        ], ['glint', 'glint-config']);

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'glint');

    }
}
