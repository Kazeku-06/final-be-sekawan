<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileUploadService
{
    /**
     * Upload file identitas pelanggan ke storage/app/public/identitas.
     * Return path relatif yang disimpan ke database.
     */
    public function uploadIdentitas(UploadedFile $file): string
    {
        // Simpan ke storage/app/public/identitas
        $path = $file->store('identitas', 'public');

        return $path;
    }

    /**
     * Hapus file lama jika ada.
     */
    public function deleteFile(string $path): void
    {
        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
