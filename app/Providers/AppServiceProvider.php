<?php

namespace App\Providers;

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
        // Register Blade directive for integer formatting
        \Illuminate\Support\Facades\Blade::directive('int', function ($expression) {
            return "<?php echo \App\Helpers\NumberHelper::toInteger($expression); ?>";
        });

        // Register Blade directive for smart number formatting
        \Illuminate\Support\Facades\Blade::directive('smartNumber', function ($expression) {
            return "<?php echo \App\Helpers\NumberHelper::formatSmart($expression); ?>";
        });

        // Register Blade directive for currency formatting
        \Illuminate\Support\Facades\Blade::directive('currency', function ($expression) {
            return "<?php echo \App\Helpers\NumberHelper::formatCurrency($expression); ?>";
        });
    }
}
