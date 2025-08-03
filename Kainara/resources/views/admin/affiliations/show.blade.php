<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detail Submission: ') . $profile->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900 dark:text-gray-100">

                {{-- Back to List Button --}}
                <a href="{{ route('admin.affiliations.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-300 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400 transition ease-in-out duration-150 mb-6">
                    ← {{ __('Back to List') }}
                </a>

                {{-- Registrant Profile --}}
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Registrant Profile') }}</h3>
                    </div>
                    <div class="p-6">
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-2">
                            <dt class="font-medium text-gray-700 dark:text-gray-300 col-span-1">Full Name:</dt>
                            <dd class="text-gray-900 dark:text-gray-100 col-span-1">{{ $profile->name }}</dd>

                            <dt class="font-medium text-gray-700 dark:text-gray-300 col-span-1">Email:</dt>
                            <dd class="text-gray-900 dark:text-gray-100 col-span-1">{{ $profile->email }}</dd>

                            <dt class="font-medium text-gray-700 dark:text-gray-300 col-span-1">Phone Number:</dt>
                            <dd class="text-gray-900 dark:text-gray-100 col-span-1">{{ $profile->phone_number }}</dd>

                            <dt class="font-medium text-gray-700 dark:text-gray-300 col-span-1">Date of Birth:</dt>
                            <dd class="text-gray-900 dark:text-gray-100 col-span-1">{{ \Carbon\Carbon::parse($profile->date_of_birth)->format('d F Y') }}</dd>

                            <dt class="font-medium text-gray-700 dark:text-gray-300 col-span-1">Gender:</dt>
                            <dd class="text-gray-900 dark:text-gray-100 col-span-1">{{ ucfirst($profile->gender) }}</dd>

                            <dt class="font-medium text-gray-700 dark:text-gray-300 col-span-1">Home Address:</dt>
                            <dd class="text-gray-900 dark:text-gray-100 col-span-1">{{ $profile->home_address }}, {{ $profile->home_city }}, {{ $profile->home_province }} {{ $profile->home_postal_code }}</dd>
                        </dl>
                    </div>
                </div>

                {{-- Business Information --}}
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Business Information') }}</h3>
                    </div>
                    <div class="p-6">
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-2">
                            <dt class="font-medium text-gray-700 dark:text-gray-300 col-span-1">Business Name:</dt>
                            <dd class="text-gray-900 dark:text-gray-100 col-span-1">{{ $profile->business_name }}</dd>

                            <dt class="font-medium text-gray-700 dark:text-gray-300 col-span-1">Business Type:</dt>
                            <dd class="text-gray-900 dark:text-gray-100 col-span-1">{{ $profile->business_type === 'others' ? $profile->other_business_type : $profile->business_type }}</dd>

                            <dt class="font-medium text-gray-700 dark:text-gray-300 col-span-1">Description:</dt>
                            <dd class="text-gray-900 dark:text-gray-100 col-span-1">{{ $profile->business_description }}</dd>

                            @if($profile->business_phone_number || $profile->business_email)
                                <dt class="font-medium text-gray-700 dark:text-gray-300 col-span-1">Business Contact:</dt>
                                <dd class="text-gray-900 dark:text-gray-100 col-span-1">{{ $profile->business_phone_number }} / {{ $profile->business_email }}</dd>
                            @endif
                            @if($profile->business_address)
                                <dt class="font-medium text-gray-700 dark:text-gray-300 col-span-1">Business Address:</dt>
                                <dd class="text-gray-900 dark:text-gray-100 col-span-1">{{ $profile->business_address }}, {{ $profile->business_city }}, {{ $profile->business_province }} {{ $profile->business_postal_code }}</dd>
                            @endif
                        </dl>
                    </div>
                </div>

                {{-- Portfolio --}}
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Portfolio') }}</h3>
                    </div>
                    <div class="p-6">
                        @if($profile->portfolios->isNotEmpty())
                            @foreach($profile->portfolios as $portfolio)
                                <div class="mb-6 border-b border-gray-200 dark:border-gray-700 pb-6 last:border-b-0 last:pb-0">
                                    <div class="md:flex md:space-x-8">
                                        <div class="md:w-1/2">
                                            <h4 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-2">
                                                {{ $portfolio->project_title }}
                                            </h4>
                                            <p class="text-gray-700 dark:text-gray-300 mb-4">
                                                {{ $portfolio->project_description }}
                                            </p>
                                            <div class="space-y-1 text-sm text-gray-700 dark:text-gray-300 mb-4">
                                                <p><strong>Fabric Type:</strong> {{ $portfolio->fabric_type === 'others' ? $portfolio->other_fabric_type : $portfolio->fabric_type }}</p>
                                                <p><strong>Year Created:</strong> {{ $portfolio->year_created }}</p>
                                            </div>
                                            @if($portfolio->video_link)
                                                <div class="mb-4">
                                                    <a href="{{ $portfolio->video_link }}" target="_blank" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-200 font-medium">
                                                        {{ __('View Video') }}
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="md:w-1/2 mt-4 md:mt-0">
                                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                                                @php
                                                    $photoPaths = json_decode($portfolio->photo_path);
                                                @endphp
                                                @if ($photoPaths)
                                                    @foreach($photoPaths as $photo)
                                                        <a href="{{ asset('storage/' . $photo) }}" target="_blank" class="group block aspect-w-1 aspect-h-1 overflow-hidden rounded-lg bg-gray-100 focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                                            <img src="{{ asset('storage/' . $photo) }}" alt="Portfolio Image" class="object-cover pointer-events-none group-hover:opacity-75">
                                                        </a>
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p class="text-gray-500 dark:text-gray-300">{{ __('No portfolio submitted.') }}</p>
                        @endif
                    </div>
                </div>

                <div class="mt-6 flex justify-end space-x-4">
                    <form action="{{ route('admin.affiliations.approve', $profile) }}" method="POST">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-600 active:bg-green-700 focus:outline-none focus:border-green-700 focus:ring focus:ring-green-300 disabled:opacity-25 transition">
                            {{ __('Approve') }}
                        </button>
                    </form>
                    <form action="{{ route('admin.affiliations.reject', $profile) }}" method="POST">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-600 active:bg-red-700 focus:outline-none focus:border-red-700 focus:ring focus:ring-red-300 disabled:opacity-25 transition">
                            {{ __('Reject') }}
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>