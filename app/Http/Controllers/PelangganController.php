<?php

namespace App\Http\Controllers;

use App\Http\Requests\Pelanggan\StorePelangganRequest;
use App\Http\Requests\Pelanggan\UpdatePelangganRequest;
use App\Models\Pelanggan;
use Illuminate\Http\JsonResponse;

class PelangganController extends Controller
{
    /**
     * GET /api/pelanggan
     * Tampilkan semua pelanggan.
     */
    public function index(): JsonResponse
    {
        try {
            $data = Pelanggan::with('data')->get();

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
     * POST /api/pelanggan
     * Simpan pelanggan baru.
     */
    public function store(StorePelangganRequest $request): JsonResponse
    {
        try {
            $pelanggan = Pelanggan::create($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Pelanggan berhasil ditambahkan.',
                'data'    => $pelanggan,
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
     * GET /api/pelanggan/{id}
     * Tampilkan detail satu pelanggan beserta dokumen identitasnya.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $pelanggan = Pelanggan::with('data')->find($id);

            if (!$pelanggan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pelanggan tidak ditemukan.',
                    'data'    => null,
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Successfully get data',
                'data'    => $pelanggan,
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
     * PATCH /api/pelanggan/{id}
     * Update data pelanggan.
     */
    public function update(UpdatePelangganRequest $request, int $id): JsonResponse
    {
        try {
            $pelanggan = Pelanggan::find($id);

            if (!$pelanggan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pelanggan tidak ditemukan.',
                    'data'    => null,
                ], 404);
            }

            $pelanggan->update($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Pelanggan berhasil diperbarui.',
                'data'    => $pelanggan,
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
     * DELETE /api/pelanggan/{id}
     * Hapus pelanggan (cascade ke pelanggan_data dan penyewaan).
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $pelanggan = Pelanggan::find($id);

            if (!$pelanggan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pelanggan tidak ditemukan.',
                    'data'    => null,
                ], 404);
            }

            $pelanggan->delete();

            return response()->json([
                'success' => true,
                'message' => 'Pelanggan berhasil dihapus.',
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
