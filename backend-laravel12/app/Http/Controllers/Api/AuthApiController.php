<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class AuthApiController extends Controller
{
    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name'     => 'required|string|max:50',
                'email'    => 'required|string|email|max:100|unique:users',
                'password' => 'required|string|min:6|confirmed',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $data             = $validator->validated();
            $data['password'] = Hash::make($data['password']);
            $user             = User::create($data);
            $token            = JWTAuth::fromUser($user);

            return response()->json([
                'success' => true,
                'message' => 'User registered successfully!',
                'data'    => [
                    'user'       => $user,
                    'token'      => $token,
                    'token_type' => 'Bearer',
                    'expires_in' => Auth::factory()->getTTL() * 60,
                ]
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors'  => $e->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email'    => 'required|email|max:100',
                'password' => 'required|string',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            if (!$token = JWTAuth::attempt($request->only('email', 'password'))) {
                return response()->json(['message' => 'Unauthorized access.'], 401);
            }

            return response()->json([
                'success' => true,
                'message' => 'User logged in successfully!',
                'data'    => [
                    'token'      => $token,
                    'token_type' => 'Bearer',
                    'expires_in' => Auth::factory()->getTTL() * 60,
                ]
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors'  => $e->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function me()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. No token provided or token invalid.',
            ], 401);
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'user' => $user,
            ]
        ], 200);
    }

    public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());

            return response()->json([
                'success' => true,
                'message' => 'Successfully logged out.',
            ], 200);
        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to log out.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function refresh()
    {
        try {
            if (!$token = JWTAuth::getToken()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token is missing.',
                ], 400);
            }

            if (!JWTAuth::parseToken()->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token has expired.',
                ], 401);
            }

            $newToken = JWTAuth::refresh($token);

            return response()->json([
                'success' => true,
                'message' => 'Token refresh successfully!',
                'data'    => [
                    'token'      => $newToken,
                    'token_type' => 'Bearer',
                    'expires_in' => JWTAuth::factory()->getTTL() * 60,
                ]
            ], 200);
        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to refresh token.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function checkTokenValidity()
    {
        try {
            if (!$token = JWTAuth::getToken()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token is missing or invalid.',
                ], 400);
            }

            $user = JWTAuth::toUser($token);

            return response()->json([
                'success' => true,
                'message' => 'Token is valid.',
                'data'    => [
                    'token'      => (string) $token,
                    'token_type' => 'Bearer',
                    'expires_in' => JWTAuth::factory()->getTTL() * 60,
                    'user'       => $user,
                ]
            ], 200);
        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token verification failed.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
