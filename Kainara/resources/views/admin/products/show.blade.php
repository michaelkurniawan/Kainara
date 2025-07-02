<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Product Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                        {{ $product->name }}
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Product Image --}}
                        <div>
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-auto object-cover rounded-lg shadow-md">
                        </div>

                        {{-- Product Information --}}
                        <div>
                            <p class="mb-2"><strong>Category:</strong> {{ $product->category->name ?? 'N/A' }}</p>
                            <p class="mb-2"><strong>Origin:</strong> {{ $product->origin }}</p>
                            <p class="mb-2"><strong>Price:</strong> IDR {{ number_format($product->price, 0, ',', '.') }}</p>
                            <p class="mb-2"><strong>Material:</strong> {{ $product->material ?? 'N/A' }}</p>
                            <p class="mb-4"><strong>Description:</strong> {{ $product->description }}</p>

                            {{-- Variants Section --}}
                            <h4 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-2 mt-4 border-t pt-4">Variants:</h4>
                            @if ($product->variants->isNotEmpty())
                                @php
                                    $uniqueColors = $product->variants->pluck('color')->unique()->sort();
                                @endphp

                                <div x-data="{ selectedColor: '' }">
                                    <div class="flex flex-wrap gap-5 mb-4">
                                        <button @click="selectedColor = ''"
                                                :class="selectedColor === '' ? 'bg-[#B39C59] text-white' : 'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300'"
                                                class="px-3 py-1 rounded-md text-sm font-semibold hover:bg-[#AD9D6D] transition ease-in-out duration-150">
                                            {{ __('All Colors') }}
                                        </button>
                                        @foreach ($uniqueColors as $color)
                                            <button @click="selectedColor = '{{ $color }}'"
                                                    :class="selectedColor === '{{ $color }}' ? 'bg-[#B39C59] text-white' : 'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300'"
                                                    class="ms-1 px-3 py-1 rounded-md text-sm font-semibold hover:bg-[#AD9D6D] transition ease-in-out duration-150">
                                                {{ $color }}
                                            </button>
                                        @endforeach
                                    </div>

                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                            <thead class="bg-gray-50 dark:bg-gray-700">
                                                <tr>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                        Size
                                                    </th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider" x-show="selectedColor === ''">
                                                        Color
                                                    </th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                        Stock
                                                    </th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                        Price (Variant)
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                                @foreach ($product->variants->sortBy('size') as $variant)
                                                    <template x-if="selectedColor === '' || selectedColor === '{{ $variant->color }}'">
                                                        <tr>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                                {{ $variant->size }}
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100" x-show="selectedColor === ''">
                                                                {{ $variant->color }}
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                                {{ $variant->stock }}
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                                Rp{{ number_format($variant->price ?? $product->price, 0, ',', '.') }}
                                                            </td>
                                                        </tr>
                                                    </template>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @else
                                <p class="text-sm text-gray-600 dark:text-gray-400">No variants available for this product.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>