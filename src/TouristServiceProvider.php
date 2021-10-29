<?php

namespace BadMushroom\LaravelTourist;

use BadMushroom\LaravelTourist\Commands\ClearTours;
use BadMushroom\LaravelTourist\Middleware\Tourism;
use BadMushroom\LaravelTourist\Parsers\UserAgentParser;
use BadMushroom\LaravelTourist\Parsers\UserAgentParserInterface;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;

class TouristServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            /**
             * Publish configuration file.
             */
            $this->publishes([
                __DIR__ . '/../config/tourist.php' => config_path('tourist.php'),
            ], 'config');

            /**
             * Load migrations.
             */
            $this->loadMigrationsFrom(__DIR__ . '/migrations');
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/tourist.php', 'tourist');

        /**
         * Bind Tour facade.
         */
        $this->app->bind('tour', function () {
            return new Tourist(app(Request::class), config('tourist'));
        });

        /**
         * Bind UserAgent prarser.
         */
        $this->app->bind(UserAgentParserInterface::class, UserAgentParser::class);

        /**
         * Register "clean up" command.
         */
        $this->app->bind('command.tourist:clear', ClearTours::class);
        $this->commands(['command.tourist:clear']);

        /**
         * Register middleware.
         */
        $router = $this->app['router'];
        $router->pushMiddlewareToGroup('tourism', Tourism::class);
    }
}
