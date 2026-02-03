<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Exception;

class AlatController extends Controller
{
    public function index()
    {
        try {
            $alat = Cache::remember('alat', 3600, function () {
                return Alat::with('kategori')->get();
            });

            $response = [
                'success' => true,
                'message' => 'Data alat berhasil diambil',
                'data' => $alat
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
                'alat_kategori_id' => 'required|exists:kategori,kategori_id',
                'alat_nama' => 'required|string|max:150',
                'alat_deskripsi' => 'required|string|max:255',
                'alat_hargaperhari' => 'required|integer|min:0',
                'alat_stok' => 'required|integer|min:0',
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

            $alat = Alat::create($validator->validated());

            Cache::put('alat', Alat::with('kategori')->get(), 3600);

            $response = [
                'success' => true,
                'message' => 'Data alat berhasil ditambahkan',
                'data' => $alat
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
            $cacheKey = 'alat_' . $id;

            $alat = Cache::remember($cacheKey, 3600, function () use ($id) {
                return Alat::with('kategori')->find($id);
            });

            if (!$alat) {
                $response = [
                    'success' => false,
                    'message' => 'Data alat tidak ditemukan',
                    'data' => null
                ];

                return response()->json($response, 404);
            }

            $response = [
                'success' => true,
                'message' => 'Detail data alat',
                'data' => $alat
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
            $alat = Alat::find($id);

            if (!$alat) {
                $response = [
                    'success' => false,
                    'message' => 'Data alat tidak ditemukan',
                    'data' => null
                ];

                return response()->json($response, 404);
            }

            $validator = Validator::make($request->all(), [
                'alat_kategori_id' => 'sometimes|required|exists:kategori,kategori_id',
                'alat_nama' => 'sometimes|required|string|max:150',
                'alat_deskripsi' => 'sometimes|required|string|max:255',
                'alat_hargaperhari' => 'sometimes|required|integer|min:0',
                'alat_stok' => 'sometimes|required|integer|min:0',
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

            $alat->update($validator->validated());

            Cache::put('alat', Alat::with('kategori')->get(), 3600);
            Cache::put('alat_' . $id, Alat::with('kategori')->find($id), 3600);

            $response = [
                'success' => true,
                'message' => 'Data alat berhasil diupdate',
                'data' => $alat
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
            $alat = Alat::find($id);

            if (!$alat) {
                $response = [
                    'success' => false,
                    'message' => 'Data alat tidak ditemukan',
                    'data' => null
                ];

                return response()->json($response, 404);
            }

            $alat->delete();

            Cache::put('alat', Alat::with('kategori')->get(), 3600);
            Cache::forget('alat_' . $id);

            $response = [
                'success' => true,
                'message' => 'Data alat berhasil dihapus',
                'data' => $alat
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
