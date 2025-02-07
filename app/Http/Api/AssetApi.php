<?php

namespace App\Http\Api;

use App\Models\Dto\ApiResponse;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class AssetApi extends _BaseApi
{
    function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $maps = $this->dashboardbe_general_map();

        $sites = [];
        foreach($maps->hpls as $hpl){
            $sites []= [
                'site_name' => $hpl->site_name,
                'lokasi' => "$hpl->kelurahan, $hpl->kota_nama, $hpl->provinsi_nama",
                'lat' => $hpl->perolehan_lat,
                'long' => $hpl->perolehan_long
            ];
        }

        return response()->json(new ApiResponse($sites));
    }

    public function detail(){
        $params = request()->all();

        $maps = $this->dashboardbe_general_map();
        $selected_site = null;
        foreach($maps->hpls as $hpl){
            if($params['site_name'] == $hpl->site_name){
                $selected_site = $hpl;
                break;
            }
        }

        $data = Cache::remember("dashboardbe.detail.perolehan.$selected_site->perolehan_id", 300, function () use($selected_site) {
            $response = Http::post("$this->endpoints_dashboard_be/dashboard/services/detail/perolehan", [
                'token' => $this->generate_dashboardbe_token(),
                'perolehan_id' => $selected_site->perolehan_id,
            ]);

            $response_json = $response->json();
            if(empty($response_json)){
                throw new Exception($response->body(), 500);
            }

            return json_decode(json_encode($response_json));
        });

        $result = json_decode(json_encode($data->perolehan), true);
        $result['geojsons'] = $data->bidangs_geodata;
        $result['photos'] = $data->fotos;
        $result['masterplan'] = !empty($data->fotos)? $data->fotos[0]: [];

        return response()->json(new ApiResponse($result));
    }

    //privates
    private function dashboardbe_general_map(){
        $data = Cache::remember('dashboardbe.general.map', 300, function () {
            $response = Http::post("$this->endpoints_dashboard_be/dashboard/services/general/map", [
                'token' => $this->generate_dashboardbe_token(),
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
