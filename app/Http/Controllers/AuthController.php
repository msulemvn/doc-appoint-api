<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use OpenApi\Annotations as OA;

class AuthController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth:api', except: ['login']),
        ];
    }

    /**
     * @OA\Post(
     *     path="/api/login",
     *     tags={"Authentication"},
     *     summary="Login user",
     *     description="Authenticate user and return JWT token",
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"email","password"},
     *
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Login successful"),
     *             @OA\Property(property="statusCode", type="integer", example=200),
     *             @OA\Property(property="status", type="string", example="OK"),
     *             @OA\Property(property="access_token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGc..."),
     *             @OA\Property(property="token_type", type="string", example="bearer"),
     *             @OA\Property(property="user", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="email", type="string", example="user@example.com")
     *             ),
     *             @OA\Property(property="expires_in", type="integer", example=86400)
     *         )
     *     ),
     *
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=422, description="Validation Error")
     * )
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only(['email', 'password']);

        if (! $token = auth('api')->attempt($credentials)) {
            return $this->error('Unauthorized', null, 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * @OA\Post(
     *     path="/api/me",
     *     tags={"Authentication"},
     *     summary="Get authenticated user",
     *     description="Get the currently authenticated user's information",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="User retrieved successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="User retrieved successfully"),
     *             @OA\Property(property="statusCode", type="integer", example=200),
     *             @OA\Property(property="status", type="string", example="OK"),
     *             @OA\Property(property="user", type="object")
     *         )
     *     ),
     *
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function me()
    {
        return $this->success(['user' => auth('api')->user()], 'User retrieved successfully');
    }

    /**
     * @OA\Post(
     *     path="/api/logout",
     *     tags={"Authentication"},
     *     summary="Logout user",
     *     description="Invalidate the user's JWT token",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successfully logged out",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Successfully logged out"),
     *             @OA\Property(property="statusCode", type="integer", example=200),
     *             @OA\Property(property="status", type="string", example="OK")
     *         )
     *     ),
     *
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function logout()
    {
        auth('api')->logout();

        return $this->success([], 'Successfully logged out');
    }

    /**
     * @OA\Post(
     *     path="/api/refresh",
     *     tags={"Authentication"},
     *     summary="Refresh JWT token",
     *     description="Get a new JWT token using the current token",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Token refreshed successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Login successful"),
     *             @OA\Property(property="access_token", type="string")
     *         )
     *     ),
     *
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function refresh()
    {
        return $this->respondWithToken(auth('api')->refresh());
    }

    protected function respondWithToken($token)
    {
        return $this->success([
            'access_token' => $token,
            'token_type' => 'bearer',
            'user' => auth('api')->user(),
            'expires_in' => auth('api')->factory()->getTTL() * 60,
        ], 'Login successful');
    }
}
