{{-- resources/views/admin/users/create.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('User Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                        {{ __('Add new user') }}
                    </h3>

                    {{-- Menampilkan error validasi dari session --}}
                    <x-auth-session-status class="mb-4" :status="session('status')" />
                    {{-- <x-input-error :messages="$errors->get('name')" class="mb-4" />
                    <x-input-error :messages="$errors->get('email')" class="mb-4" />
                    <x-input-error :messages="$errors->get('password')" class="mb-4" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mb-4" /> --}}


                    <form method="POST" action="{{ route('admin.users.store') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
                        @csrf

                        {{-- Profile Picture --}}
                        <div class="flex flex-column items-center justify-center mb-6">
                            <label for="profile_picture" class="cursor-pointer relative group">
                                <img id="profile_picture_preview"
                                    src="{{ asset('/asset/default.png') }}"
                                    alt="Profile Picture"
                                    class="w-32 h-32 rounded-full object-cover border-4 border-gray-300 transition-colors duration-200"
                                >
                                <input type="file" id="profile_picture" name="profile_picture" accept="image/*" class="hidden" onchange="previewImage(event)">

                                {{-- Overlay dan Ikon Pensil --}}
                                <div class="absolute inset-0 w-32 h-32 rounded-full bg-black bg-opacity-50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                    <img src="{{ asset('/asset/edit-profile-pic.png') }}" alt="Edit Icon" class="w-8 h-8 text-white">
                                </div>
                            </label>
                            <x-input-error class="mt-2" :messages="$errors->get('profile_picture')" />
                        </div>

                        {{-- First Name --}}
                        <div>
                            <x-input-label for="first_name" :value="__('First Name')" />
                            <x-text-input id="first_name" class="block mt-1 w-full" type="text" name="first_name" :value="old('first_name')" required autofocus autocomplete="first_name" />
                            <x-input-error class="mt-2" :messages="$errors->get('first_name')" />
                        </div>
                        {{-- Last Name --}}
                        <div>
                            <x-input-label for="last_name" :value="__('Last Name')" />
                            <x-text-input id="last_name" class="block mt-1 w-full" type="text" name="last_name" :value="old('last_name')" required autofocus autocomplete="last_name" />
                            <x-input-error class="mt-2" :messages="$errors->get('last_name')" />
                        </div>

                        {{-- Email --}}
                        <div class="mt-4">
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
                            <x-input-error class="mt-2" :messages="$errors->get('email')" />
                        </div>

                        {{-- Password --}}
                        <div class="mt-4">
                            <x-input-label for="password" :value="__('Password')" />
                            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
                            <x-input-error class="mt-2" :messages="$errors->get('password')" />
                        </div>

                        {{-- Confirm Password --}}
                        <div class="mt-4">
                            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
                            <x-input-error class="mt-2" :messages="$errors->get('password_confirmation')" />
                        </div>

                        {{-- Role Dropdown --}}
                        <div class="mt-4">
                            <x-input-label for="role" :value="__('Role')" />
                            <select id="role" name="role" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>User</option>
                                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('role')" />
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

<script>
    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function(){
            const output = document.getElementById('profile_picture_preview');
            output.src = reader.result;
        };
        reader.readAsDataURL(event.target.files[0]);
    }
</script>
