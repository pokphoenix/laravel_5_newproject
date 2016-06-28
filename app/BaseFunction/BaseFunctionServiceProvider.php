<?php namespace App\BaseFunction;

use Illuminate\Support\ServiceProvider;

class BaseFunctionServiceProvider extends ServiceProvider {

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {

        $this->app['basefunction'] = $this->app->share(function($app)
        {
            return new BaseFunctionManager;
        });

        // Shortcut so developers don't need to add an Alias in app/config/app.php
        $this->app->booting(function()
        {
            $loader = \Illuminate\Foundation\AliasLoader::getInstance();
            $loader->alias('BaseFunction', 'App\BaseFunction\Facades\BaseFunction');
            $loader->alias('BF', 'App\BaseFunction\Facades\BaseFunction');
        });
    }

}
