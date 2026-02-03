<?php

namespace App\Http\Controllers;

use App\Models\PelangganData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Exception;

class PelangganDataController extends Controller
{
    /**
     * Display a listing of the resource.
     * GET /api/pelanggan-data
     */
    public function index()
    {
        try {
            $pelangganData = Cache::remember('pelanggan_data', 3600, function () {
                return PelangganData::with('pelanggan')->get();
            });

            $response = [
                'success' => true,
                'message' => 'Data dokumen pelanggan berhasil diambil',
                'data' => $pelangganData
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
     * POST /api/pelanggan-data
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'pelanggan_data_pelanggan_id' => 'required|exists:pelanggan,pelanggan_id',
                'pelanggan_data_jenis' => 'required|in:KTP,SIM',
                'pelanggan_data_file' => 'required|file|mimes:jpg,jpeg,png|max:2048',
            ], [
                'pelanggan_data_pelanggan_id.required' => 'ID pelanggan wajib diisi',
                'pelanggan_data_pelanggan_id.exists' => 'ID pelanggan tidak ditemukan',
                'pelanggan_data_jenis.required' => 'Jenis dokumen wajib diisi',
                'pelanggan_data_jenis.in' => 'Jenis dokumen harus KTP atau SIM',
                'pelanggan_data_file.required' => 'File dokumen wajib diupload',
                'pelanggan_data_file.file' => 'File yang diupload tidak valid',
                'pelanggan_data_file.mimes' => 'File harus memiliki format .jpg, .png, atau .jpeg',
                'pelanggan_data_file.max' => 'Ukuran file maksimal 2MB',
            ]);

            if ($validator->fails()) {
                $response = [
                    'success' => false,
                    'message' => 'Gagal menambahkan data dokumen pelanggan!',
                    'data' => null,
                    'errors' => [$validator->errors()]
                ];

                return response()->json($response, 422);
            }

            // Upload file
            $file = $request->file('pelanggan_data_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('pelanggan_data', $filename, 'public');

            $pelangganData = PelangganData::create([
                'pelanggan_data_pelanggan_id' => $request->pelanggan_data_pelanggan_id,
                'pelanggan_data_jenis' => $request->pelanggan_data_jenis,
                'pelanggan_data_file' => $path,
            ]);

            // Update cache dengan data terbaru
            Cache::put('pelanggan_data', PelangganData::with('pelanggan')->get(), 3600);

            // Update cache pelanggan terkait
            $pelangganCacheKey = 'pelanggan_' . $request->pelanggan_data_pelanggan_id;
            Cache::forget($pelangganCacheKey);

            $response = [
                'success' => true,
                'message' => 'Data dokumen pelanggan berhasil ditambahkan',
                'data' => $pelangganData
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
     * GET /api/pelanggan-data/{id}
     */
    public function show($id)
    {
        try {
            // Cache key unik untuk setiap pelanggan data
            $cacheKey = 'pelanggan_data_' . $id;

            // Cache pelanggan data by ID selama 1 jam
            $pelangganData = Cache::remember($cacheKey, 3600, function () use ($id) {
                return PelangganData::with('pelanggan')->find($id);
            });

            if (!$pelangganData) {
                $response = [
                    'success' => false,
                    'message' => 'Data dokumen pelanggan tidak ditemukan',
                    'data' => null
                ];

                return response()->json($response, 404);
            }

            $response = [
                'success' => true,
                'message' => 'Detail data dokumen pelanggan',
                'data' => $pelangganData
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
     * PUT/PATCH /api/pelanggan-data/{id}
     */
    public function update(Request $request, $id)
    {
        try {
            $pelangganData = PelangganData::find($id);

            if (!$pelangganData) {
                $response = [
                    'success' => false,
                    'message' => 'Data dokumen pelanggan tidak ditemukan',
                    'data' => null
                ];

                return response()->json($response, 404);
            }

            $validator = Validator::make($request->all(), [
                'pelanggan_data_pelanggan_id' => 'sometimes|required|exists:pelanggan,pelanggan_id',
                'pelanggan_data_jenis' => 'sometimes|required|in:KTP,SIM',
                'pelanggan_data_file' => 'sometimes|file|mimes:jpg,jpeg,png|max:2048',
            ], [
                'pelanggan_data_pelanggan_id.required' => 'ID pelanggan wajib diisi',
                'pelanggan_data_pelanggan_id.exists' => 'ID pelanggan tidak ditemukan',
                'pelanggan_data_jenis.required' => 'Jenis dokumen wajib diisi',
                'pelanggan_data_jenis.in' => 'Jenis dokumen harus KTP atau SIM',
                'pelanggan_data_file.file' => 'File yang diupload tidak valid',
                'pelanggan_data_file.mimes' => 'File harus memiliki format .jpg, .png, atau .jpeg',
                'pelanggan_data_file.max' => 'Ukuran file maksimal 2MB',
            ]);

            if ($validator->fails()) {
                $response = [
                    'success' => false,
                    'message' => 'Gagal mengupdate data dokumen pelanggan!',
                    'data' => null,
                    'errors' => [$validator->errors()]
                ];

                return response()->json($response, 422);
            }

            $oldPelangganId = $pelangganData->pelanggan_data_pelanggan_id;

            // Update file jika ada file baru
            if ($request->hasFile('pelanggan_data_file')) {
                // Hapus file lama
                Storage::disk('public')->delete($pelangganData->pelanggan_data_file);
                
                // Upload file baru
                $file = $request->file('pelanggan_data_file');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('pelanggan_data', $filename, 'public');
                
                $pelangganData->pelanggan_data_file = $path;
            }

            if ($request->has('pelanggan_data_pelanggan_id')) {
                $pelangganData->pelanggan_data_pelanggan_id = $request->pelanggan_data_pelanggan_id;
            }

            if ($request->has('pelanggan_data_jenis')) {
                $pelangganData->pelanggan_data_jenis = $request->pelanggan_data_jenis;
            }

            $pelangganData->save();

            // Update cache all pelanggan data
            Cache::put('pelanggan_data', PelangganData::with('pelanggan')->get(), 3600);

            // Update cache pelanggan data by ID
            $cacheKey = 'pelanggan_data_' . $id;
            Cache::put($cacheKey, PelangganData::with('pelanggan')->find($id), 3600);

            // Update cache pelanggan terkait
            Cache::forget('pelanggan_' . $oldPelangganId);
            if ($request->has('pelanggan_data_pelanggan_id')) {
                Cache::forget('pelanggan_' . $request->pelanggan_data_pelanggan_id);
            }

            $response = [
                'success' => true,
                'message' => 'Data dokumen pelanggan berhasil diupdate',
                'data' => $pelangganData
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
     * DELETE /api/pelanggan-data/{id}
     */
    public function destroy($id)
    {
        try {
            $pelangganData = PelangganData::find($id);

            if (!$pelangganData) {
                $response = [
                    'success' => false,
                    'message' => 'Data dokumen pelanggan tidak ditemukan',
                    'data' => null
                ];

                return response()->json($response, 404);
            }

            $pelangganId = $pelangganData->pelanggan_data_pelanggan_id;

            // Hapus file
            Storage::disk('public')->delete($pelangganData->pelanggan_data_file);
            
            $pelangganData->delete();

            // Update cache all pelanggan data
            Cache::put('pelanggan_data', PelangganData::with('pelanggan')->get(), 3600);

            // Hapus cache pelanggan data by ID
            $cacheKey = 'pelanggan_data_' . $id;
            Cache::forget($cacheKey);

            // Update cache pelanggan terkait
            Cache::forget('pelanggan_' . $pelangganId);

            $response = [
                'success' => true,
                'message' => 'Data dokumen pelanggan berhasil dihapus',
                'data' => $pelangganData
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
