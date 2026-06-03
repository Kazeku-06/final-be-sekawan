<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Models\Admin;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    // ─── Login ───────────────────────────────────────────────────────────────

    /**
     * POST /api/auth/login
     * Autentikasi admin dan kembalikan JWT token.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $admin = Admin::where('admin_username', $request->admin_username)->first();

            if (!$admin || !Hash::check($request->admin_password, $admin->admin_password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Username atau password salah.',
                    'data'    => null,
                    'errors'  => null,
                ], 401);
            }

            $token = JWTAuth::fromUser($admin);

            return response()->json([
                'success' => true,
                'message' => 'Login berhasil.',
                'data'    => [
                    'admin'        => [
                        'admin_id'       => $admin->admin_id,
                        'admin_username' => $admin->admin_username,
                    ],
                    'access_token' => $token,
                    'token_type'   => 'bearer',
                    'expires_in'   => config('jwt.ttl') * 60,
                ],
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'There error in Internal Server',
                'data'    => null,
                'errors'  => $e->getMessage(),
            ], 500);
        }
    }

    // ─── Me / Profile ────────────────────────────────────────────────────────

    /**
     * GET /api/auth/me
     * Ambil data admin yang sedang login.
     */
    public function me(): JsonResponse
    {
        try {
            $admin = JWTAuth::parseToken()->authenticate();

            return response()->json([
                'success' => true,
                'message' => 'Successfully get profile',
                'data'    => [
                    'admin_id'       => $admin->admin_id,
                    'admin_username' => $admin->admin_username,
                ],
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'There error in Internal Server',
                'data'    => null,
                'errors'  => $e->getMessage(),
            ], 500);
        }
    }

    // ─── Logout ──────────────────────────────────────────────────────────────

    /**
     * POST /api/auth/logout
     * Invalidate token JWT yang aktif.
     */
    public function logout(): JsonResponse
    {
        try {
            JWTAuth::parseToken()->invalidate();

            return response()->json([
                'success' => true,
                'message' => 'Logout berhasil.',
                'data'    => null,
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'There error in Internal Server',
                'data'    => null,
                'errors'  => $e->getMessage(),
            ], 500);
        }
    }

    // ─── Refresh Token ───────────────────────────────────────────────────────

    /**
     * POST /api/auth/refresh
     * Refresh JWT token.
     */
    public function refresh(): JsonResponse
    {
        try {
            $newToken = JWTAuth::parseToken()->refresh();

            return response()->json([
                'success' => true,
                'message' => 'Token berhasil diperbarui.',
                'data'    => [
                    'access_token' => $newToken,
                    'token_type'   => 'bearer',
                    'expires_in'   => config('jwt.ttl') * 60,
                ],
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'There error in Internal Server',
                'data'    => null,
                'errors'  => $e->getMessage(),
            ], 500);
        }
    }
}
