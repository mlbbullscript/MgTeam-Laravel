<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;

/**
 * Validasi saat SuperAdmin menambah atau mengupdate saham rekan.
 * Aturan kritis: total coin_saham semua user tidak boleh melebihi 100.
 */
class UpdateSahamRequest extends FormRequest
{
    public function authorize(): bool { return auth()->user()?->isSuperAdmin() ?? false; }

    public function rules(): array
    {
        $rekan = $this->route('rekan');
        $userId = is_object($rekan) ? $rekan->id : $rekan;

        return [
            'username'   => ['required', 'string', 'max:50', 'unique:users,username,' . ($userId ?? 'NULL')],
            'password'   => [$userId ? 'nullable' : 'required', 'string', 'min:8'],
            'coin_saham' => ['required', 'numeric', 'min:0', 'max:100'],
            'is_active'  => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'username.required'   => 'Username wajib diisi.',
            'username.unique'     => 'Username sudah digunakan oleh rekan lain.',
            'username.max'        => 'Username maksimal 50 karakter.',
            'password.required'   => 'Password wajib diisi saat membuat rekan baru.',
            'password.min'        => 'Password minimal 8 karakter.',
            'coin_saham.required' => 'Koin saham wajib diisi.',
            'coin_saham.min'      => 'Koin saham tidak boleh negatif.',
            'coin_saham.max'      => 'Koin saham tidak boleh melebihi 100.',
        ];
    }

    /**
     * Validasi tambahan: total koin saham semua user tidak boleh melebihi 100.
     * Dipanggil setelah rules() lulus.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $rekan = $this->route('rekan');
            $userId = is_object($rekan) ? $rekan->id : $rekan;
            $coinBaru   = (float) $this->input('coin_saham', 0);

            // Total koin saham semua user KECUALI user yang sedang diedit
            $totalLain = User::when($userId, fn($q) => $q->where('id', '!=', $userId))
                ->sum('coin_saham');

            if (($totalLain + $coinBaru) > 100) {
                $sisa = 100 - $totalLain;
                $validator->errors()->add(
                    'coin_saham',
                    "Total koin saham akan melebihi 100. Sisa yang tersedia: {$sisa} koin."
                );
            }
        });
    }
}
