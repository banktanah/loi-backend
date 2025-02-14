<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class WilayahService
{
    private $endpoint = "https://emsifa.github.io/api-wilayah-indonesia/api";

    public function __construct(){
    }

    public function provinces(){
        $data = Cache::remember('wilayah.provinces', 300, function () {
            $response = Http::get("$this->endpoint/provinces.json");

            $response_json = $response->json();
            if(empty($response_json)){
                throw new Exception($response->body(), 500);
            }

            return json_decode(json_encode($response_json));
        });

        return $data;
    }

    public function regencies($province_id){
        $data = Cache::remember("wilayah.regencies.$province_id", 300, function () use($province_id) {
            $response = Http::get("$this->endpoint/regencies/$province_id.json");

            $response_json = $response->json();
            if(empty($response_json)){
                throw new Exception($response->body(), 500);
            }

            return json_decode(json_encode($response_json));
        });

        return $data;
    }

    public function districts($regency_id){
        $data = Cache::remember("wilayah.districts.$regency_id", 300, function () use($regency_id) {
            $response = Http::get("$this->endpoint/districts/$regency_id.json");

            $response_json = $response->json();
            if(empty($response_json)){
                throw new Exception($response->body(), 500);
            }

            return json_decode(json_encode($response_json));
        });

        return $data;
    }

    public function villages($district_id){
        $data = Cache::remember("wilayah.villages.$district_id", 300, function () use($district_id) {
            $response = Http::get("$this->endpoint/villages/$district_id.json");

            $response_json = $response->json();
            if(empty($response_json)){
                throw new Exception($response->body(), 500);
            }

            return json_decode(json_encode($response_json));
        });

        return $data;
    }
}