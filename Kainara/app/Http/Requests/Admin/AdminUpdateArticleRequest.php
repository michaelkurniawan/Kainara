<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AdminUpdateArticleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $articleId = $this->route('article')->id;

        return [
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'], // Thumbnail opsional untuk update
            // 'admin_id' => ['required', 'exists:users,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul artikel wajib diisi.',
            'title.string' => 'Judul artikel harus berupa teks.',
            'title.max' => 'Judul artikel tidak boleh lebih dari :max karakter.',
            'content.required' => 'Konten artikel wajib diisi.',
            'content.string' => 'Konten artikel harus berupa teks.',
            'thumbnail.image' => 'File thumbnail harus berupa gambar.',
            'thumbnail.mimes' => 'Format thumbnail yang diperbolehkan: jpeg, png, jpg, gif, svg.',
            'thumbnail.max' => 'Ukuran thumbnail tidak boleh lebih dari :max kilobyte.',
            'admin_id.required' => 'ID Admin wajib diisi.',
            'admin_id.exists' => 'Admin yang dipilih tidak valid.',
        ];
    }
}
