<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Exception;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     * GET /api/admin
     */
    public function index()
    {
        try {
            $admin = Cache::remember('admin', 3600, function () {
                return Admin::all();
            });

            $response = [
                'success' => true,
                'message' => 'Data admin berhasil diambil',
                'data' => $admin
            ];

            return response()->json($response, 200)->header('Cache-Control', 'public, max-age=300');
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
     * Store a newly created resource in storage.
     * POST /api/admin
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'admin_username' => 'required|string|max:50|unique:admin,admin_username',
                'admin_password' => 'required|string|min:6|max:255',
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

            $admin = Admin::create([
                'admin_username' => $request->admin_username,
                'admin_password' => Hash::make($request->admin_password),
            ]);

            // Update cache dengan data terbaru
            Cache::put('admin', Admin::all(), 3600);

            $response = [
                'success' => true,
                'message' => 'Data admin berhasil ditambahkan',
                'data' => $admin
            ];

            return response()->json($response, 201);
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
     * Display the specified resource.
     * GET /api/admin/{id}
     */
    public function show($id)
    {
        try {
            // Cache key unik untuk setiap admin
            $cacheKey = 'admin_' . $id;

            // Cache admin by ID selama 1 jam
            $admin = Cache::remember($cacheKey, 3600, function () use ($id) {
                return Admin::find($id);
            });

            if (!$admin) {
                $response = [
                    'success' => false,
                    'message' => 'Data admin tidak ditemukan',
                    'data' => null
                ];

                return response()->json($response, 404);
            }

            $response = [
                'success' => true,
                'message' => 'Detail data admin',
                'data' => $admin
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
     * Update the specified resource in storage.
     * PUT/PATCH /api/admin/{id}
     */
    public function update(Request $request, $id)
    {
        try {
            $admin = Admin::find($id);

            if (!$admin) {
                $response = [
                    'success' => false,
                    'message' => 'Data admin tidak ditemukan',
                    'data' => null
                ];

                return response()->json($response, 404);
            }

            $validator = Validator::make($request->all(), [
                'admin_username' => 'sometimes|required|string|max:50|unique:admin,admin_username,' . $id . ',admin_id',
                'admin_password' => 'sometimes|required|string|min:6|max:255',
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

            if ($request->has('admin_username')) {
                $admin->admin_username = $request->admin_username;
            }

            if ($request->has('admin_password')) {
                $admin->admin_password = Hash::make($request->admin_password);
            }

            $admin->save();

            // Update cache all admin
            Cache::put('admin', Admin::all(), 3600);

            // Update cache admin by ID
            $cacheKey = 'admin_' . $id;
            Cache::put($cacheKey, Admin::find($id), 3600);

            $response = [
                'success' => true,
                'message' => 'Data admin berhasil diupdate',
                'data' => $admin
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
     * Remove the specified resource from storage.
     * DELETE /api/admin/{id}
     */
    public function destroy($id)
    {
        try {
            $admin = Admin::find($id);

            if (!$admin) {
                $response = [
                    'success' => false,
                    'message' => 'Data admin tidak ditemukan',
                    'data' => null
                ];

                return response()->json($response, 404);
            }

            $admin->delete();

            // Update cache all admin
            Cache::put('admin', Admin::all(), 3600);

            // Hapus cache admin by ID
            $cacheKey = 'admin_' . $id;
            Cache::forget($cacheKey);

            $response = [
                'success' => true,
                'message' => 'Data admin berhasil dihapus',
                'data' => $admin
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
}
