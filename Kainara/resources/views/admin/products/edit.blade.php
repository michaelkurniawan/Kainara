@extends('admin.layouts.app')

@section('title', 'Edit Produk')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6 mx-auto">
    <h2 class="text-xl font-semibold text-gray-800 mb-4">Edit Produk: {{ $product->name }}</h2>

    <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Nama Produk:</label>
            <input type="text" name="name" id="name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500 @error('name') border-red-500 @enderror" value="{{ old('name', $product->name) }}" placeholder="Masukkan nama produk">
            @error('name')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-3">
            <label for="category_id" class="block text-gray-700 text-sm font-bold mb-2">Kategori:</label>
            <select name="category_id" id="category_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500 @error('category_id') border-red-500 @enderror">
                <option value="">Pilih Kategori</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                @endforeach
            </select>
            @error('category_id')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-3">
            <label for="price" class="block text-gray-700 text-sm font-bold mb-2">Harga:</label>
            <input type="number" name="price" id="price" step="0.01" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500 @error('price') border-red-500 @enderror" value="{{ old('price', $product->price) }}" placeholder="Masukkan harga produk">
            @error('price')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-3">
            <label for="origin" class="block text-gray-700 text-sm font-bold mb-2">Asal Produk:</label>
            <input type="text" name="origin" id="origin" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500 @error('origin') border-red-500 @enderror" value="{{ old('origin', $product->origin) }}" placeholder="Contoh: Indonesia, China">
            @error('origin')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="description" class="block text-gray-700 text-sm font-bold mb-2">Deskripsi (Opsional):</label>
            <textarea name="description" id="description" rows="5" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500 @error('description') border-red-500 @enderror" placeholder="Masukkan deskripsi produk">{{ old('description', $product->description) }}
            </textarea>
            @error('description')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-between">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-md focus:outline-none focus:shadow-outline transition duration-200">
                <i class="fas fa-sync-alt mr-2"></i> Perbarui Produk
            </button>
            <a href="{{ route('admin.products.index') }}" class="inline-block align-baseline font-bold text-sm text-gray-600 hover:text-gray-800">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection


