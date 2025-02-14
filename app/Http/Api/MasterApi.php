<?php

namespace App\Http\Api;

use App\Models\Dto\ApiResponse;
use App\Models\Masters\InvestorType;
use App\Services\PemanfaatanService;
use App\Services\PerolehanService;
use App\Services\WilayahService;

class MasterApi extends _BaseApi
{
    private $perolehanService;
    private $pemanfaatanService;
    private $wilayahService;

    function __construct()
    {
        parent::__construct();
        $this->perolehanService = new PerolehanService();
        $this->pemanfaatanService = new PemanfaatanService();
        $this->wilayahService = new WilayahService();
    }

    public function investorType()
    {
        $investorTypes = InvestorType::get();

        return response()->json(new ApiResponse($investorTypes));
    }

    public function provinces()
    {
        $data = $this->wilayahService->provinces();

        return response()->json(new ApiResponse($data));
    }

    public function regencies($province_id)
    {
        $data = $this->wilayahService->regencies($province_id);

        return response()->json(new ApiResponse($data));
    }

    public function districts($regency_id)
    {
        $data = $this->wilayahService->districts($regency_id);

        return response()->json(new ApiResponse($data));
    }

    public function villages($district_id)
    {
        $data = $this->wilayahService->villages($district_id);

        return response()->json(new ApiResponse($data));
    }
}
