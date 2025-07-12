<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Create New Product') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
                        @csrf

                        {{-- Product Image --}}
                        <div class="flex flex-col items-center justify-center mb-6">
                            <label for="image" class="cursor-pointer relative group">
                                <img id="image_preview"
                                        src="{{ asset('/asset/default_product.png') }}"
                                        alt="Product Image"
                                        class="w-96 h-96 object-cover border-4 border-gray-300 transition-colors duration-200"
                                >
                                <input type="file" id="image" name="image" accept="image/*" class="hidden" onchange="previewImage(event)">

                                <div class="absolute inset-0 w-96 h-96 flex items-center justify-center bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                    <img src="{{ asset('/asset/edit-profile-pic.png') }}" alt="Edit Icon" class="w-8 h-8 text-white">
                                </div>
                            </label>
                            <x-input-error :messages="$errors->get('image')" class="mt-2" />
                        </div>

                        {{-- Product Details --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                {{-- Name --}}
                                <div class="mb-4">
                                    <x-input-label for="name" :value="__('Product Name')" />
                                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                </div>

                                {{-- Category --}}
                                <div class="mb-4">
                                    <x-input-label for="category_id" :value="__('Category')" />
                                    <select id="category_id" name="category_id" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                        <option value="">{{ __('Select Category') }}</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                                </div>

                                {{-- Origin --}}
                                <div class="mb-4">
                                    <x-input-label for="origin" :value="__('Origin')" />
                                    <x-text-input id="origin" class="block mt-1 w-full" type="text" name="origin" :value="old('origin')" required />
                                    <x-input-error :messages="$errors->get('origin')" class="mt-2" />
                                </div>
                            </div>

                            <div>
                                {{-- Price --}}
                                <div class="mb-4">
                                    <x-input-label for="price" :value="__('Base Price')" />
                                    <x-text-input id="price" class="block mt-1 w-full" type="number" step="0.01" name="price" :value="old('price')" required />
                                    <x-input-error :messages="$errors->get('price')" class="mt-2" />
                                </div>

                                {{-- Material --}}
                                <div class="mb-4">
                                    <x-input-label for="material" :value="__('Material (Optional)')" />
                                    <x-text-input id="material" class="block mt-1 w-full" type="text" name="material" :value="old('material')" />
                                    <x-input-error :messages="$errors->get('material')" class="mt-2" />
                                </div>

                                {{-- Description --}}
                                <div class="mb-4">
                                    <x-input-label for="description" :value="__('Description')" />
                                    <textarea id="description" name="description" rows="4" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>{{ old('description') }}</textarea>
                                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        {{-- Variants Section --}}
                        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700 variants-section">
                            <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('Product Variants') }}</h4>

                            @php
                                $oldVariants = old('variants', []);
                                $numVariants = count($oldVariants);
                                if (empty($oldVariants) && !$errors->any()) {
                                    $numVariants = 1;
                                }
                            @endphp

                            {{-- Wrapper untuk semua baris varian dan tombol add (+), menjadi group hover --}}
                            {{-- Adjusted padding-right (pr-4) on the individual variant row --}}
                            <div id="variants-wrapper" class="relative group p-4 border border-gray-200 dark:border-gray-700 rounded-md pr-12"> {{-- Added pr-12 to the main wrapper for overall right padding --}}
                                <div id="variants-container" class="pb-16"> {{-- Increased padding-bottom to create space for the button --}}
                                    @for ($i = 0; $i < $numVariants; $i++)
                                        {{-- Tambahkan mt-4 untuk pemisah visual antar varian --}}
                                        {{-- Sesuaikan padding-right agar tombol delete tidak bertabrakan --}}
                                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4 p-4 border border-gray-200 dark:border-gray-700 rounded-md relative {{ $i > 0 ? 'mt-4' : '' }} pr-4"> {{-- Changed pr-16 to pr-4 on individual variant row --}}
                                            @if ($i > 0 || $numVariants > 1)
                                            {{-- Sesuaikan posisi tombol delete --}}
                                            {{-- Changed right-12 to right-0 for strong right alignment and added mr-4 for margin --}}
                                            <button type="button" onclick="this.closest('div.grid').remove(); updateVariantIndexes();" class="absolute top-4 right-0 mr-5 my-5">
                                                <img src="{{ asset('asset/trash.png') }}" alt="Delete" class="h-8 w-8">
                                            </button>
                                            @endif

                                            {{-- Color --}}
                                            <div>
                                                <x-input-label for="variants_{{ $i }}_color" :value="__('Color')" />
                                                <x-text-input id="variants_{{ $i }}_color" class="block mt-1 w-full" type="text" name="variants[{{ $i }}][color]" :value="old('variants.' . $i . '.color', $oldVariants[$i]['color'] ?? '')" required />
                                                @error('variants.' . $i . '.color')
                                                    <p class="text-sm text-red-600 dark:text-red-400 mt-2">{{ $message }}</p>
                                                @enderror
                                            </div>

                                            {{-- Size --}}
                                            <div>
                                                <x-input-label for="variants_{{ $i }}_size" :value="__('Size')" />
                                                <select id="variants_{{ $i }}_size" name="variants[{{ $i }}][size]" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                                    <option value="">{{ __('Select Size') }}</option>
                                                    @php
                                                        $commonSizes = ['S', 'M', 'L', 'XL', 'XXL', 'One Size'];
                                                    @endphp
                                                    @foreach($commonSizes as $sizeOption)
                                                        <option value="{{ $sizeOption }}" {{ old('variants.' . $i . '.size', $oldVariants[$i]['size'] ?? '') == $sizeOption ? 'selected' : '' }}>
                                                            {{ $sizeOption }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('variants.' . $i . '.size')
                                                    <p class="text-sm text-red-600 dark:text-red-400 mt-2">{{ $message }}</p>
                                                @enderror
                                            </div>

                                            {{-- Stock --}}
                                            <div>
                                                <x-input-label for="variants_{{ $i }}_stock" :value="__('Stock')" />
                                                <x-text-input id="variants_{{ $i }}_stock" class="block mt-1 w-full" type="number" name="variants[{{ $i }}][stock]" :value="old('variants.' . $i . '.stock', $oldVariants[$i]['stock'] ?? '')" required min="0" />
                                                @error('variants.' . $i . '.stock')
                                                    <p class="text-sm text-red-600 dark:text-red-400 mt-2">{{ $message }}</p>
                                                @enderror
                                            </div>

                                            {{-- Variant Price (Optional) --}}
                                            <div>
                                                <x-input-label for="variants_{{ $i }}_price" :value="__('Variant Price (Optional)')" />
                                                <x-text-input id="variants_{{ $i }}_price" class="block mt-1 w-full" type="number" step="0.01" name="variants[{{ $i }}][price]" :value="old('variants.' . $i . '.price', $oldVariants[$i]['price'] ?? '')" />
                                                @error('variants.' . $i . '.price')
                                                    <p class="text-sm text-red-600 dark:text-red-400 mt-2">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>
                                    @endfor
                                </div>

                                {{-- Tombol Plus (+) yang muncul saat hover --}}
                                {{-- Posisi disesuaikan agar di tengah bawah container dengan padding --}}
                                <div onclick="addVariantRow()" class="absolute bottom-[-32px] left-1/2 transform -translate-x-1/2 w-16 h-16 rounded-full flex items-center justify-center bg-gray-100 dark:bg-gray-700 bg-opacity-75 opacity-0 group-hover:opacity-100 transition-opacity duration-300 cursor-pointer shadow-md hover:scale-105"> {{-- bottom-[-32px] untuk lebih ke bawah --}}
                                    <svg class="w-8 h-8 text-gray-500 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                </div>
                            </div>
                        </div> {{-- End of Variants Section --}}

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ms-4">
                                {{ __('Create Product') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function() {
                const output = document.getElementById('image_preview');
                output.src = reader.result;
            }
            reader.readAsDataURL(event.target.files[0]);
        }

        let globalVariantIndex = {{ $numVariants > 0 ? $numVariants : 0 }};

        function addVariantRow() {
            const variantContainer = document.getElementById('variants-container');
            const newVariantRow = document.createElement('div');
            // Menambahkan p-4 border rounded-md ke baris yang ditambahkan via JS
            newVariantRow.className = 'grid grid-cols-1 md:grid-cols-4 gap-4 mb-4 p-4 border border-gray-200 dark:border-gray-700 rounded-md relative mt-4 pr-4'; // Changed pr-16 to pr-4

            newVariantRow.innerHTML = `
                <button type="button" onclick="this.closest('div.grid').remove(); updateVariantIndexes();" class="absolute top-4 right-0 mr-4"> {{-- Changed right-12 to right-0 and added mr-4 --}}
                    <img src="{{ asset('asset/trash.png') }}" alt="Delete" class="h-5 w-5">
                </button>

                <div>
                    <x-input-label for="variants_${globalVariantIndex}_color" :value="__('Color')" />
                    <x-text-input id="variants_${globalVariantIndex}_color" class="block mt-1 w-full" type="text" name="variants[${globalVariantIndex}][color]" required />
                    @error('variants.${globalVariantIndex}.color')
                        <p class="text-sm text-red-600 dark:text-red-400 mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <x-input-label for="variants_${globalVariantIndex}_size" :value="__('Size')" />
                    <select id="variants_${globalVariantIndex}_size" name="variants[${globalVariantIndex}][size]" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                        <option value="">{{ __('Select Size') }}</option>
                        @php
                            $commonSizes = ['S', 'M', 'L', 'XL', 'XXL', 'One Size'];
                        @endphp
                        @foreach($commonSizes as $sizeOption)
                            <option value="{{ $sizeOption }}">{{ $sizeOption }}</option>
                        @endforeach
                    </select>
                    @error('variants.${globalVariantIndex}.size')
                        <p class="text-sm text-red-600 dark:text-red-400 mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <x-input-label for="variants_${globalVariantIndex}_stock" :value="__('Stock')" />
                    <x-text-input id="variants_${globalVariantIndex}_stock" class="block mt-1 w-full" type="number" name="variants[${globalVariantIndex}][stock]" required min="0" />
                    @error('variants.${globalVariantIndex}.stock')
                        <p class="text-sm text-red-600 dark:text-red-400 mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <x-input-label for="variants_${globalVariantIndex}_price" :value="__('Variant Price (Optional)')" />
                    <x-text-input id="variants_${globalVariantIndex}_price" class="block mt-1 w-full" type="number" step="0.01" name="variants[${globalVariantIndex}][price]" />
                    @error('variants.${globalVariantIndex}.price')
                        <p class="text-sm text-red-600 dark:text-red-400 mt-2">{{ $message }}</p>
                    @enderror
                </div>
            `;
            variantContainer.appendChild(newVariantRow);
            globalVariantIndex++;
            updateVariantIndexes();
        }

        function updateVariantIndexes() {
            const variantRows = document.querySelectorAll('#variants-container > div.grid');
            variantRows.forEach((row, newIndex) => {
                // Update names
                row.querySelector('[name^="variants["][name$="][color]"]').name = `variants[${newIndex}][color]`;
                row.querySelector('[name^="variants["][name$="][size]"]').name = `variants[${newIndex}][size]`;
                row.querySelector('[name^="variants["][name$="][stock]"]').name = `variants[${newIndex}][stock]`;
                // Check if the price input exists before trying to access its name property
                const priceInput = row.querySelector('[name^="variants["][name$="][price]"]');
                if (priceInput) {
                    priceInput.name = `variants[${newIndex}][price]`;
                }


                // Update IDs
                row.querySelector('[id^="variants_"][id$="_color"]').id = `variants_${newIndex}_color`;
                row.querySelector('[id^="variants_"][id$="_size"]').id = `variants_${newIndex}_size`;
                row.querySelector('[id^="variants_"][id$="_stock"]').id = `variants_${newIndex}_stock`;
                // Check if the price input exists before trying to access its id property
                const priceIdInput = row.querySelector('[id^="variants_"][id$="_price"]');
                if (priceIdInput) {
                    priceIdInput.id = `variants_${newIndex}_price`;
                }

                const removeButton = row.querySelector('button[onclick*="remove"]');
                if (removeButton) {
                    if (variantRows.length === 1) {
                        removeButton.style.display = 'none';
                    } else {
                        removeButton.style.display = '';
                    }
                }
            });
            globalVariantIndex = variantRows.length;
        }

        document.addEventListener('DOMContentLoaded', updateVariantIndexes);
    </script>
</x-app-layout>
