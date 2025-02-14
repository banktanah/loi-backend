<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class PemanfaatanService
{
    private $endpoints_dashboard_be;
    private $tokenService;

    public function __construct(){
        $this->endpoints_dashboard_be = config('app.endpoints.dashboard_be');
        $this->tokenService = new TokenService();
    }

    public function getProfile($site_name){
        $data = Cache::remember("dashboardbe.pemanfaatan.profile.$site_name", 300, function () use($site_name) {
            $response = Http::post("$this->endpoints_dashboard_be/dashboard/services/pemanfaatan/profile", [
                'token' => $this->tokenService->get(),
                'site_name' => $site_name
            ]);

            $response_json = $response->json();
            if(empty($response_json)){
                return null;
            }

            return json_decode(json_encode($response_json))[0];
        });

        return $data;
    }

    public function getRegulation($site_name){
        $data = Cache::remember("dashboardbe.pemanfaatan.regulation.$site_name", 300, function () use($site_name) {
            $response = Http::post("$this->endpoints_dashboard_be/dashboard/services/pemanfaatan/regulation", [
                'token' => $this->tokenService->get(),
                'site_name' => $site_name
            ]);

            $response_json = $response->json();
            // if(empty($response_json)){
            //     return null;
            // }

            return json_decode(json_encode($response_json));
        });

        return $data;
    }

    public function getMasterPlan($site_name){
        $data = Cache::remember("dashboardbe.pemanfaatan.masterplan.$site_name", 300, function () use($site_name) {
            $response = Http::post("$this->endpoints_dashboard_be/dashboard/services/pemanfaatan/masterplan", [
                'token' => $this->tokenService->get(),
                'site_name' => $site_name
            ]);

            $response_json = $response->json();
            if(empty($response_json)){
                return null;
            }

            return json_decode(json_encode($response_json))[0];
        });

        return $data;
    }
}