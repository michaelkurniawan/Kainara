<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('admin.login') }}">
        @csrf
        <div class="mb-10 text-center text-2xl font-medium text-slate-800">Login as Administrator</div>

        <!-- Email Address -->
        <div>
            {{-- <x-input-label for="email" :value="__('Email')" /> --}}
            <x-text-input id="email" class="block mt-1 w-full placeholder-slate-700 placeholder-opacity-80 italic" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="Email" /> {{-- Added placeholder --}}
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            {{-- <x-input-label for="password" :value="__('Password')" /> --}}

            <x-text-input id="password" class="block mt-1 w-full placeholder-slate-700 placeholder-opacity-80 italic focus:border-none focus:ring-[#B39C59]"
                            type="password"
                            name="password"
                            required autocomplete="current-password" 
                            placeholder="Password"/>

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button class="ms-3 text-white bg-[#B39C59] hover:bg-[#AD9D6D]">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
