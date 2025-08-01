<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Order Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold">{{ __('Order List') }}</h3>
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
                        <form action="{{ route('admin.orders.index') }}" method="GET" class="flex flex-col sm:flex-row items-center space-y-2 sm:space-y-0 sm:space-x-2">
                            <div class="flex-1 w-full sm:w-auto">
                                <label for="status_filter" class="sr-only">{{ __('Filter by Status') }}</label>
                                <select id="status_filter" name="status" onchange="this.form.submit()" class="block w-full md:w-auto border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value="">{{ __('All Orders') }}</option>
                                    <option value="Uncompleted" {{ $filterStatus == 'Uncompleted' ? 'selected' : '' }}>
                                        {{ __('Uncompleted Orders') }}
                                    </option>
                                    @foreach($allStatuses as $statusOption)
                                        @if($statusOption !== 'Uncompleted')
                                            <option value="{{ $statusOption }}" {{ $filterStatus == $statusOption ? 'selected' : '' }}>
                                                {{ __($statusOption) }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            {{-- Anda bisa menambahkan input search di sini jika diperlukan untuk Order ID/Customer Name --}}
                            {{-- <div class="flex-1 w-full sm:w-auto">
                                <x-text-input type="text" name="search" placeholder="{{ __('Search orders...') }}" value="{{ request('search') }}" class="w-full" />
                            </div> --}}
                            <x-primary-button class="bg-[#B39C59] hover:bg-[#AD9D6D] w-full sm:w-auto">
                                {{ __('Apply Filter') }}
                            </x-primary-button>
                            @if($filterStatus)
                                <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-300 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400 transition ease-in-out duration-150 w-full sm:w-auto justify-center">
                                    {{ __('Reset Filter') }}
                                </a>
                            @endif
                        </form>
                    </div>
                    {{-- End Search and Filter Bar --}}

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('Order ID') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('Customer') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('Total Amount') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('Status') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('Order Date') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('Actions') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($orders as $order)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $order->id }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                            {{ $order->user->name ?? $order->original_user_name ?? 'Guest' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                            IDR {{ number_format($order->grand_total, 0, ',', '.') }} {{-- Menggunakan grand_total accessor --}}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                @if($order->status == 'Order Confirmed') bg-blue-100 text-blue-800
                                                @elseif($order->status == 'Awaiting Shipment') bg-yellow-100 text-yellow-800
                                                @elseif($order->status == 'Shipped') bg-purple-100 text-purple-800
                                                @elseif($order->status == 'Delivered') bg-green-100 text-green-800
                                                @elseif($order->status == 'Returned') bg-red-100 text-red-800
                                                @elseif($order->status == 'Completed') bg-teal-100 text-teal-800
                                                @elseif($order->status == 'Canceled') bg-gray-400 text-gray-800
                                                @elseif($order->status == 'Refunded') bg-pink-100 text-pink-800
                                                @elseif($order->status == 'Refund Pending') bg-yellow-100 text-yellow-800
                                                @elseif($order->status == 'Refund Failed') bg-red-100 text-red-800
                                                @elseif($order->status == 'Refund Rejected') bg-gray-400 text-gray-800
                                                @elseif($order->status == 'Refund Approved') bg-blue-100 text-blue-800
                                                @else bg-gray-100 text-gray-800 @endif">
                                                {{ $order->status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                            {{ $order->created_at->format('d M Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('admin.orders.show', $order) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-200 mr-2">
                                                {{ __('View Details') }}
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-300">
                                            {{ __('No orders found.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $orders->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>