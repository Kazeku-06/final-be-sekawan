<?php

namespace App\Http\Requests\Pelanggan;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdatePelangganRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $pelangganId = $this->route('pelanggan');

        return [
            'pelanggan_nama'   => ['sometimes', 'string', 'max:255'],
            'pelanggan_alamat' => ['sometimes', 'string'],
            'pelanggan_notelp' => ['sometimes', 'string', 'max:20'],
            'pelanggan_email'  => [
                'sometimes', 'email', 'max:255',
                "unique:pelanggan,pelanggan_email,{$pelangganId},pelanggan_id",
            ],
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
