<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

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
        Schema::defaultStringLength(191);
        Blade::directive('money', function ($amount) {
            return "<?php
                if($amount < 0) {
                    $amount *= -1;
                    echo '-$' . number_format($amount, 2);
                } else {
                    echo '$' . number_format($amount, 2);
                }
            ?>";
     });

    }
}
