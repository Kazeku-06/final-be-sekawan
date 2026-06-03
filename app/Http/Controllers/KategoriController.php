<?php

namespace App\Http\Controllers;

use App\Http\Requests\Kategori\StoreKategoriRequest;
use App\Http\Requests\Kategori\UpdateKategoriRequest;
use App\Models\Kategori;
use Illuminate\Http\JsonResponse;

class KategoriController extends Controller
{
    /**
     * GET /api/kategori
     * Tampilkan semua kategori beserta jumlah alatnya.
     */
    public function index(): JsonResponse
    {
        try {
            $data = Kategori::withCount('alat')->get();

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
     * POST /api/kategori
     * Simpan kategori baru.
     */
    public function store(StoreKategoriRequest $request): JsonResponse
    {
        try {
            $kategori = Kategori::create($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil ditambahkan.',
                'data'    => $kategori,
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
     * GET /api/kategori/{id}
     * Tampilkan detail satu kategori beserta alat-alatnya.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $kategori = Kategori::with('alat')->find($id);

            if (!$kategori) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kategori tidak ditemukan.',
                    'data'    => null,
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Successfully get data',
                'data'    => $kategori,
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
     * PATCH /api/kategori/{id}
     * Update data kategori.
     */
    public function update(UpdateKategoriRequest $request, int $id): JsonResponse
    {
        try {
            $kategori = Kategori::find($id);

            if (!$kategori) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kategori tidak ditemukan.',
                    'data'    => null,
                ], 404);
            }

            $kategori->update($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil diperbarui.',
                'data'    => $kategori,
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
     * DELETE /api/kategori/{id}
     * Hapus kategori (cascade delete akan menghapus alat terkait).
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $kategori = Kategori::find($id);

            if (!$kategori) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kategori tidak ditemukan.',
                    'data'    => null,
                ], 404);
            }

            $kategori->delete();

            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil dihapus.',
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
