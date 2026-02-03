<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Exception;

class PelangganController extends Controller
{
    /**
     * Display a listing of the resource.
     * GET /api/pelanggan
     */
    public function index()
    {
        try {
            $pelanggan = Cache::remember('pelanggan', 3600, function () {
                return Pelanggan::with('pelangganData')->get();
            });

            $response = [
                'success' => true,
                'message' => 'Data pelanggan berhasil diambil',
                'data' => $pelanggan
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
     * POST /api/pelanggan
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'pelanggan_nama' => 'required|string|max:150',
                'pelanggan_alamat' => 'required|string|max:200',
                'pelanggan_notelp' => 'required|string|size:13',
                'pelanggan_email' => 'required|email|max:100|unique:pelanggan,pelanggan_email',
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

            $pelanggan = Pelanggan::create($validator->validated());

            // Update cache dengan data terbaru
            Cache::put('pelanggan', Pelanggan::with('pelangganData')->get(), 3600);

            $response = [
                'success' => true,
                'message' => 'Data pelanggan berhasil ditambahkan',
                'data' => $pelanggan
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
     * GET /api/pelanggan/{id}
     */
    public function show($id)
    {
        try {
            // Cache key unik untuk setiap pelanggan
            $cacheKey = 'pelanggan_' . $id;

            // Cache pelanggan by ID selama 1 jam
            $pelanggan = Cache::remember($cacheKey, 3600, function () use ($id) {
                return Pelanggan::with('pelangganData', 'penyewaan')->find($id);
            });

            if (!$pelanggan) {
                $response = [
                    'success' => false,
                    'message' => 'Data pelanggan tidak ditemukan',
                    'data' => null
                ];

                return response()->json($response, 404);
            }

            $response = [
                'success' => true,
                'message' => 'Detail data pelanggan',
                'data' => $pelanggan
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
     * PUT/PATCH /api/pelanggan/{id}
     */
    public function update(Request $request, $id)
    {
        try {
            $pelanggan = Pelanggan::find($id);

            if (!$pelanggan) {
                $response = [
                    'success' => false,
                    'message' => 'Data pelanggan tidak ditemukan',
                    'data' => null
                ];

                return response()->json($response, 404);
            }

            $validator = Validator::make($request->all(), [
                'pelanggan_nama' => 'sometimes|required|string|max:150',
                'pelanggan_alamat' => 'sometimes|required|string|max:200',
                'pelanggan_notelp' => 'sometimes|required|string|size:13',
                'pelanggan_email' => 'sometimes|required|email|max:100|unique:pelanggan,pelanggan_email,' . $id . ',pelanggan_id',
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

            $pelanggan->update($validator->validated());

            // Update cache all pelanggan
            Cache::put('pelanggan', Pelanggan::with('pelangganData')->get(), 3600);

            // Update cache pelanggan by ID
            $cacheKey = 'pelanggan_' . $id;
            Cache::put($cacheKey, Pelanggan::with('pelangganData', 'penyewaan')->find($id), 3600);

            $response = [
                'success' => true,
                'message' => 'Data pelanggan berhasil diupdate',
                'data' => $pelanggan
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
     * DELETE /api/pelanggan/{id}
     */
    public function destroy($id)
    {
        try {
            $pelanggan = Pelanggan::find($id);

            if (!$pelanggan) {
                $response = [
                    'success' => false,
                    'message' => 'Data pelanggan tidak ditemukan',
                    'data' => null
                ];

                return response()->json($response, 404);
            }

            $pelanggan->delete();

            // Update cache all pelanggan
            Cache::put('pelanggan', Pelanggan::with('pelangganData')->get(), 3600);

            // Hapus cache pelanggan by ID
            $cacheKey = 'pelanggan_' . $id;
            Cache::forget($cacheKey);

            $response = [
                'success' => true,
                'message' => 'Data pelanggan berhasil dihapus',
                'data' => $pelanggan
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
