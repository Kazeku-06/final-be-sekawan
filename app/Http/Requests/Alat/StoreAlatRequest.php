<?php

namespace App\Http\Requests\Alat;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreAlatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'alat_kategori_id'  => ['required', 'integer', 'exists:kategori,kategori_id'],
            'alat_nama'         => ['required', 'string', 'max:255'],
            'alat_deskripsi'    => ['required', 'string'],
            'alat_hargaperhari' => ['required', 'integer', 'min:0'],
            'alat_stok'         => ['required', 'integer', 'min:0'],
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
