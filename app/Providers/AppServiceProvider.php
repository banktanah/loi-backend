<?php

namespace App\Providers;

use App\Models\Investment;
use App\Models\Investor;
use App\Observers\InvestmentModelObserver;
use App\Observers\InvestorModelObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Investor::observe(InvestorModelObserver::class);
        Investment::observe(InvestmentModelObserver::class);
    }
}
