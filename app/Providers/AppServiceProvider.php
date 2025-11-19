<?php

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDates();
        $this->configureHttpMacros();
        $this->configureModels();
    }

    private function configureDates(): void
    {
        Date::use(CarbonImmutable::class);
    }

    private function configureHttpMacros(): void
    {
        Http::macro('cloudflareStatus', function () {
            return Http::baseUrl('https://www.cloudflarestatus.com/api/v2')
                ->timeout(10)
                ->retry(3, 100)
                ->withHeaders(['Accept' => 'application/json']);
        });
    }

    private function configureModels()
    {
        Model::unguard();
    }
}
