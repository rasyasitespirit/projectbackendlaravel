<?php

namespace App\Http\Controllers;

use App\Models\Penyewaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Exception;

class PenyewaanController extends Controller
{
    /**
     * Display a listing of the resource.
     * GET /api/penyewaan
     */
    public function index()
    {
        try {
            $penyewaan = Cache::remember('penyewaan', 3600, function () {
                return Penyewaan::with('pelanggan')->get();
            });

            $response = [
                'success' => true,
                'message' => 'Data penyewaan berhasil diambil',
                'data' => $penyewaan
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
     * POST /api/penyewaan
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'penyewaan_pelanggan_id' => 'required|exists:pelanggan,pelanggan_id',
                'penyewaan_tglsewa' => 'required|date',
                'penyewaan_tglkembali' => 'required|date|after_or_equal:penyewaan_tglsewa',
                'penyewaan_sttspembayaran' => 'sometimes|in:Lunas,Belum Dibayar,DP',
                'penyewaan_sttskembali' => 'sometimes|in:Sudah Kembali,Belum Kembali',
                'penyewaan_totalharga' => 'required|integer|min:0',
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

            $penyewaan = Penyewaan::create($validator->validated());

            // Update cache dengan data terbaru
            Cache::put('penyewaan', Penyewaan::with('pelanggan')->get(), 3600);

            // Update cache pelanggan terkait
            $pelangganCacheKey = 'pelanggan_' . $request->penyewaan_pelanggan_id;
            Cache::forget($pelangganCacheKey);

            $response = [
                'success' => true,
                'message' => 'Data penyewaan berhasil ditambahkan',
                'data' => $penyewaan
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
     * GET /api/penyewaan/{id}
     */
    public function show($id)
    {
        try {
            // Cache key unik untuk setiap penyewaan
            $cacheKey = 'penyewaan_' . $id;

            // Cache penyewaan by ID selama 1 jam
            $penyewaan = Cache::remember($cacheKey, 3600, function () use ($id) {
                return Penyewaan::with('pelanggan', 'penyewaanDetail')->find($id);
            });

            if (!$penyewaan) {
                $response = [
                    'success' => false,
                    'message' => 'Data penyewaan tidak ditemukan',
                    'data' => null
                ];

                return response()->json($response, 404);
            }

            $response = [
                'success' => true,
                'message' => 'Detail data penyewaan',
                'data' => $penyewaan
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
     * PUT/PATCH /api/penyewaan/{id}
     */
    public function update(Request $request, $id)
    {
        try {
            $penyewaan = Penyewaan::find($id);

            if (!$penyewaan) {
                $response = [
                    'success' => false,
                    'message' => 'Data penyewaan tidak ditemukan',
                    'data' => null
                ];

                return response()->json($response, 404);
            }

            $validator = Validator::make($request->all(), [
                'penyewaan_pelanggan_id' => 'sometimes|required|exists:pelanggan,pelanggan_id',
                'penyewaan_tglsewa' => 'sometimes|required|date',
                'penyewaan_tglkembali' => 'sometimes|required|date|after_or_equal:penyewaan_tglsewa',
                'penyewaan_sttspembayaran' => 'sometimes|in:Lunas,Belum Dibayar,DP',
                'penyewaan_sttskembali' => 'sometimes|in:Sudah Kembali,Belum Kembali',
                'penyewaan_totalharga' => 'sometimes|required|integer|min:0',
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

            $oldPelangganId = $penyewaan->penyewaan_pelanggan_id;

            $penyewaan->update($validator->validated());

            // Update cache all penyewaan
            Cache::put('penyewaan', Penyewaan::with('pelanggan', 'penyewaanDetail')->get(), 3600);

            // Update cache penyewaan by ID
            $cacheKey = 'penyewaan_' . $id;
            Cache::put($cacheKey, Penyewaan::with('pelanggan', 'penyewaanDetail')->find($id), 3600);

            // Update cache pelanggan terkait
            Cache::forget('pelanggan_' . $oldPelangganId);
            if ($request->has('penyewaan_pelanggan_id')) {
                Cache::forget('pelanggan_' . $request->penyewaan_pelanggan_id);
            }

            $response = [
                'success' => true,
                'message' => 'Data penyewaan berhasil diupdate',
                'data' => $penyewaan
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
     * DELETE /api/penyewaan/{id}
     */
    public function destroy($id)
    {
        try {
            $penyewaan = Penyewaan::find($id);

            if (!$penyewaan) {
                $response = [
                    'success' => false,
                    'message' => 'Data penyewaan tidak ditemukan',
                    'data' => null
                ];

                return response()->json($response, 404);
            }

            $pelangganId = $penyewaan->penyewaan_pelanggan_id;

            $penyewaan->delete();

            // Update cache all penyewaan
            Cache::put('penyewaan', Penyewaan::with('pelanggan', 'penyewaanDetail')->get(), 3600);

            // Hapus cache penyewaan by ID
            $cacheKey = 'penyewaan_' . $id;
            Cache::forget($cacheKey);

            // Update cache pelanggan terkait
            Cache::forget('pelanggan_' . $pelangganId);

            $response = [
                'success' => true,
                'message' => 'Data penyewaan berhasil dihapus',
                'data' => $penyewaan
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
