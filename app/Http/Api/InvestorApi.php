<?php

namespace App\Http\Api;

use App\Models\Dto\ApiResponse;
use App\Models\Investor;
use App\Services\InvestorService;
use App\Services\PemanfaatanService;
use App\Services\PerolehanService;
use DateTime;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class InvestorApi extends _BaseApi
{
    private $investorService;
    private $pemanfaatanService;

    function __construct()
    {
        parent::__construct();
        $this->investorService = new InvestorService();
    }

    public function index()
    {
    }

    public function list()
    {
        $params = request()->all();

        $test = $this->investorService->addInvestment([
            'investor_id' => 'NIK_KTP_OR_NIB',
            'site_name' => 'SITE_NAME',
            'name' => 'Investment Name',
            'description' => 'This is just a test'
        ]);

        $active_only = false;
        if(!empty($params['active_only'])){
            $active_only = filter_var($params['active_only'], FILTER_VALIDATE_BOOLEAN);
        }

        $data = $this->investorService->list($active_only);

        return response()->json(new ApiResponse($data));
    }

    public function register(){
        $params = request()->all();

        $data = $this->investorService->add($params);

        return response()->json(new ApiResponse($data));
    }

    public function approve_registration(){
        $params = request()->all();

        $res = $this->investorService->approveRegistration($params['investor_id']);

        return response()->json(new ApiResponse($res));
    }

    public function detail(){
        $params = request()->all();

        $data = $this->investorService->get($params['investor_id']);

        return response()->json(new ApiResponse($data));
    }

    public function listInvestment(){
        $params = request()->all();

        $data = $this->investorService->listInvestment($params['investor_id']);

        return response()->json(new ApiResponse($data));
    }

    public function addInvestment(){
        $params = request()->all();

        $data = $this->investorService->addInvestment($params);

        return response()->json(new ApiResponse($data));
    }

    public function addDocuments(){
        $params = request()->all();

        $data = $this->investorService->addDocuments($params);

        return response()->json(new ApiResponse($data));
    }

    public function approveInvestment(){
        $params = request()->all();

        $data = $this->investorService->approveInvestment($params['investment_id']);

        return response()->json(new ApiResponse($data));
    }
}
