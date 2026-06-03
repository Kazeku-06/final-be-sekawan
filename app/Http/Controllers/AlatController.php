<?php

namespace App\Http\Controllers;

use App\Http\Requests\Alat\StoreAlatRequest;
use App\Http\Requests\Alat\UpdateAlatRequest;
use App\Models\Alat;
use Illuminate\Http\JsonResponse;

class AlatController extends Controller
{
    /**
     * GET /api/alat
     * Tampilkan semua alat dengan eager loading kategori.
     */
    public function index(): JsonResponse
    {
        try {
            $data = Alat::with('kategori')->get();

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
     * POST /api/alat
     * Simpan alat baru.
     */
    public function store(StoreAlatRequest $request): JsonResponse
    {
        try {
            $alat = Alat::create($request->validated());
            $alat->load('kategori');

            return response()->json([
                'success' => true,
                'message' => 'Alat berhasil ditambahkan.',
                'data'    => $alat,
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
     * GET /api/alat/{id}
     * Tampilkan detail satu alat.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $alat = Alat::with('kategori')->find($id);

            if (!$alat) {
                return response()->json([
                    'success' => false,
                    'message' => 'Alat tidak ditemukan.',
                    'data'    => null,
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Successfully get data',
                'data'    => $alat,
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
     * PATCH /api/alat/{id}
     * Update data alat.
     */
    public function update(UpdateAlatRequest $request, int $id): JsonResponse
    {
        try {
            $alat = Alat::find($id);

            if (!$alat) {
                return response()->json([
                    'success' => false,
                    'message' => 'Alat tidak ditemukan.',
                    'data'    => null,
                ], 404);
            }

            $alat->update($request->validated());
            $alat->load('kategori');

            return response()->json([
                'success' => true,
                'message' => 'Alat berhasil diperbarui.',
                'data'    => $alat,
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
     * DELETE /api/alat/{id}
     * Hapus alat.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $alat = Alat::find($id);

            if (!$alat) {
                return response()->json([
                    'success' => false,
                    'message' => 'Alat tidak ditemukan.',
                    'data'    => null,
                ], 404);
            }

            $alat->delete();

            return response()->json([
                'success' => true,
                'message' => 'Alat berhasil dihapus.',
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
