<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Create New Vendor') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('admin.vendors.store') }}">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                {{-- Name --}}
                                <div class="mb-4">
                                    <x-input-label for="name" :value="__('Vendor Name')" />
                                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                </div>

                                {{-- Email --}}
                                <div class="mb-4">
                                    <x-input-label for="email" :value="__('Email')" />
                                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
                                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                </div>

                                {{-- Phone Number --}}
                                <div class="mb-4">
                                    <x-input-label for="phone_number" :value="__('Phone Number (Optional)')" />
                                    <x-text-input id="phone_number" class="block mt-1 w-full" type="text" name="phone_number" :value="old('phone_number')" />
                                    <x-input-error :messages="$errors->get('phone_number')" class="mt-2" />
                                </div>

                                {{-- Address --}}
                                <div class="mb-4">
                                    <x-input-label for="address" :value="__('Address (Optional)')" />
                                    <x-text-input id="address" class="block mt-1 w-full" type="text" name="address" :value="old('address')" />
                                    <x-input-error :messages="$errors->get('address')" class="mt-2" />
                                </div>

                                {{-- City --}}
                                <div class="mb-4">
                                    <x-input-label for="city" :value="__('City (Optional)')" />
                                    <x-text-input id="city" class="block mt-1 w-full" type="text" name="city" :value="old('city')" />
                                    <x-input-error :messages="$errors->get('city')" class="mt-2" />
                                </div>
                            </div>

                            <div>
                                {{-- Province --}}
                                <div class="mb-4">
                                    <x-input-label for="province" :value="__('Province (Optional)')" />
                                    <x-text-input id="province" class="block mt-1 w-full" type="text" name="province" :value="old('province')" />
                                    <x-input-error :messages="$errors->get('province')" class="mt-2" />
                                </div>

                                {{-- Postal Code --}}
                                <div class="mb-4">
                                    <x-input-label for="postal_code" :value="__('Postal Code (Optional)')" />
                                    <x-text-input id="postal_code" class="block mt-1 w-full" type="text" name="postal_code" :value="old('postal_code')" />
                                    <x-input-error :messages="$errors->get('postal_code')" class="mt-2" />
                                </div>

                                {{-- Business Type --}}
                                <div class="mb-4">
                                    <x-input-label for="business_type" :value="__('Business Type (Optional)')" />
                                    <x-text-input id="business_type" class="block mt-1 w-full" type="text" name="business_type" :value="old('business_type')" />
                                    <x-input-error :messages="$errors->get('business_type')" class="mt-2" />
                                </div>

                                {{-- Business Description --}}
                                <div class="mb-4">
                                    <x-input-label for="business_description" :value="__('Business Description (Optional)')" />
                                    <textarea id="business_description" name="business_description" rows="4" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ old('business_description') }}</textarea>
                                    <x-input-error :messages="$errors->get('business_description')" class="mt-2" />
                                </div>

                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ms-4">
                                {{ __('Create Vendor') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
