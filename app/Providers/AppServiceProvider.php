<?php

namespace App\Providers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Resources\Json\JsonResource;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        /** URL config */
        URL::forceScheme(config('app.protocol'));

        /** DB config */
        Schema::defaultStringLength(191);

        /** Rate limit config */
        $this->configRateLimiterForWeb();
        $this->configRateLimiterForApi();

        /** Password rules default */
        $this->configPasswordRule();

        /** Disable the wrapping of the resource */
        JsonResource::withoutWrapping();
    }

    /**
     * Configuring the maximum number of requests per minute for the `web` route group
     *
     * @return void
     */
    private function configRateLimiterForWeb(): void
    {
        RateLimiter::for('web', function (Request $request) {
            return $request->user()
                ? Limit::perMinute(200)->by($request->user()->getKey())
                : Limit::perMinute(100)->by($request->ip());
        });
    }

    /**
     * Configuring the maximum number of requests per minute for the `api` route group
     *
     * @return void
     */
    private function configRateLimiterForApi(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return $request->user()
                ? Limit::perMinute(200)->by($request->user()->getKey())
                : Limit::perMinute(100)->by($request->ip());
        });
    }

    /**
     * Default settings for passwords in different types of environments.
     *
     * @return void
     */
    private function configPasswordRule(): void
    {
        Password::defaults(function () {
            /** @var \Illuminate\Foundation\Application */
            $app = $this->app;
            $rule = Password::min(8);

            return $app->isProduction()
                        ? $rule->symbols()->mixedCase()->numbers()
                        : $rule;
        });
    }
}
