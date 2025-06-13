@extends('admin.layouts.app')

@section('title', 'Edit Artikel')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6 max-w-2xl mx-auto">
    <h2 class="text-xl font-semibold text-gray-800 mb-6">Edit Artikel: {{ $article->title }}</h2>

    {{-- Penting: Tambahkan enctype="multipart/form-data" untuk unggahan file --}}
    <form action="{{ route('admin.articles.update', $article->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT') {{-- Penting untuk metode PUT/PATCH --}}

        <div class="mb-4">
            <label for="title" class="block text-gray-700 text-sm font-bold mb-2">Judul Artikel:</label>
            <input type="text" name="title" id="title" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500 @error('title') border-red-500 @enderror" value="{{ old('title', $article->title) }}" placeholder="Masukkan judul artikel">
            @error('title')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="slug" class="block text-gray-700 text-sm font-bold mb-2">Slug Artikel:</label>
            <input type="text" name="slug" id="slug" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500 @error('slug') border-red-500 @enderror" value="{{ old('slug', $article->slug) }}" placeholder="Contoh: judul-artikel-anda">
            <p class="text-gray-500 text-xs mt-1">Slug digunakan untuk URL yang mudah dibaca.</p>
            @error('slug')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="thumbnail" class="block text-gray-700 text-sm font-bold mb-2">Gambar Thumbnail:</label>
            {{-- Tampilkan thumbnail yang ada jika ada --}}
            @if($article->thumbnail)
                <div class="mb-2">
                    <img src="{{ asset('storage/' . $article->thumbnail) }}" alt="Current Article Thumbnail" class="w-32 h-32 object-cover rounded shadow-md">
                    <p class="text-gray-500 text-xs mt-1">Thumbnail saat ini: {{ $article->thumbnail }}</p>
                </div>
            @endif
            <input type="file" name="thumbnail" id="thumbnail" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('thumbnail') border-red-500 @enderror">
            <p class="text-gray-500 text-xs mt-1">Biarkan kosong jika tidak ingin mengubah thumbnail.</p>
            @error('thumbnail')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label for="content" class="block text-gray-700 text-sm font-bold mb-2">Konten Artikel:</label>
            <textarea name="content" id="content" rows="10" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500 @error('content') border-red-500 @enderror" placeholder="Tulis konten artikel Anda di sini">{{ old('content', $article->content) }}</textarea>
            @error('content')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-between">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-md focus:outline-none focus:shadow-outline transition duration-200">
                <i class="fas fa-sync-alt mr-2"></i> Perbarui Artikel
            </button>
            <a href="{{ route('admin.articles.index') }}" class="inline-block align-baseline font-bold text-sm text-gray-600 hover:text-gray-800">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection