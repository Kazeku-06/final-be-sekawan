<?php

namespace App\Http\Requests\Penyewaan;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdatePenyewaanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'penyewaan_sttspembayaran' => ['sometimes', 'in:Lunas,Belum Dibayar,DP'],
            'penyewaan_sttskembali'    => ['sometimes', 'in:Sudah Kembali,Belum Kembali'],
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
