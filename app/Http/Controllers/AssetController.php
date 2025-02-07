<?php

namespace App\Http\Controllers;

class AssetController extends _BaseController
{
    function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        return $this->includeYear(date("Y"));
    }
}
