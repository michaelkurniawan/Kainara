{{-- resources/views/admin/users/create.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('User Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        {{-- Ganti 'max-w-md' menjadi 'max-w-sm' atau 'max-w-xs' di sini --}}
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8"> {{-- Lebar yang lebih kecil untuk form --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                        {{ __('Add new user') }}
                    </h3>

                    {{-- Menampilkan error validasi dari session, sama seperti Breeze --}}
                    <x-auth-session-status class="mb-4" :status="session('status')" />
                    {{-- <x-input-error :messages="$errors->get('name')" class="mb-4" />
                    <x-input-error :messages="$errors->get('email')" class="mb-4" />
                    <x-input-error :messages="$errors->get('password')" class="mb-4" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mb-4" /> --}}


                    <form method="POST" action="{{ route('admin.users.store') }}" class="mt-6 space-y-6">
                        @csrf

                        {{-- First Name --}}
                        <div>
                            <x-input-label for="first_name" :value="__('First Name')" />
                            <x-text-input id="first_name" class="block mt-1 w-full" type="text" name="first_name" :value="old('first_name')" required autofocus autocomplete="first_name" />
                            <x-input-error class="mt-2" :messages="$errors->get('first_name')" /> {{-- Tambahkan ini --}}
                        </div>
                        {{-- Last Name --}}
                        <div>
                            <x-input-label for="last_name" :value="__('Last Name')" />
                            <x-text-input id="last_name" class="block mt-1 w-full" type="text" name="last_name" :value="old('last_name')" required autofocus autocomplete="last_name" />
                            <x-input-error class="mt-2" :messages="$errors->get('last_name')" /> {{-- Tambahkan ini --}}
                        </div>

                        {{-- Email --}}
                        <div class="mt-4">
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
                            <x-input-error class="mt-2" :messages="$errors->get('email')" /> {{-- Tambahkan ini --}}
                        </div>

                        {{-- Password --}}
                        <div class="mt-4">
                            <x-input-label for="password" :value="__('Password')" />
                            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
                            <x-input-error class="mt-2" :messages="$errors->get('password')" /> {{-- Tambahkan ini --}}
                        </div>

                        {{-- Confirm Password --}}
                        <div class="mt-4">
                            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
                            <x-input-error class="mt-2" :messages="$errors->get('password_confirmation')" /> {{-- Tambahkan ini --}}
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ms-4 bg-[#B39C59] hover:bg-[#AD9D6D]">
                                {{ __('Create User') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>