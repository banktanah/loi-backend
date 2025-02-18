<?php

namespace App\Http\Api;

use App\Models\Dto\ApiResponse;
use App\Services\PemanfaatanService;
use App\Services\PerolehanService;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class AssetApi extends _BaseApi
{
    private $perolehanService;
    private $pemanfaatanService;

    function __construct()
    {
        parent::__construct();
        $this->perolehanService = new PerolehanService();
        $this->pemanfaatanService = new PemanfaatanService();
    }

    public function index()
    {
        $maps = $this->perolehanService->getMapList();

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

        $data = $this->perolehanService->getDetail($params['site_name']);

        $result = json_decode(json_encode($data->perolehan), true);
        $result['geojsons'] = $data->bidangs_geodata;
        $result['photos'] = $data->fotos;

        if(!empty($params['with'])){
            if(in_array('profile', $params['with'])){
                $result['profile'] = $this->pemanfaatanService->getProfile($params['site_name']);
            }
            if(in_array('regulation', $params['with'])){
                $result['regulation'] = $this->pemanfaatanService->getRegulation($params['site_name']);
            }
            if(in_array('masterplan', $params['with'])){
                $result['masterplan'] = $this->pemanfaatanService->getMasterPlan($params['site_name']);
            }
        }

        return response()->json(new ApiResponse($result));
    }
}
