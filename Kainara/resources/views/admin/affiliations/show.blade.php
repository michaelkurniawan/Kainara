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

                            {{-- Display business contact & address info if available --}}
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
                        @if($profile->portfolio)
                            @php $portfolio = $profile->portfolio; @endphp

                            <div class="mb-4 pb-4 border-b border-gray-200 dark:border-gray-700 last:border-b-0 last:pb-0">
                                <h5 class="text-md font-semibold text-gray-900 dark:text-gray-100 mb-1">
                                    {{ $portfolio->project_title }} ({{ $portfolio->year_created }})
                                </h5>
                                <p class="text-sm text-gray-700 dark:text-gray-300 mb-1">
                                    <strong>Fabric Type:</strong>
                                    {{ $portfolio->fabric_type === 'others' ? $portfolio->other_fabric_type : $portfolio->fabric_type }}
                                </p>
                                <p class="text-sm text-gray-700 dark:text-gray-300 mb-2">
                                    {{ $portfolio->project_description }}
                                </p>

                                @if($portfolio->video_link)
                                    <p class="text-sm mb-2">
                                        <a href="{{ $portfolio->video_link }}" target="_blank"
                                        class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-200">
                                            {{ __('View Video') }}
                                        </a>
                                    </p>
                                @endif

                                <div class="flex flex-wrap gap-3 mt-3">
                                    @foreach(json_decode($portfolio->photo_paths) as $photo)
                                        <div>
                                            <a href="{{ asset('storage/' . $photo) }}" target="_blank">
                                                <img src="{{ asset('storage/' . $photo) }}" alt="Portfolio Image"
                                                    class="h-36 w-36 object-cover rounded-md shadow-sm">
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <p class="text-gray-500 dark:text-gray-300">{{ __('No portfolio submitted.') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>