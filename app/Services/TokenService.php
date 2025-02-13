<?php

namespace App\Services;

class TokenService
{
    protected $endpoints_dashboard_be;

    public function __construct(){
        $this->endpoints_dashboard_be = config('app.endpoints.dashboard_be');
    }

    public function get(){
        return md5(date("dmY") . "2023@dashbOard_bbt-ri");
    }
}