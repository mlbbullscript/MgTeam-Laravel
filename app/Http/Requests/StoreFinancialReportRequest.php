<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFinancialReportRequest extends FormRequest
{
    public function authorize(): bool { return auth()->check(); }

    public function rules(): array
    {
        return [
            'type'        => ['required', 'in:pemasukan,pengeluaran'],
            'name'        => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
            'amount'      => ['required', 'numeric', 'min:1'],
            'report_date' => ['required', 'date'],
            'screenshot'  => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'type.required'        => 'Tipe laporan wajib dipilih.',
            'type.in'              => 'Tipe laporan harus pemasukan atau pengeluaran.',
            'name.required'        => 'Nama laporan wajib diisi.',
            'amount.required'      => 'Nominal wajib diisi.',
            'amount.min'           => 'Nominal harus lebih dari 0.',
            'report_date.required' => 'Tanggal laporan wajib diisi.',
            'screenshot.mimes'     => 'File harus berformat JPG, PNG, atau PDF.',
            'screenshot.max'       => 'Ukuran file maksimal 2MB.',
        ];
    }
}
