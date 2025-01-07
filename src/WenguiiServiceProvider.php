<?php

namespace Wenguii;

use Illuminate\Support\ServiceProvider;

class WenguiiServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap les services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/wenguii.php' => config_path('wenguii.php'),
        ], 'config');
    }

    /**
     * Enregistrer les services dans le conteneur.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(WenguiiClient::class, function ($app) {
            return new WenguiiClient(
                config('wenguii.cdprt'),
                config('wenguii.usr'),
                config('wenguii.pwd'),
                config('wenguii.base_url') 
            );
        });
    }
}
