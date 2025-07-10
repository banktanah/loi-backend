<?php

namespace App\Observers;

use App\Models\Investment;
use App\Services\InvestorService;

class InvestmentModelObserver
{
    private $investorService;
    
    function __construct()
    {
        $this->investorService = new InvestorService();
        $a = 1;
    }

    /**
     * Handle the investment "created" event.
     *
     * @param  \App\Investment  $investment
     * @return void
     */
    public function creating(Investment $investment)
    {
        
    }

    /**
     * Handle the investment "updated" event.
     *
     * @param  \App\Investment  $investment
     * @return void
     */
    public function updated(Investment $investment)
    {
        //
    }

    /**
     * Handle the investment "deleted" event.
     *
     * @param  \App\Investment  $investment
     * @return void
     */
    public function deleted(Investment $investment)
    {
        //
    }

    /**
     * Handle the investment "restored" event.
     *
     * @param  \App\Investment  $investment
     * @return void
     */
    public function restored(Investment $investment)
    {
        //
    }

    /**
     * Handle the investment "force deleted" event.
     *
     * @param  \App\Investment  $investment
     * @return void
     */
    public function forceDeleted(Investment $investment)
    {
        //
    }
}
