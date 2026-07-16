<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOvertimeTaskRequest extends FormRequest
{
    public function authorize(): bool { return auth()->user()?->isSuperAdmin() ?? false; }

    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
            'poin_kerja'  => ['required', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'      => 'Nama tugas lembur wajib diisi.',
            'poin_kerja.required'=> 'Poin kerja wajib diisi.',
            'poin_kerja.min'     => 'Poin kerja tidak boleh negatif.',
        ];
    }
}
