<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule; // Import Rule

class UpdateProductRequest extends FormRequest
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
     * @return array<string, 
     */
    public function rules(): array
    {
        $productId = $this->route('product')->id;

        return [
            'category_id' => ['required', 'exists:categories,id'],
            'vendor_id' => ['nullable', 'exists:vendors,id'], // Make it nullable if 'Shirt' category handles it automatically
            'name' => ['required', 'string', 'max:255'],
            'origin' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'material' => ['nullable', 'string', 'max:255'],
            'variants' => ['array'], 
            'variants.*.id' => ['nullable', 'integer', 'exists:product_variants,id'],
            'variants.*.color' => ['required_with:variants', 'string', 'max:255'],
            'variants.*.size' => ['required_with:variants', 'string', 'max:255'],
            'variants.*.stock' => ['required_with:variants', 'integer', 'min:0'],
            'variants.*.price' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'category_id.required' => 'Kategori produk wajib diisi.',
            'category_id.exists' => 'Kategori yang dipilih tidak valid.',
            'name.required' => 'Nama produk wajib diisi.',
            'name.max' => 'Nama produk tidak boleh lebih dari :max karakter.',
            'origin.required' => 'Asal produk wajib diisi.',
            'description.required' => 'Deskripsi produk wajib diisi.',
            'price.required' => 'Harga produk wajib diisi.',
            'price.numeric' => 'Harga produk harus berupa angka.',
            'price.min' => 'Harga produk tidak boleh negatif.',
            'image.image' => 'File harus berupa gambar.',
            'image.mimes' => 'Format gambar yang diperbolehkan: jpeg, png, jpg, gif, svg.',
            'image.max' => 'Ukuran gambar tidak boleh lebih dari :max kilobyte.',
            'variants.array' => 'Format varian produk tidak valid.',
            'variants.*.id.exists' => 'ID varian tidak valid.',
            'variants.*.color.required_with' => 'Warna varian wajib diisi.',
            'variants.*.size.required_with' => 'Ukuran varian wajib diisi.',
            'variants.*.stock.required_with' => 'Stok varian wajib diisi.',
            'variants.*.stock.integer' => 'Stok varian harus berupa angka bulat.',
            'variants.*.stock.min' => 'Stok varian tidak boleh negatif.',
            'variants.*.price.numeric' => 'Harga varian harus berupa angka.',
            'variants.*.price.min' => 'Harga varian tidak boleh negatif.',
        ];
    }
}