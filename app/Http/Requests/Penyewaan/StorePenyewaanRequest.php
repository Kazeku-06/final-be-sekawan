<?php

namespace App\Http\Requests\Penyewaan;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StorePenyewaanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'penyewaan_pelanggan_id'          => ['required', 'integer', 'exists:pelanggan,pelanggan_id'],
            'penyewaan_tglsewa'               => ['required', 'date'],
            'penyewaan_tglkembali'            => ['required', 'date', 'after:penyewaan_tglsewa'],
            'penyewaan_sttspembayaran'        => ['sometimes', 'in:Lunas,Belum Dibayar,DP'],

            // Array detail alat yang disewa
            'details'                         => ['required', 'array', 'min:1'],
            'details.*.alat_id'               => ['required', 'integer', 'exists:alat,alat_id'],
            'details.*.jumlah'                => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'penyewaan_tglkembali.after' => 'Tanggal kembali harus setelah tanggal sewa.',
            'details.required'           => 'Detail alat tidak boleh kosong.',
            'details.*.alat_id.exists'   => 'Alat tidak ditemukan.',
            'details.*.jumlah.min'       => 'Jumlah minimal 1.',
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
