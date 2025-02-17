<?php

namespace App\Services;

use App\Libraries\Utils;
use App\Models\Investment;
use App\Models\Investor;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class InvestorService
{
    public function __construct(){
    }

    public function list($active_only = false){
        $queryable = Investor::select("*");

        if($active_only){
            $queryable = $queryable->whereNotNull('approved_at');
        }

        $data = $queryable->get();

        return $data;
    }

    public function add($input){
        if(!is_array($input)){
            $input = json_encode(json_decode($input, true));
        }

        if(!empty($input['created_at'])){
            throw new Exception("Invalid Operation", 500);
        }

        $new = new Investor($input);
        $res = $new->save();

        return $new;
    }

    public function get($investor_id){
        $existing = Investor::where('investor_id', $investor_id)->first();

        return $existing;
    }

    public function update($input){
        if(!is_array($input)){
            // $input = json_encode(json_decode($input, true));
            $input = $input->toArray();
        }

        $existing = Investor::where('investor_id', $input['investor_id'])->first();
        $res = $existing->update($input);

        return $existing;
    }

    public function generateInvestmentId(){
        $currYear = date("Y");
        $currMonth = intval(date("n"));
        $currMonthRoman = Utils::numberToRomanRepresentation($currMonth);

        $inv_id_prefix = "INV/$currYear/$currMonthRoman/";
        $lastInvestment = Investment::
            where('investment_id', 'like', "$inv_id_prefix%")
            ->orderBy('investment_id', 'DESC')
            ->first();

        $lastNumber = 0;
        if(!empty($lastInvestment)){
            
        }
    }

    public function listInvestment($investor_id = null){
        $queryable = Investor::select("*");

        if(!empty($investor_id)){
            $queryable = $queryable->whereNotNull('approved_at');
        }

        $data = $queryable->get();

        return $data;
    }
}