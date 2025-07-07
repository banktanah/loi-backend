<?php

namespace App\Http\Api;

use App\Http\Controllers\Controller;
use App\Services\UsersService;
use App\Models\Dto\ApiResponse;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth; 

class UsersApi extends Controller
{
    private $usersService;

    public function __construct(UsersService $usersService)
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
        $this->usersService = $usersService;
    }

     /**
     * Handle registration from multipart/form-data.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        // Validation rules for form-data
        $validator = Validator::make($request->all(), [
            // Investor data
            'investor_id' => 'required|string|max:100|unique:investor,investor_id',
            'investor_type_id' => 'required|string',
            'name' => 'required|string|max:1000',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'province_id' => 'nullable|string',
            'regency_id' => 'nullable|string',
            'district_id' => 'nullable|string',
            'village_id' => 'nullable|string',
            
            // File upload validation
            'company_profile' => 'required|file|mimes:pdf,doc,docx|max:5120', // Example: PDF/Word, max 5MB

            // User data
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

         if ($validator->fails()) {
            // THE FIX:
            // 1. Get the validation error object.
            $errorMessages = $validator->errors()->all(); 
            // 2. Join all error messages into a single string.
            $flatErrorMessage = implode(' ', $errorMessages); 
            
            // 3. Pass the flattened string as the message to the DTO.
            $responseDto = new ApiResponse(ApiResponse::CODE_FAILED, $flatErrorMessage);
            return response()->json($responseDto, 422);
        }

        try {
            $validatedData = $validator->validated();
            if ($request->hasFile('company_profile')) {
                $path = $request->file('company_profile')->store('company_profiles', 'public');
                $validatedData['company_profile_url'] = Storage::disk('public')->url($path);
            } else {
                 throw new Exception("Company profile file is required.", 422);
            }
            $user = $this->usersService->registerInvestorAndUser($validatedData);
            
            // This now uses the 1-argument constructor for success
            $responseDto = new ApiResponse($user); 
            // We'll add a custom success message manually for consistency
            $responseDto->message = "Registration successful."; 
            
            return response()->json($responseDto, 201);
        } catch (Exception $e) {
             Log::error("User Registration Failed: " . $e->getMessage());
            $code = is_numeric($e->getCode()) && $e->getCode() > 0 ? $e->getCode() : 500;
            // This uses the 2-argument constructor for general errors
            $responseDto = new ApiResponse(ApiResponse::CODE_ERROR, $e->getMessage());
            return response()->json($responseDto, $code);
        }
    }

    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            $errorMessage = implode(' ', $validator->errors()->all());
            return response()->json(new ApiResponse(ApiResponse::CODE_FAILED, $errorMessage), 422);
        }
        
        try {
            $token = $this->usersService->loginUser($validator->validated());

            if (!$token) {
                return response()->json(new ApiResponse(ApiResponse::CODE_FAILED, 'The provided credentials do not match our records.'), 401);
            }

            $data = $this->respondWithToken($token);
            return response()->json(new ApiResponse($data), 200);

        } catch (Exception $e) {
            $code = is_numeric($e->getCode()) && $e->getCode() > 0 ? $e->getCode() : 500;
            return response()->json(new ApiResponse(ApiResponse::CODE_ERROR, $e->getMessage()), $code);
        }
    }

    public function info(): JsonResponse
    {
        $user = Auth::guard('api')->user()->load('investor');
        return response()->json(new ApiResponse($user));
    }

    public function logout(): JsonResponse
    {
        Auth::guard('api')->logout();
        return response()->json(new ApiResponse());
    }

    public function refresh(): JsonResponse
    {
        $refreshedToken = Auth::guard('api')->refresh();
        $data = $this->respondWithToken($refreshedToken);
        return response()->json(new ApiResponse($data));
    }

    protected function respondWithToken(string $token): array
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::guard('api')->factory()->getTTL() * 60
        ];
    }
}