<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Refund Requests Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold">{{ __('All Refund Requests') }}</h3>
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

                    {{-- Search and Filter Bar --}}
                    <div class="mb-4">
                        <form action="{{ route('admin.refunds.index') }}" method="GET" class="flex flex-col sm:flex-row items-center space-y-2 sm:space-y-0 sm:space-x-2">
                            <div class="flex-1 w-full sm:w-auto">
                                <label for="filterStatus" class="sr-only">{{ __('Filter by Status') }}</label>
                                <select name="status" id="filterStatus" class="block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600">
                                    <option value="all" {{ $filterStatus === 'all' ? 'selected' : '' }}>{{ __('All Statuses') }}</option>
                                    @foreach($allStatuses as $status)
                                        <option value="{{ $status }}" {{ $filterStatus === $status ? 'selected' : '' }}>{{ Str::title(str_replace('_', ' ', $status)) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex-1 w-full sm:w-auto">
                                <label for="searchQuery" class="sr-only">{{ __('Search (Refund ID, Reason, User)') }}</label>
                                <x-text-input type="text" name="search" placeholder="{{ __('Search refunds...') }}" value="{{ request('search') }}" class="w-full" />
                            </div>
                            <x-primary-button class="bg-[#B39C59] hover:bg-[#AD9D6D] w-full sm:w-auto">
                                {{ __('Apply Filter') }}
                            </x-primary-button>
                            @if(request('search') || $filterStatus !== null && $filterStatus !== 'all')
                                <a href="{{ route('admin.refunds.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-300 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400 transition ease-in-out duration-150 w-full sm:w-auto justify-center">
                                    {{ __('Reset') }}
                                </a>
                            @endif
                        </form>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('Refund ID') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('Order ID') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('Customer Name') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('Amount') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('Reason') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('Proof Image') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('Status') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('Admin Notes') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('Requested At') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('Action') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($refunds as $refund)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $refund->id }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                        @if($refund->payment && $refund->payment->order)
                                            <a href="{{ route('admin.orders.show', $refund->payment->order->id) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-200">#{{ $refund->payment->order->id }}</a>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                        @if($refund->payment && $refund->payment->order && $refund->payment->order->user)
                                            {{ $refund->payment->order->user->first_name . ' ' . $refund->payment->order->user->last_name }}
                                        @elseif($refund->payment && $refund->payment->order)
                                            {{ $refund->payment->order->original_user_name ?? 'Guest' }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                        IDR {{ number_format($refund->refunded_amount, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-normal text-sm text-gray-500 dark:text-gray-300 max-w-xs">
                                        {{ Str::limit($refund->reason ?? 'No reason provided', 50) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                        @if($refund->refund_image)
                                            <a href="{{ Storage::url($refund->refund_image) }}" target="_blank" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-200">View Image</a>
                                        @else
                                            No Image
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            @if($refund->status === 'succeeded') bg-green-100 text-green-800
                                            @elseif($refund->status === 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($refund->status === 'failed') bg-red-100 text-red-800
                                            @elseif($refund->status === 'rejected') bg-gray-100 text-gray-800
                                            @elseif($refund->status === 'approved') bg-blue-100 text-blue-800
                                            @endif">
                                            {{ Str::title(str_replace('_', ' ', $refund->status)) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-normal text-sm text-gray-500 dark:text-gray-300 max-w-xs">
                                        {{ Str::limit($refund->admin_notes ?? 'N/A', 50) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                        {{ $refund->created_at->format('Y-m-d H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('admin.refunds.show', $refund->id) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-200">
                                            {{ __('View Details') }}
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-300">
                                        {{ __('No refund requests found.') }}
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $refunds->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>