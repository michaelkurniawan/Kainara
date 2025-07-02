<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Article Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-4">
                        {{ $article->title }}
                    </h3>

                    <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                        By {{ $article->admin->first_name . ' ' . $article->admin->last_name ?? 'N/A' }} on {{ $article->created_at->format('d M Y H:i') }}
                    </div>

                    <div class="mb-6">
                        <img src="{{ $article->thumbnail_url ?? asset('/asset/default_thumbnail.png') }}" alt="{{ $article->title }}" class="w-full h-auto object-cover rounded-lg shadow-md max-w-xl mx-auto">
                    </div>

                    <div class="prose dark:prose-invert max-w-none">
                        {{-- Menggunakan {!! !!} untuk merender HTML dari konten jika Anda menggunakan WYSIWYG --}}
                        {!! nl2br(e($article->content)) !!} {{-- nl2br dan e() untuk keamanan dasar jika tidak pakai WYSIWYG --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>