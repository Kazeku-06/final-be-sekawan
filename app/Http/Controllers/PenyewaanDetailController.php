<?php

namespace App\Http\Controllers;

use App\Models\PenyewaanDetail;
use Illuminate\Http\JsonResponse;

class PenyewaanDetailController extends Controller
{
    /**
     * GET /api/penyewaan-detail
     * Tampilkan semua detail penyewaan.
     */
    public function index(): JsonResponse
    {
        try {
            $data = PenyewaanDetail::with(['penyewaan.pelanggan', 'alat.kategori'])->get();

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
     * GET /api/penyewaan-detail/{id}
     * Tampilkan satu detail penyewaan.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $detail = PenyewaanDetail::with(['penyewaan.pelanggan', 'alat.kategori'])->find($id);

            if (!$detail) {
                return response()->json([
                    'success' => false,
                    'message' => 'Detail penyewaan tidak ditemukan.',
                    'data'    => null,
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Successfully get data',
                'data'    => $detail,
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
