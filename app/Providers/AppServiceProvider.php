<?php

namespace App\Providers;

use App\Support\Clinic;
use App\Support\Content;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(Content::class);
        $this->app->singleton(Clinic::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::share('clinic', $this->app->make(Clinic::class));
    }
}
