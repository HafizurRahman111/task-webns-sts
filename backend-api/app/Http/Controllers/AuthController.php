<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiter;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // Register a new user
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:4|max:255',
            'email' => 'required|string|email|min:4|max:255|unique:users',
            'phone' => 'required|string|min:4|max:20|unique:users',
            'password' => 'required|string|min:8|max:50|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->role,
                'created_at' => $user->created_at,
            ],
        ], 201);
    }

    // Login an existing user
    public function login(Request $request)
    {
        $limiter = app(RateLimiter::class);
        $key = 'login:' . $request->ip();

        if ($limiter->tooManyAttempts($key, 5)) {
            return response()->json([
                'message' => 'Too many login attempts. Please try again later.',
            ], 429);
        }

        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255|exists:users,email',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $email = filter_var($request->email, FILTER_SANITIZE_EMAIL);
        $password = filter_var($request->password, FILTER_SANITIZE_STRING);

        $user = User::where('email', $email)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            $limiter->hit($key, 60);

            return response()->json([
                'message' => 'The provided credentials are incorrect.',
            ], 401);
        }

        $limiter->clear($key);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'User logged in successfully',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->role,
            ],
        ], 200);
    }

    // Logout the user
    public function logout(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }

        $tokensDeleted = $user->tokens()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
            'tokens_deleted' => $tokensDeleted,
        ]);
    }

    // Get current user info
    public function currentUser(Request $request)
    {
        try {
            $user = $request->user();
            return response()->json(['data' => $user], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch user information'], 500);
        }
    }

    public function refresh(Request $request)
    {
        try {
            $refreshToken = $request->cookie('refresh_token') ?? $request->input('refresh_token');

            if (!$refreshToken) {
                return response()->json(['error' => 'Refresh token missing'], 401);
            }

            $response = $this->guard()->refresh();

            return $this->respondWithToken($response);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Could not refresh token',
                'message' => $e->getMessage()
            ], 401);
        }
    }
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'refresh_token' => request()->cookie('refresh_token') ?? null
        ]);
    }

}
