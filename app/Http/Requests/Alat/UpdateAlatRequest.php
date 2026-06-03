<?php

namespace App\Http\Requests\Alat;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateAlatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'alat_kategori_id'  => ['sometimes', 'integer', 'exists:kategori,kategori_id'],
            'alat_nama'         => ['sometimes', 'string', 'max:255'],
            'alat_deskripsi'    => ['sometimes', 'string'],
            'alat_hargaperhari' => ['sometimes', 'integer', 'min:0'],
            'alat_stok'         => ['sometimes', 'integer', 'min:0'],
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation Error',
            'data'    => null,
            'errors'  => $validator->errors(),
        ], 422));
    }
}
