<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class PerolehanService
{
    private $endpoints_dashboard_be;
    private $tokenService;

    public function __construct(){
        $this->endpoints_dashboard_be = config('app.endpoints.dashboard_be');
        $this->tokenService = new TokenService();
    }

    public function getMapList(){
        $data = Cache::remember('dashboardbe.general.map', 300, function () {
            $response = Http::post("$this->endpoints_dashboard_be/dashboard/services/general/map", [
                'token' => $this->tokenService->get(),
            ]);

            $response_json = $response->json();
            if(empty($response_json)){
                throw new Exception($response->body(), 500);
            }

            return json_decode(json_encode($response_json));
        });

        return $data;
    }

    public function getDetail($site_name){
        $maps = $this->getMapList();

        $selected_site = null;
        foreach($maps->hpls as $hpl){
            if($hpl->site_name == $site_name){
                $selected_site = $hpl;
                break;
            }
        }

        if(empty($selected_site)){
            return null;
        }

        $data = Cache::remember("dashboardbe.detail.perolehan.$selected_site->perolehan_id", 300, function () use($selected_site) {
            $response = Http::post("$this->endpoints_dashboard_be/dashboard/services/detail/perolehan", [
                'token' => $this->tokenService->get(),
                'perolehan_id' => $selected_site->perolehan_id,
            ]);

            $response_json = $response->json();
            if(empty($response_json)){
                throw new Exception($response->body(), 500);
            }

            return json_decode(json_encode($response_json));
        });

        return $data;
    }
}