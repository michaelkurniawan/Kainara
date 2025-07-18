<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Category; // Pastikan model Category di-import

class StoreProductRequest extends FormRequest
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
        return [
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255', 'unique:products,name'],
            'origin' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'material' => ['nullable', 'string', 'max:255'],
            'vendor_id' => ['nullable', 'exists:vendors,id'], // Make it nullable if 'Shirt' category handles it automatically

            // Rules for variants array
            'variants' => ['required', 'array', 'min:1'],
            'variants.*.color' => ['required', 'string', 'max:255', 'min:1'],
            'variants.*.size' => ['required', 'string', 'max:255', 'not_in:'], // Diperbarui menjadi 'not_in:'
            'variants.*.stock' => ['required', 'integer', 'min:0'],
            'variants.*.price' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $variants = $this->input('variants');
            $categoryId = $this->input('category_id');

            if (!$variants || !is_array($variants)) {
                return; // Jika tidak ada varian atau bukan array, validasi 'required|array' akan menangani
            }

            // Get the selected category name for size validation
            $category = Category::find($categoryId);
            $categoryName = $category ? $category->name : null;

            // Define allowed sizes based on category
            $allowedSizes = [
                'Shirt' => ['S', 'M', 'L', 'XL', 'XXL'],
                'Fabric' => ['One Size'],
                // Tambahkan kategori lain dan ukurannya di sini
                'default' => ['S', 'M', 'L', 'XL', 'XXL', 'One Size'], // Fallback
            ];
            $currentAllowedSizes = $allowedSizes[$categoryName] ?? $allowedSizes['default'];

            $seenVariants = []; // Untuk melacak kombinasi warna-ukuran yang sudah ada

            foreach ($variants as $index => $variant) {
                $color = $variant['color'] ?? null;
                $size = $variant['size'] ?? null;

                // 1. Validasi Ukuran Berdasarkan Kategori
                // Periksa juga jika $size tidak kosong sebelum validasi in_array
                if ($size && $size !== '' && !in_array($size, $currentAllowedSizes)) {
                    $validator->errors()->add("variants.{$index}.size", "The size '{$size}' is not valid for the selected category '{$categoryName}'. Allowed sizes: " . implode(', ', $currentAllowedSizes) . ".");
                }

                // 2. Validasi Duplikat Varian (Warna dan Ukuran) dalam satu request
                if ($color && $size && $color !== '' && $size !== '') { // Pastikan keduanya tidak kosong
                    $key = strtolower($color) . '-' . strtolower($size); // Buat kunci unik (case-insensitive)
                    if (in_array($key, $seenVariants)) {
                        $validator->errors()->add("variants.{$index}.color", "Duplicate variant: A variant with color '{$color}' and size '{$size}' already exists in this list.");
                        $validator->errors()->add("variants.{$index}.size", "Duplicate variant: A variant with color '{$color}' and size '{$size}' already exists in this list.");
                    }
                    $seenVariants[] = $key;
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'variants.required' => 'At least one product variant is required.',
            'variants.array' => 'Product variants must be an array.',
            'variants.min' => 'At least one product variant is required.',
            'variants.*.color.required' => 'The color field for variant :position is required.',
            'variants.*.color.min' => 'The color field for variant :position must not be empty.',
            'variants.*.size.required' => 'The size field for variant :position is required.',
            'variants.*.size.not_in' => 'Please select a valid size for variant :position.', // Pesan baru untuk 'not_in'
            'variants.*.stock.required' => 'The stock field for variant :position is required.',
            'variants.*.stock.integer' => 'The stock for variant :position must be an integer.',
            'variants.*.stock.min' => 'The stock for variant :position must be at least 0.',
            'variants.*.price.numeric' => 'The variant price for variant :position must be a number.',
            'variants.*.price.min' => 'The variant price for variant :position must be at least 0.',
        ];
    }
}
