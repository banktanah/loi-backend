<?php

namespace App\Http\Api;

use App\Models\Dto\ApiResponse;
use Illuminate\Support\Facades\Http;

class AssetApi extends _BaseApi
{
    function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $response = Http::post("$this->endpoints_dashboard_be/dashboard/services/general", [
            'token' => $this->generate_dashboardbe_token(),
            'year' => '',
        ]);
        $data = $response->json();

        return response()->json(new ApiResponse($data));
    }
}
