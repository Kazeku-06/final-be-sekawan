<?php

namespace App\Http\Requests\PelangganData;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StorePelangganDataRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pelanggan_data_pelanggan_id' => ['required', 'integer', 'exists:pelanggan,pelanggan_id'],
            'pelanggan_data_jenis'        => ['required', 'in:KTP,SIM'],
            'pelanggan_data_file'         => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
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
