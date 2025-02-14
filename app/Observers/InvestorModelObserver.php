<?php

namespace App\Observers;

use App\Models\Investor;
use DateTime;

class InvestorModelObserver
{
    /**
     * Handle the investor model "created" event.
     *
     * @param  \App\InvestorModel  $investorModel
     * @return void
     */
    public function created(Investor $investorModel)
    {
        $investorModel->created_at = new DateTime();
    }

    /**
     * Handle the investor model "updated" event.
     *
     * @param  \App\InvestorModel  $investorModel
     * @return void
     */
    public function updated(Investor $investorModel)
    {
        $investorModel->created_at = new DateTime();
    }

    /**
     * Handle the investor model "deleted" event.
     *
     * @param  \App\InvestorModel  $investorModel
     * @return void
     */
    public function deleted(Investor $investorModel)
    {
        //
    }

    /**
     * Handle the investor model "restored" event.
     *
     * @param  \App\InvestorModel  $investorModel
     * @return void
     */
    public function restored(Investor $investorModel)
    {
        //
    }

    /**
     * Handle the investor model "force deleted" event.
     *
     * @param  \App\InvestorModel  $investorModel
     * @return void
     */
    public function forceDeleted(Investor $investorModel)
    {
        //
    }
}
