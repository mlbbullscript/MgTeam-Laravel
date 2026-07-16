<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreScheduleRequest extends FormRequest
{
    public function authorize(): bool { return auth()->user()?->isSuperAdmin() ?? false; }

    public function rules(): array
    {
        return [
            'task_name'     => ['required', 'string', 'max:100'],
            'description'   => ['nullable', 'string', 'max:1000'],
            'poin_kerja'    => ['required', 'numeric', 'min:0'],
            'assigned_to'   => ['required', 'exists:users,id'],
            'hari'          => ['required', 'string', 'in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu'],
        ];
    }

    public function messages(): array
    {
        return [
            'task_name.required'     => 'Nama tugas wajib diisi.',
            'poin_kerja.required'    => 'Poin kerja wajib diisi.',
            'poin_kerja.min'         => 'Poin kerja tidak boleh negatif.',
            'assigned_to.required'   => 'Pilih rekan yang ditugaskan.',
            'assigned_to.exists'     => 'Rekan tidak ditemukan.',
            'hari.required'          => 'Hari tugas wajib diisi.',
            'hari.in'                => 'Format hari tidak valid.',
        ];
    }
}
