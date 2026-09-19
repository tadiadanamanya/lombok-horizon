<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInquiryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; // Allow anyone to submit inquiries
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'nama' => ['required', 'string', 'min:2', 'max:255'],
            'phone' => ['required', 'string', 'regex:/^(\+62|62)?[\s-]?0?8[1-9][0-9]{7,9}$/'],
            'kavling_id' => ['nullable', 'integer', 'exists:kavlings,id'],
            'project_id' => ['nullable', 'integer', 'exists:projects,id'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages()
    {
        return [
            'nama.required' => 'Nama harus diisi.',
            'nama.string' => 'Nama harus berupa teks.',
            'nama.min' => 'Nama harus memiliki minimal 2 karakter.',
            'phone.required' => 'Nomor telepon harus diisi.',
            'phone.regex' => 'Nomor telepon tidak valid. Gunakan format nomor Indonesia yang benar.',
            'kavling_id.integer' => 'ID kavling harus berupa angka.',
            'kavling_id.exists' => 'Kavling yang dipilih tidak ditemukan.',
            'project_id.integer' => 'ID proyek harus berupa angka.',
            'project_id.exists' => 'Proyek yang dipilih tidak ditemukan.',
        ];
    }
}
