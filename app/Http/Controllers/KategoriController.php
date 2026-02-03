<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Exception;

class KategoriController extends Controller
{
    /**
     * Display a listing of the resource.
     * GET /api/kategori
     */
    public function index()
    {
        try {
            $kategori = Cache::remember('kategori', 3600, function () {
                return Kategori::with('alat')->get();
            });

            $response = [
                'success' => true,
                'message' => 'Data kategori berhasil diambil',
                'data' => $kategori
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
     * POST /api/kategori
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'kategori_nama' => 'required|string|max:100',
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

            $kategori = Kategori::create($validator->validated());

            // Update cache
            Cache::put('kategori', Kategori::with('alat')->get(), 3600);

            $response = [
                'success' => true,
                'message' => 'Data kategori berhasil ditambahkan',
                'data' => $kategori
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
     * GET /api/kategori/{id}
     */
    public function show($id)
    {
        try {
            $cacheKey = 'kategori_' . $id;

            $kategori = Cache::remember($cacheKey, 3600, function () use ($id) {
                return Kategori::with('alat')->find($id);
            });

            if (!$kategori) {
                $response = [
                    'success' => false,
                    'message' => 'Data kategori tidak ditemukan',
                    'data' => null
                ];

                return response()->json($response, 404);
            }

            $response = [
                'success' => true,
                'message' => 'Detail data kategori',
                'data' => $kategori
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
     * PATCH /api/kategori/{id}
     */
    public function update(Request $request, $id)
    {
        try {
            $kategori = Kategori::find($id);

            if (!$kategori) {
                $response = [
                    'success' => false,
                    'message' => 'Data kategori tidak ditemukan',
                    'data' => null
                ];

                return response()->json($response, 404);
            }

            $validator = Validator::make($request->all(), [
                'kategori_nama' => 'sometimes|required|string|max:100',
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

            $kategori->update($validator->validated());

            // Update cache
            Cache::put('kategori', Kategori::with('alat')->get(), 3600);
            Cache::put('kategori_' . $id, Kategori::with('alat')->find($id), 3600);

            $response = [
                'success' => true,
                'message' => 'Data kategori berhasil diupdate',
                'data' => $kategori
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
     * DELETE /api/kategori/{id}
     */
    public function destroy($id)
    {
        try {
            $kategori = Kategori::find($id);

            if (!$kategori) {
                $response = [
                    'success' => false,
                    'message' => 'Data kategori tidak ditemukan',
                    'data' => null
                ];

                return response()->json($response, 404);
            }

            $kategori->delete();

            // Update cache
            Cache::put('kategori', Kategori::with('alat')->get(), 3600);
            Cache::forget('kategori_' . $id);

            $response = [
                'success' => true,
                'message' => 'Data kategori berhasil dihapus',
                'data' => $kategori
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
