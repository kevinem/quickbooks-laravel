<?php

namespace KevinEm\QuickBooks\Laravel\Providers;


use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;
use KevinEm\QuickBooks\Laravel\QuickBooks;
use Wheniwork\OAuth1\Client\Server\Intuit;

class QuickBooksLaravelServiceProvider extends ServiceProvider
{

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $config = $this->app['path.config'] . '/quickbooks.php';

        $this->publishes([
            __DIR__ . '/../config.php' => $config
        ]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config.php', 'quickbooks');

        $this->app->bind('quickbooks', function ($app) {
            $config = $app['config']['quickbooks'];

            return new QuickBooks($config, new Client(), new Intuit([
                'identifier' => $config['consumer_key'],
                'secret' => $config['consumer_secret'],
                'callback_uri' => $config['callback'],
            ]));
        });
    }
}