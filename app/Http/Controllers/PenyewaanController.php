<?php

namespace App\Http\Controllers;

use App\Http\Requests\Penyewaan\StorePenyewaanRequest;
use App\Http\Requests\Penyewaan\UpdatePenyewaanRequest;
use App\Models\Penyewaan;
use App\Services\PenyewaanService;
use Illuminate\Http\JsonResponse;

class PenyewaanController extends Controller
{
    public function __construct(protected PenyewaanService $penyewaanService) {}

    /**
     * GET /api/penyewaan
     * Tampilkan semua transaksi penyewaan.
     */
    public function index(): JsonResponse
    {
        try {
            $data = Penyewaan::with(['pelanggan', 'detail.alat.kategori'])->get();

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
     * POST /api/penyewaan
     * Buat transaksi penyewaan baru dengan DB::transaction().
     */
    public function store(StorePenyewaanRequest $request): JsonResponse
    {
        try {
            $penyewaan = $this->penyewaanService->createPenyewaan($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Transaksi penyewaan berhasil dibuat.',
                'data'    => $penyewaan,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data'    => null,
                'errors'  => null,
            ], 422);
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
     * GET /api/penyewaan/{id}
     * Tampilkan detail satu transaksi penyewaan.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $penyewaan = Penyewaan::with(['pelanggan', 'detail.alat.kategori'])->find($id);

            if (!$penyewaan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Penyewaan tidak ditemukan.',
                    'data'    => null,
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Successfully get data',
                'data'    => $penyewaan,
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
     * PATCH /api/penyewaan/{id}
     * Update status pembayaran atau status kembali.
     */
    public function update(UpdatePenyewaanRequest $request, int $id): JsonResponse
    {
        try {
            $penyewaan = Penyewaan::find($id);

            if (!$penyewaan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Penyewaan tidak ditemukan.',
                    'data'    => null,
                ], 404);
            }

            $penyewaan->update($request->validated());
            $penyewaan->load(['pelanggan', 'detail.alat.kategori']);

            return response()->json([
                'success' => true,
                'message' => 'Status penyewaan berhasil diperbarui.',
                'data'    => $penyewaan,
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
     * DELETE /api/penyewaan/{id}
     * Hapus transaksi penyewaan.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $penyewaan = Penyewaan::find($id);

            if (!$penyewaan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Penyewaan tidak ditemukan.',
                    'data'    => null,
                ], 404);
            }

            $penyewaan->delete();

            return response()->json([
                'success' => true,
                'message' => 'Penyewaan berhasil dihapus.',
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
