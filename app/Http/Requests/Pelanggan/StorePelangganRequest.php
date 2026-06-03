<?php

namespace App\Http\Requests\Pelanggan;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StorePelangganRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pelanggan_nama'   => ['required', 'string', 'max:255'],
            'pelanggan_alamat' => ['required', 'string'],
            'pelanggan_notelp' => ['required', 'string', 'max:20'],
            'pelanggan_email'  => ['required', 'email', 'unique:pelanggan,pelanggan_email', 'max:255'],
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
