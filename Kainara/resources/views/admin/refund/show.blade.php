<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Refund Request Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold">{{ __('Refund Details') }} #{{ $refund->id }}</h3>
                        <a href="{{ route('admin.refunds.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-300 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400 transition ease-in-out duration-150">
                            <i class="fas fa-arrow-left fa-sm text-gray-500 dark:text-gray-300 mr-2"></i> {{ __('Back to Refund Requests') }}
                        </a>
                    </div>

                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        {{-- Refund Information Card --}}
                        <div class="lg:col-span-2 bg-gray-50 dark:bg-gray-700 p-6 rounded-lg shadow-md">
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ __('Refund Information') }}</h4>
                            <div class="space-y-3">
                                <div class="flex items-center">
                                    <p class="w-1/3 font-medium text-gray-700 dark:text-gray-300">{{ __('Refund ID:') }}</p>
                                    <p class="w-2/3 text-gray-900 dark:text-gray-100">{{ $refund->id }}</p>
                                </div>
                                <div class="flex items-center">
                                    <p class="w-1/3 font-medium text-gray-700 dark:text-gray-300">{{ __('Stripe Refund ID:') }}</p>
                                    <p class="w-2/3 text-gray-900 dark:text-gray-100">{{ $refund->stripe_refund_id ?? 'N/A' }}</p>
                                </div>
                                <div class="flex items-center">
                                    <p class="w-1/3 font-medium text-gray-700 dark:text-gray-300">{{ __('Order ID:') }}</p>
                                    <p class="w-2/3 text-gray-900 dark:text-gray-100">
                                        @if($refund->payment && $refund->payment->order)
                                            <a href="{{ route('admin.orders.show', $refund->payment->order->id) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-200">#{{ $refund->payment->order->id }}</a>
                                        @else
                                            N/A
                                        @endif
                                    </p>
                                </div>
                                <div class="flex items-center">
                                    <p class="w-1/3 font-medium text-gray-700 dark:text-gray-300">**{{ __('Customer Name:') }}**</p>
                                    <p class="w-2/3 text-gray-900 dark:text-gray-100">
                                        @if($refund->payment && $refund->payment->order && $refund->payment->order->user)
                                            {{ $refund->payment->order->user->first_name . ' ' . $refund->payment->order->user->last_name }}
                                        @elseif($refund->payment && $refund->payment->order)
                                            {{ $refund->payment->order->original_user_name ?? 'Guest' }}
                                        @else
                                            N/A
                                        @endif
                                    </p>
                                </div>
                                <div class="flex items-center">
                                    <p class="w-1/3 font-medium text-gray-700 dark:text-gray-300">{{ __('Refunded Amount:') }}</p>
                                    <p class="w-2/3 text-gray-900 dark:text-gray-100">IDR {{ number_format($refund->refunded_amount, 0, ',', '.') }}</p>
                                </div>
                                <div class="flex items-center">
                                    <p class="w-1/3 font-medium text-gray-700 dark:text-gray-300">{{ __('Reason:') }}</p>
                                    <p class="w-2/3 text-gray-900 dark:text-gray-100">{{ $refund->reason ?? 'No reason provided by user.' }}</p>
                                </div>
                                <div class="flex items-center">
                                    <p class="w-1/3 font-medium text-gray-700 dark:text-gray-300">{{ __('Status:') }}</p>
                                    <p class="w-2/3">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            @if($refund->status === 'succeeded') bg-green-100 text-green-800
                                            @elseif($refund->status === 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($refund->status === 'failed') bg-red-100 text-red-800
                                            @elseif($refund->status === 'rejected') bg-gray-100 text-gray-800
                                            @elseif($refund->status === 'approved') bg-blue-100 text-blue-800
                                            @endif">
                                            {{ Str::title(str_replace('_', ' ', $refund->status)) }}
                                        </span>
                                    </p>
                                </div>
                                <div class="flex items-center">
                                    <p class="w-1/3 font-medium text-gray-700 dark:text-gray-300">{{ __('Requested At:') }}</p>
                                    <p class="w-2/3 text-gray-900 dark:text-gray-100">{{ $refund->created_at->format('d M Y H:i') }}</p>
                                </div>
                                <div class="flex items-center">
                                    <p class="w-1/3 font-medium text-gray-700 dark:text-gray-300">{{ __('Refunded At (Stripe):') }}</p>
                                    <p class="w-2/3 text-gray-900 dark:text-gray-100">{{ $refund->refunded_at ? $refund->refunded_at->format('d M Y H:i') : 'N/A' }}</p>
                                </div>
                                <div class="flex items-start">
                                    <p class="w-1/3 font-medium text-gray-700 dark:text-gray-300">{{ __('Proof Image:') }}</p>
                                    <div class="w-2/3">
                                        @if($refund->refund_image)
                                            <a href="{{ Storage::url($refund->refund_image) }}" target="_blank">
                                                <img src="{{ Storage::url($refund->refund_image) }}" alt="Refund Proof" class="max-w-xs rounded-md shadow-sm">
                                            </a>
                                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-300"><a href="{{ Storage::url($refund->refund_image) }}" target="_blank" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-200">{{ __('View Full Image') }}</a></p>
                                        @else
                                            <p class="text-gray-900 dark:text-gray-100">{{ __('No image provided.') }}</p>
                                        @endif
                                    </div>
                                </div>
                                {{-- Admin Notes display --}}
                                <div class="flex items-start">
                                    <p class="w-1/3 font-medium text-gray-700 dark:text-gray-300">**{{ __('Admin Notes:') }}**</p>
                                    <p class="w-2/3 text-gray-900 dark:text-gray-100">{{ $refund->admin_notes ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Update Status Card --}}
                        <div class="lg:col-span-1 bg-gray-50 dark:bg-gray-700 p-6 rounded-lg shadow-md">
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ __('Update Refund Status') }}</h4>
                            <form action="{{ route('admin.refunds.updateStatus', $refund->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="mb-4">
                                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Status') }}</label>
                                    <select name="status" id="status" class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600">
                                        @foreach($allStatuses as $status)
                                            <option value="{{ $status }}" {{ $refund->status === $status ? 'selected' : '' }}>{{ Str::title(str_replace('_', ' ', $status)) }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Admin Notes textarea --}}
                                <div class="mb-4">
                                    <label for="admin_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Admin Notes (Optional)') }}</label>
                                    <textarea name="admin_notes" id="admin_notes" rows="3" class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600" placeholder="{{ __('Add internal notes about this refund...') }}">{{ old('admin_notes', $refund->admin_notes ?? '') }}</textarea>
                                </div>

                                <x-primary-button class="bg-green-600 hover:bg-green-700 w-full justify-center">
                                    {{ __('Update Status') }}
                                </x-primary-button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>