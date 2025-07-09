<?php

namespace App\Http\Api;

use App\Models\Dto\ApiResponse;
use App\Services\InvestmentService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class InvestmentApi extends _BaseApi
{
    private $investmentService;

    public function __construct(InvestmentService $investmentService)
    {
        parent::__construct();
        $this->investmentService = $investmentService;
    }

    /**
     * Endpoint untuk mengajukan minat investasi baru.
     */
    public function submit(Request $request): JsonResponse
    {
        // Validasi untuk data form dan file
        $validator = Validator::make($request->all(), [
            'perolehan_id' => 'required|string|max:50',
            'site_name' => 'required|string|max:100',
            'tujuan_pemanfaatan' => 'required|string|max:255',
            'deskripsi_rencana_proyek' => 'required|string',
            'skema_pemanfaatan' => 'required|in:Sewa,HGU,HGB,Lainnya',
            
            // Validasi untuk file dokumen (contoh)
            'dokumen_proposal' => 'required|file|mimes:pdf,docx,doc,png|max:5120', // Proposal (PDF, max 5MB)
            'dokumen_profil_perusahaan' => 'required|file|mimes:pdf,docx,doc,png|max:5120', // Profil (PDF, max 5MB)
            'dokumen_lainnya' => 'nullable|file|mimes:pdf,docx,doc,zip|max:10240', // Opsional (PDF/ZIP, max 10MB)
        ]);

        if ($validator->fails()) {
            $responseDto = new ApiResponse(null, 'Validation failed', $validator->errors());
            return response()->json($responseDto, 422);
        }

        try {
            $validatedData = $validator->validated();
            
            // Pisahkan file dari data teks
            $files = [
                'PROPOSAL' => $request->file('dokumen_proposal'),
                'PROFIL_PERUSAHAAN' => $request->file('dokumen_profil_perusahaan'),
                'LAINNYA' => $request->file('dokumen_lainnya'),
            ];

            $investment = $this->investmentService->createInvestment($validatedData, array_filter($files));

            $responseDto = new ApiResponse($investment, "Investment interest submitted successfully.");
            return response()->json($responseDto, 201);

        } catch (Exception $e) {
            Log::error("Investment submission failed: " . $e->getMessage());
            $code = is_numeric($e->getCode()) && $e->getCode() > 0 ? $e->getCode() : 500;
            $responseDto = new ApiResponse(null, $e->getMessage());
            return response()->json($responseDto, $code);
        }
    }

    /**
     * Endpoint untuk melihat daftar investasi milik user.
     */
    public function list(): JsonResponse
    {
        try {
            $investments = $this->investmentService->getUserInvestments();
            $responseDto = new ApiResponse($investments);
            return response()->json($responseDto, 200);
        } catch (Exception $e) {
            Log::error("Failed to fetch investments: " . $e->getMessage());
            $code = is_numeric($e->getCode()) && $e->getCode() > 0 ? $e->getCode() : 500;
            $responseDto = new ApiResponse(null, $e->getMessage());
            return response()->json($responseDto, $code);
        }
    }

    /**
     * Endpoint untuk melihat detail satu investasi.
     */
    public function detail(int $id): JsonResponse
    {
        try {
            $investment = $this->investmentService->getInvestmentDetail($id);
            $responseDto = new ApiResponse($investment);
            return response()->json($responseDto, 200);
        } catch (Exception $e) {
            Log::error("Failed to fetch investment detail for ID {$id}: " . $e->getMessage());
            $code = is_numeric($e->getCode()) && $e->getCode() > 0 ? $e->getCode() : 500;
            $responseDto = new ApiResponse(null, $e->getMessage());
            return response()->json($responseDto, $code);
        }
    }
}