<?php

namespace App\Http\Api;

use App\Http\Controllers\Controller;
use App\Services\UsersService;
use App\Models\Dto\ApiResponse;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class UsersApi extends Controller
{
    private $usersService;

    public function __construct(UsersService $usersService)
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
        $this->usersService = $usersService;
    }

    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'investor_id' => 'required|string|max:100|unique:investor,investor_id',
            'investor_type_id' => 'required|string',
            'name' => 'required|string|max:1000',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            $errorMessage = implode(' ', $validator->errors()->all());
            return response()->json(new ApiResponse(ApiResponse::CODE_FAILED, $errorMessage), 422);
        }

        try {
            $user = $this->usersService->registerInvestorAndUser($validator->validated());
            return response()->json(new ApiResponse($user), 201);
        } catch (Exception $e) {
            $code = is_numeric($e->getCode()) && $e->getCode() > 0 ? $e->getCode() : 500;
            return response()->json(new ApiResponse(ApiResponse::CODE_ERROR, $e->getMessage()), $code);
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