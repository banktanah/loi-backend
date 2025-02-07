<?php

namespace App\Http\Api;

use Illuminate\Routing\Controller as BaseController;

class _BaseApi extends BaseController
{
    protected $endpoints_dashboard_be;
    
    function __construct()
    {
        $this->endpoints_dashboard_be = config('app.endpoints.dashboard_be');
    }

    protected function generate_dashboardbe_token(){
        return md5(date("dmY") . "2023@dashbOard_bbt-ri");
    }
}
