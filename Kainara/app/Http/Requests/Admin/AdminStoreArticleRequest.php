<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AdminStoreArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Pastikan pengguna sudah login
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'thumbnail' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            // 'admin_id' dihapus dari validasi karena akan diisi otomatis
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
            'thumbnail.required' => 'Thumbnail artikel wajib diunggah.',
            'thumbnail.image' => 'File thumbnail harus berupa gambar.',
            'thumbnail.mimes' => 'Format thumbnail yang diperbolehkan: jpeg, png, jpg, gif, svg.',
            'thumbnail.max' => 'Ukuran thumbnail tidak boleh lebih dari :max kilobyte.',
        ];
    }
}