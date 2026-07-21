<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:80'],
            'email' => ['required', 'email:rfc', 'max:160'],
            'subject' => ['required', 'string', 'max:120'],
            'message' => ['required', 'string', 'min:20', 'max:3000'],
            'website' => ['nullable', 'string', 'max:0'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama perlu diisi.',
            'email.required' => 'Email perlu diisi.',
            'email.email' => 'Format email belum benar.',
            'subject.required' => 'Subjek perlu diisi.',
            'message.required' => 'Pesan perlu diisi.',
            'message.min' => 'Pesan minimal terdiri dari 20 karakter.',
            'website.max' => 'Pesan tidak dapat diproses.',
        ];
    }
}
