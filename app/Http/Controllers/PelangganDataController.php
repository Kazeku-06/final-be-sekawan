<?php

namespace App\Http\Controllers;

use App\Http\Requests\PelangganData\StorePelangganDataRequest;
use App\Models\PelangganData;
use App\Services\FileUploadService;
use Illuminate\Http\JsonResponse;

class PelangganDataController extends Controller
{
    public function __construct(protected FileUploadService $fileUploadService) {}

    /**
     * GET /api/pelanggan-data
     * Tampilkan semua data identitas pelanggan.
     */
    public function index(): JsonResponse
    {
        try {
            $data = PelangganData::with('pelanggan')->get();

            return response()->json([
                'success' => true,
                'message' => 'Successfully get data',
                'data'    => $data,
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

    /**
     * POST /api/pelanggan-data
     * Upload identitas pelanggan (KTP / SIM).
     */
    public function store(StorePelangganDataRequest $request): JsonResponse
    {
        try {
            // Upload file ke storage/app/public/identitas
            $filePath = $this->fileUploadService->uploadIdentitas($request->file('pelanggan_data_file'));

            $pelangganData = PelangganData::create([
                'pelanggan_data_pelanggan_id' => $request->pelanggan_data_pelanggan_id,
                'pelanggan_data_jenis'        => $request->pelanggan_data_jenis,
                'pelanggan_data_file'         => $filePath,
            ]);

            $pelangganData->load('pelanggan');

            return response()->json([
                'success' => true,
                'message' => 'Identitas pelanggan berhasil diunggah.',
                'data'    => $pelangganData,
            ], 201);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'There error in Internal Server',
                'data'    => null,
                'errors'  => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET /api/pelanggan-data/{id}
     * Tampilkan satu data identitas.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $pelangganData = PelangganData::with('pelanggan')->find($id);

            if (!$pelangganData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data identitas tidak ditemukan.',
                    'data'    => null,
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Successfully get data',
                'data'    => $pelangganData,
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

    /**
     * DELETE /api/pelanggan-data/{id}
     * Hapus data identitas pelanggan beserta filenya.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $pelangganData = PelangganData::find($id);

            if (!$pelangganData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data identitas tidak ditemukan.',
                    'data'    => null,
                ], 404);
            }

            // Hapus file dari storage
            $this->fileUploadService->deleteFile($pelangganData->pelanggan_data_file);

            $pelangganData->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data identitas berhasil dihapus.',
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
}
