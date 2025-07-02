<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Create New Article') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <form method="POST" action="{{ route('admin.articles.store') }}" enctype="multipart/form-data">
                        @csrf

                        {{-- Thumbnail --}}
                        <div class="flex flex-col items-center justify-center mb-6">
                            <label for="thumbnail" class="cursor-pointer relative group">
                                <img id="thumbnail_preview"
                                    src="{{ asset('/asset/default_thumbnail.png') }}"
                                    alt="Article Thumbnail"
                                    {{-- Ukuran awal tetap fixed untuk default image --}}
                                    class="w-96 h-96 object-cover border-4 border-gray-300 transition-colors duration-200"
                                >
                                <input type="file" id="thumbnail" name="thumbnail" accept="image/*" class="hidden" onchange="previewThumbnail(event)">

                                <div id="thumbnail_overlay" class="absolute inset-0 w-96 h-96 flex items-center justify-center bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                    <img src="{{ asset('/asset/edit-profile-pic.png') }}" alt="Edit Icon" class="w-8 h-8 text-white">
                                </div>
                            </label>
                            <x-input-error :messages="$errors->get('thumbnail')" class="mt-2" />
                        </div>

                        {{-- Title --}}
                        <div class="mb-4">
                            <x-input-label for="title" :value="__('Article Title')" />
                            <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title')" required autofocus />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        {{-- Content (Kembali ke textarea biasa) --}}
                        <div class="mb-4">
                            <x-input-label for="content" :value="__('Content')" />
                            <textarea id="content" name="content" rows="10" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>{{ old('content') }}</textarea>
                            <x-input-error :messages="$errors->get('content')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ms-4">
                                {{ __('Create Article') }}
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

                output.src = reader.result;

                output.classList.remove('w-96', 'h-96'); // Hapus ukuran fixed
                output.classList.add('w-full', 'h-auto', 'block'); // Tambah ukuran adaptif

                overlay.classList.remove('w-96', 'h-96');
                overlay.classList.add('w-full', 'h-full'); // Overlay menyesuaikan ukuran label parent
            }
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>

</x-app-layout>