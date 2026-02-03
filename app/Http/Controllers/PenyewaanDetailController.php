<?php

namespace App\Http\Controllers;

use App\Models\PenyewaanDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Exception;

class PenyewaanDetailController extends Controller
{
    public function index()
    {
        try {
            $penyewaanDetail = Cache::remember('penyewaan_detail', 3600, function () {
                return PenyewaanDetail::with('penyewaan', 'alat')->get();
            });

            $response = [
                'success' => true,
                'message' => 'Data detail penyewaan berhasil diambil',
                'data' => $penyewaanDetail
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

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'penyewaan_detail_penyewaan_id' => 'required|exists:penyewaan,penyewaan_id',
                'penyewaan_detail_alat_id' => 'required|exists:alat,alat_id',
                'penyewaan_detail_jumlah' => 'required|integer|min:1',
                'penyewaan_detail_subharga' => 'required|integer|min:0',
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

            $penyewaanDetail = PenyewaanDetail::create($validator->validated());

            Cache::put('penyewaan_detail', PenyewaanDetail::with('penyewaan', 'alat')->get(), 3600);

            $response = [
                'success' => true,
                'message' => 'Data detail penyewaan berhasil ditambahkan',
                'data' => $penyewaanDetail
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

    public function show($id)
    {
        try {
            $cacheKey = 'penyewaan_detail_' . $id;

            $penyewaanDetail = Cache::remember($cacheKey, 3600, function () use ($id) {
                return PenyewaanDetail::with('penyewaan', 'alat')->find($id);
            });

            if (!$penyewaanDetail) {
                $response = [
                    'success' => false,
                    'message' => 'Data detail penyewaan tidak ditemukan',
                    'data' => null
                ];

                return response()->json($response, 404);
            }

            $response = [
                'success' => true,
                'message' => 'Detail data penyewaan',
                'data' => $penyewaanDetail
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

    public function update(Request $request, $id)
    {
        try {
            $penyewaanDetail = PenyewaanDetail::find($id);

            if (!$penyewaanDetail) {
                $response = [
                    'success' => false,
                    'message' => 'Data detail penyewaan tidak ditemukan',
                    'data' => null
                ];

                return response()->json($response, 404);
            }

            $validator = Validator::make($request->all(), [
                'penyewaan_detail_penyewaan_id' => 'sometimes|required|exists:penyewaan,penyewaan_id',
                'penyewaan_detail_alat_id' => 'sometimes|required|exists:alat,alat_id',
                'penyewaan_detail_jumlah' => 'sometimes|required|integer|min:1',
                'penyewaan_detail_subharga' => 'sometimes|required|integer|min:0',
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

            $penyewaanDetail->update($validator->validated());

            Cache::put('penyewaan_detail', PenyewaanDetail::with('penyewaan', 'alat')->get(), 3600);
            Cache::put('penyewaan_detail_' . $id, PenyewaanDetail::with('penyewaan', 'alat')->find($id), 3600);

            $response = [
                'success' => true,
                'message' => 'Data detail penyewaan berhasil diupdate',
                'data' => $penyewaanDetail
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

    public function destroy($id)
    {
        try {
            $penyewaanDetail = PenyewaanDetail::find($id);

            if (!$penyewaanDetail) {
                $response = [
                    'success' => false,
                    'message' => 'Data detail penyewaan tidak ditemukan',
                    'data' => null
                ];

                return response()->json($response, 404);
            }

            $penyewaanDetail->delete();

            Cache::put('penyewaan_detail', PenyewaanDetail::with('penyewaan', 'alat')->get(), 3600);
            Cache::forget('penyewaan_detail_' . $id);

            $response = [
                'success' => true,
                'message' => 'Data detail penyewaan berhasil dihapus',
                'data' => $penyewaanDetail
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
