<?php

namespace App\Services;

use App\Libraries\Utils;
use App\Models\Investment;
use App\Models\InvestmentDocument;
use App\Models\Investor;
use DateTime;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class InvestorService
{
    static $required_document_types  = [
        [
            'type' => 'PROPOSAL',
            'required' => true
        ],
        [
            'type' => 'NPWP',
            'required' => true
        ],
        [
            'type' => 'SURAT_USAHA',
            'required' => true
        ]
    ];

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

    public function approveRegistration($investor_id){
        $existing = $this->get($investor_id);
        $existing->approved_at = new DateTime();
        $existing->approved_by = "system";
        $data = $this->update($existing);
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

        $lastCounter = 0;
        if(!empty($lastInvestment)){
            $investmentIdArr = explode("/", $lastInvestment->investment_id);
            $lastCounter = intval($investmentIdArr[count($investmentIdArr)-1]);
        }
        $lastCounter++;
        $lastCounter = sprintf('%04d', $lastCounter);

        return $inv_id_prefix.$lastCounter;
    }

    public function listInvestment($investor_id = null){
        $queryable = Investment::
            select("*")
            ->with('documents');

        if(!empty($investor_id)){
            $queryable = $queryable->where('investor_id', $investor_id);
        }

        $data = $queryable->get();

        return $data;
    }

    public function addInvestment($input){
        if(!is_array($input)){
            $input = json_encode(json_decode($input, true));
        }

        if(!empty($input['created_at'])){
            throw new Exception("Invalid Operation", 500);
        }

        try {
            DB::beginTransaction();

            $newInvestment = new Investment($input);
            $res = $newInvestment->save();

            if(empty($input['proposal'])){
                throw new Exception("Must include a proposal for register an Investment", 403);
            }

            $input['proposal']['investment_id'] = $newInvestment->investment_id;
            $input['proposal']['document_type'] = 'PROPOSAL';
            $newDocument = new InvestmentDocument($input['proposal']);
            $res = $newDocument->save();

            DB::commit();
    
            return $newInvestment;
        } catch (\Throwable $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function addDocuments($input){
        if(!is_array($input)){
            $input = json_encode(json_decode($input, true));
        }

        if(!empty($input['created_at'])){
            throw new Exception("Invalid Operation", 500);
        }

        try {
            DB::beginTransaction();

            foreach($input['documents'] as $doc){
                $doc['investment_id'] = $input['investment_id'];
                $newDocument = new InvestmentDocument($doc);
                $res = $newDocument->save();
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function approveInvestment($investment_id){
        $required_documents = [];
        foreach(InvestorService::$required_document_types as $loop){
            if($loop['required'] == true){
                $required_documents []= $loop['type'];
            }
        }
        $required_documents_str = implode(', ', $required_documents);

        $investment = Investment::where('investment_id', $investment_id)->first();

        $existing_docs = [];
        $documents = InvestmentDocument::where('investment_id', $investment_id)->get();
        foreach($documents as $doc){
            $existing_docs []= $doc->document_type;
        }

        foreach(InvestorService::$required_document_types as $loop){
            if($loop['required'] == true){
                if(!in_array($loop['type'], $existing_docs)){
                    throw new Exception("Investment must have: $required_documents_str to be approved", 403);
                }
            }
        }

        $investment->approved_at = new DateTime();
        $investment->approved_by = "system";
        $investment->save();

        return $investment;
    }
}