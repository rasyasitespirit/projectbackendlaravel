<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;

class AuthController extends Controller
{
    /**
     * Login admin dan generate JWT token
     * POST /api/auth/login
     */
    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'admin_username' => 'required|string',
                'admin_password' => 'required|string',
            ]);

            if ($validator->fails()) {
                $response = [
                    'success' => false,
                    'message' => 'Validasi gagal. Silakan periksa data Anda.',
                    'data' => null,
                    'errors' => $validator->errors()
                ];

                return response()->json($response, 400);
            }

            // Attempt login dengan credentials
            $credentials = [
                'admin_username' => $request->admin_username,
                'password' => $request->admin_password
            ];

            if (!$token = auth('api')->attempt($credentials)) {
                $response = [
                    'success' => false,
                    'message' => 'Username atau password salah',
                    'data' => null
                ];

                return response()->json($response, 401);
            }

            $response = [
                'success' => true,
                'message' => 'Login berhasil',
                'data' => [
                    'admin' => auth('api')->user(),
                    'token' => $token,
                    'token_type' => 'bearer',
                    'expires_in' => auth('api')->factory()->getTTL() * 60
                ]
            ];

            return response()->json($response, 200);
        } catch (Exception $error) {
            $response = [
                'success' => false,
                'message' => 'Terjadi kesalahan pada server',
                'data' => null,
                'errors' => $error->getMessage()
            ];

            return response()->json($response, 500);
        }
    }

    /**
     * Get authenticated admin profile
     * GET /api/auth/me
     */
    public function me()
    {
        try {
            $response = [
                'success' => true,
                'message' => 'Data admin berhasil diambil',
                'data' => auth('api')->user()
            ];

            return response()->json($response, 200);
        } catch (Exception $error) {
            $response = [
                'success' => false,
                'message' => 'Token tidak valid atau sudah expired',
                'data' => null,
                'errors' => $error->getMessage()
            ];

            return response()->json($response, 401);
        }
    }

    /**
     * Logout admin (invalidate token)
     * POST /api/auth/logout
     */
    public function logout()
    {
        try {
            auth('api')->logout();

            $response = [
                'success' => true,
                'message' => 'Logout berhasil',
                'data' => null
            ];

            return response()->json($response, 200);
        } catch (Exception $error) {
            $response = [
                'success' => false,
                'message' => 'Terjadi kesalahan pada server',
                'data' => null,
                'errors' => $error->getMessage()
            ];

            return response()->json($response, 500);
        }
    }

    /**
     * Refresh JWT token
     * POST /api/auth/refresh
     */
    public function refresh()
    {
        try {
            $newToken = auth('api')->refresh();

            $response = [
                'success' => true,
                'message' => 'Token berhasil di-refresh',
                'data' => [
                    'token' => $newToken,
                    'token_type' => 'bearer',
                    'expires_in' => auth('api')->factory()->getTTL() * 60
                ]
            ];

            return response()->json($response, 200);
        } catch (Exception $error) {
            $response = [
                'success' => false,
                'message' => 'Token tidak dapat di-refresh',
                'data' => null,
                'errors' => $error->getMessage()
            ];

            return response()->json($response, 401);
        }
    }
}
