<?php

namespace App\Services;

use App\Libraries\Utils;
use App\Models\Investment;
use App\Models\Investor;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class DocumentService
{
    public function __construct(){
    }

    public function listProposal(){
        $queryable = Investor::select("*");

        $data = $queryable->get();

        return $data;
    }
}