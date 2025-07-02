<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Article') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <form method="POST" action="{{ route('admin.articles.update', $article) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        {{-- Thumbnail --}}
                        <div class="flex flex-col items-center justify-center mb-6">
                            {{-- Tambahkan ID pada label untuk manipulasi JS --}}
                            <label for="thumbnail" id="thumbnail_label" class="cursor-pointer relative group w-96 h-96 overflow-hidden">
                                <img id="thumbnail_preview"
                                    src="{{ $article->thumbnail_url ?? asset('/asset/default_thumbnail.png') }}"
                                    alt="Article Thumbnail"
                                    class="w-full h-full object-cover border-4 border-gray-300 transition-colors duration-200"
                                >
                                <input type="file" id="thumbnail" name="thumbnail" accept="image/*" class="hidden" onchange="previewThumbnail(event)">

                                <div id="thumbnail_overlay" class="absolute inset-0 w-full h-full flex items-center justify-center bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                    <img src="{{ asset('/asset/edit-profile-pic.png') }}" alt="Edit Icon" class="w-8 h-8 text-white">
                                </div>
                            </label>
                            <x-input-error :messages="$errors->get('thumbnail')" class="mt-2" />
                        </div>

                        {{-- Title --}}
                        <div class="mb-4">
                            <x-input-label for="title" :value="__('Article Title')" />
                            <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title', $article->title)" required autofocus />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        {{-- Content --}}
                        <div class="mb-4">
                            <x-input-label for="content" :value="__('Content')" />
                            <textarea id="content" name="content" rows="10" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>{{ old('content', $article->content) }}</textarea>
                            <x-input-error :messages="$errors->get('content')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ms-4">
                                {{ __('Update Article') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    {{-- Script untuk Preview Thumbnail --}}
    <script>
        function previewThumbnail(event) {
            const reader = new FileReader();
            reader.onload = function() {
                const output = document.getElementById('thumbnail_preview');
                const overlay = document.getElementById('thumbnail_overlay');
                const label = document.getElementById('thumbnail_label'); // Dapatkan elemen label

                output.src = reader.result;

                // Ketika gambar baru dimuat, biarkan img menyesuaikan ukurannya secara alami
                // dan hilangkan object-cover agar gambar tidak terpotong
                output.classList.remove('w-full', 'h-full', 'object-cover'); // Hapus ukuran fixed dari img
                output.classList.add('max-w-full', 'h-auto', 'block'); // Biarkan img mengatur ukurannya sendiri

                // Ini penting: Hapus ukuran tetap dari label agar bisa menyesuaikan ukuran img
                label.classList.remove('w-96', 'h-96', 'overflow-hidden');
                label.classList.add('w-auto', 'h-auto'); // Biarkan label mengikuti ukuran gambar

                // Overlay sudah w-full h-full, jadi akan mengisi label yang sudah adaptif
                // Tidak perlu mengubah kelas di overlay secara spesifik lagi
            };

            // Tambahkan event listener saat gambar sudah benar-benar dimuat
            // Ini penting untuk mendapatkan dimensi gambar yang benar
            reader.onloadend = function() {
                const output = document.getElementById('thumbnail_preview');
                const overlay = document.getElementById('thumbnail_overlay');
                const label = document.getElementById('thumbnail_label');

                // Set ukuran label berdasarkan gambar yang baru dimuat
                // Ini akan memastikan overlay mengikuti
                label.style.width = output.offsetWidth + 'px';
                label.style.height = output.offsetHeight + 'px';

                // Opsional: jika Anda ingin ada batasan ukuran maksimum, bisa ditambahkan di sini
                // Contoh:
                // if (output.offsetWidth > 384) { // 384px = w-96
                //     label.style.width = '384px';
                //     label.style.height = 'auto'; // Agar aspect ratio tetap
                //     output.classList.add('object-contain'); // Atau object-cover jika ingin mengisi
                // }
            };

            reader.readAsDataURL(event.target.files[0]);
        }

        // Jalankan script ini saat DOM sudah dimuat untuk inisialisasi awal
        document.addEventListener('DOMContentLoaded', function() {
            const output = document.getElementById('thumbnail_preview');
            const label = document.getElementById('thumbnail_label');

            // Pastikan label memiliki ukuran yang benar jika ada gambar lama dari DB
            // Ini akan memicu penyesuaian ukuran label saat gambar lama dimuat
            if (output.complete) {
                // Gambar sudah dimuat (misal dari cache browser)
                label.style.width = output.offsetWidth + 'px';
                label.style.height = output.offsetHeight + 'px';
            } else {
                // Gambar sedang dimuat, tambahkan event listener
                output.onload = function() {
                    label.style.width = output.offsetWidth + 'px';
                    label.style.height = output.offsetHeight + 'px';
                };
            }

            // Jika awalnya tidak ada thumbnail dan pakai default_thumbnail.png,
            // pastikan ukurannya tetap w-96 h-96 seperti yang sudah diatur di HTML awal
        });
    </script>
</x-app-layout>