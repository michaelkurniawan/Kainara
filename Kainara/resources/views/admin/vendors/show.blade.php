<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Vendor Details: ') . $vendor->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <a href="{{ route('admin.vendors.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-300 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-400 transition ease-in-out duration-150 mb-6">
                        ← {{ __('Back to List') }}
                    </a>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="mb-2"><strong>{{ __('Name:') }}</strong> {{ $vendor->name }}</p>
                            <p class="mb-2"><strong>{{ __('Email:') }}</strong> {{ $vendor->email }}</p>
                            <p class="mb-2"><strong>{{ __('Phone Number:') }}</strong> {{ $vendor->phone_number ?? 'N/A' }}</p>
                            <p class="mb-2"><strong>{{ __('Address:') }}</strong> {{ $vendor->address ?? 'N/A' }}</p>
                            <p class="mb-2"><strong>{{ __('City:') }}</strong> {{ $vendor->city ?? 'N/A' }}</p>
                            <p class="mb-2"><strong>{{ __('Province:') }}</strong> {{ $vendor->province ?? 'N/A' }}</p>
                            <p class="mb-2"><strong>{{ __('Postal Code:') }}</strong> {{ $vendor->postal_code ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="mb-2"><strong>{{ __('Business Type:') }}</strong> {{ $vendor->business_type ?? 'N/A' }}</p>
                            <p class="mb-2"><strong>{{ __('Business Description:') }}</strong> {{ $vendor->business_description ?? 'N/A' }}</p>
                            {{-- Tampilan Status DIHAPUS --}}
                            <p class="mb-2"><strong>{{ __('Created At:') }}</strong> {{ $vendor->created_at->format('d M Y H:i') }}</p>
                            <p class="mb-2"><strong>{{ __('Last Updated:') }}</strong> {{ $vendor->updated_at->format('d M Y H:i') }}</p>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <a href="{{ route('admin.vendors.edit', $vendor) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-2">
                            {{ __('Edit Vendor') }}
                        </a>
                        <form action="{{ route('admin.vendors.destroy', $vendor) }}" method="POST" class="inline-block" onsubmit="return confirm('{{ __('Are you sure you want to delete this vendor?') }}');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Delete Vendor') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
