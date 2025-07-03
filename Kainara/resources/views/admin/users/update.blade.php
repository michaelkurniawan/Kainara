{{-- resources/views/admin/users/edit.blade.php --}}

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
                        {{ __('Edit User') }}
                    </h3>

                    {{-- Menampilkan session status (misal: success message) --}}
                    <x-auth-session-status class="mb-4" :status="session('status')" />

                    <form method="POST" action="{{ route('admin.users.update', $user->id) }}" class="mt-6 space-y-6" enctype="multipart/form-data">
                        @csrf
                        @method('PUT') {{-- PENTING: Untuk method PUT/PATCH --}}

                        {{-- Profile Picture --}}
                        <div class="flex flex-column items-center justify-center mb-6">
                            <label for="profile_picture" class="cursor-pointer relative group">
                                @php
                                    // Tentukan path gambar profil yang akan ditampilkan
                                    $currentProfilePicture = $user->profile_picture;
                                    // Jika default.png disimpan di public/asset/default.png
                                    // dan Anda ingin tetap menggunakan itu sebagai default fallback
                                    $imageUrl = asset('/asset/default.png'); // Default fallback jika path kosong atau tidak valid
                                    if ($currentProfilePicture && file_exists(storage_path('app/public/' . $currentProfilePicture))) {
                                        $imageUrl = asset('storage/' . $currentProfilePicture);
                                    } elseif ($currentProfilePicture === '/asset/default.png') {
                                        $imageUrl = asset('/asset/default.png');
                                    }
                                    // Logika ini sedikit lebih kompleks karena Anda menggunakan dua skema path default
                                    // Jika Anda pindahkan default.png ke storage/app/public/avatars/default.png
                                    // maka bisa disederhanakan menjadi:
                                    // $imageUrl = asset('storage/' . ($user->profile_picture ?? 'avatars/default.png'));
                                @endphp
                                <img id="profile_picture_preview"
                                     src="{{ $imageUrl }}"
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
                            <x-text-input id="first_name" class="block mt-1 w-full" type="text" name="first_name" :value="old('first_name', $user->first_name)" required autofocus autocomplete="first_name" />
                            <x-input-error class="mt-2" :messages="$errors->get('first_name')" />
                        </div>
                        {{-- Last Name --}}
                        <div>
                            <x-input-label for="last_name" :value="__('Last Name')" />
                            <x-text-input id="last_name" class="block mt-1 w-full" type="text" name="last_name" :value="old('last_name', $user->last_name)" required autofocus autocomplete="last_name" />
                            <x-input-error class="mt-2" :messages="$errors->get('last_name')" />
                        </div>

                        {{-- Email --}}
                        <div class="mt-4">
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $user->email)" required autocomplete="username" />
                            <x-input-error class="mt-2" :messages="$errors->get('email')" />
                        </div>

                        {{-- Password (Opsional untuk Update) --}}
                        <div class="mt-4">
                            <x-input-label for="password" :value="__('Password (Leave blank to keep current)')" />
                            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" autocomplete="new-password" /> {{-- Hapus 'required' --}}
                            <x-input-error class="mt-2" :messages="$errors->get('password')" />
                        </div>

                        {{-- Confirm Password (Opsional untuk Update) --}}
                        <div class="mt-4">
                            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" autocomplete="new-password" /> {{-- Hapus 'required' --}}
                            <x-input-error class="mt-2" :messages="$errors->get('password_confirmation')" />
                        </div>

                        {{-- Role Dropdown --}}
                        <div class="mt-4">
                            <x-input-label for="role" :value="__('Role')" />
                            <select id="role" name="role" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="user" {{ old('role', $user->role) == 'user' ? 'selected' : '' }}>User</option>
                                <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('role')" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ms-4 bg-[#B39C59] hover:bg-[#AD9D6D]">
                                {{ __('Update User') }}
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