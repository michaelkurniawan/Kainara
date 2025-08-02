<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Order Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    {{-- Header Order with Customer Name and Status --}}
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-1">
                                {{ __('Order') }} #{{ $order->id }}
                            </h3>
                            <p class="text-lg font-semibold text-gray-700 dark:text-gray-300">
                                {{ __('Customer:') }} {{ $order->user->name ?? $order->original_user_name ?? 'Guest' }}
                            </p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                {{ $order->user->email ?? $order->original_user_email ?? 'N/A' }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="mb-1 text-sm text-gray-600 dark:text-gray-400">
                                {{ __('Order Date:') }} {{ $order->created_at->format('d M Y H:i') }}
                            </p>
                            <span class="px-3 py-1 inline-flex text-base leading-5 font-semibold rounded-full
                                @if($order->status == 'Order Confirmed') bg-blue-100 text-blue-800
                                @elseif($order->status == 'Awaiting Shipment') bg-yellow-100 text-yellow-800
                                @elseif($order->status == 'Shipped') bg-purple-100 text-purple-800
                                @elseif($order->status == 'Delivered') bg-green-100 text-green-800
                                @elseif($order->status == 'Canceled') bg-red-100 text-red-800
                                @elseif($order->status == 'Returned') bg-red-100 text-red-800
                                @elseif($order->status == 'Refunded') bg-pink-100 text-pink-800
                                @elseif($order->status == 'Completed') bg-teal-100 text-teal-800
                                @elseif($order->status == 'Refund Pending') bg-yellow-100 text-yellow-800
                                @elseif($order->status == 'Refund Failed') bg-red-100 text-red-800
                                @elseif($order->status == 'Refund Rejected') bg-gray-400 text-gray-800
                                @elseif($order->status == 'Refund Approved') bg-blue-100 text-blue-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ $order->status }}
                            </span>
                        </div>
                    </div>
                    <hr class="mb-6 border-gray-200 dark:border-gray-700">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        {{-- Order Costs & Completion --}}
                        <div>
                            <h4 class="text-lg font-semibold mb-2">{{ __('Cost Summary') }}</h4>
                            {{-- Removed Subtotal and Shipping Cost --}}
                            <p class="mb-1 text-xl font-bold"><strong>{{ __('Total Amount:') }}</strong> IDR {{ number_format($order->grand_total, 0, ',', '.') }}</p> {{-- Use grand_total --}}
                            <p class="mb-1 mt-4"><strong>{{ __('Order Completed:') }}</strong> {{ $order->is_completed ? 'Yes' : 'No' }}</p>
                            @if($order->completed_at)
                                <p class="mb-1"><strong>{{ __('Completed At:') }}</strong> {{ $order->completed_at->format('d M Y H:i') }}</p>
                            @endif
                        </div>

                        {{-- Shipping Information --}}
                        <div>
                            <h4 class="text-lg font-semibold mb-2">{{ __('Shipping Details') }}</h4>
                            <p class="mb-1"><strong>{{ __('Recipient Name:') }}</strong> {{ $order->shipping_recipient_name ?? 'N/A' }}</p>
                            <p class="mb-1"><strong>{{ __('Phone:') }}</strong> {{ $order->shipping_phone ?? 'N/A' }}</p>
                            <p class="mb-1"><strong>{{ __('Address:') }}</strong> {{ $order->shipping_address ?? 'N/A' }}</p>
                            <p class="mb-1"><strong>{{ __('City:') }}</strong> {{ $order->shipping_city ?? 'N/A' }}</p>
                            <p class="mb-1"><strong>{{ __('Province:') }}</strong> {{ $order->shipping_province ?? 'N/A' }}</p>
                            <p class="mb-1"><strong>{{ __('Postal Code:') }}</strong> {{ $order->shipping_postal_code ?? 'N/A' }}</p>
                            <p class="mb-1"><strong>{{ __('Country:') }}</strong> {{ $order->shipping_country ?? 'N/A' }}</p>
                            @if($order->shipping_label)
                                <p class="mb-1">
                                    <strong>{{ __('Shipping Label:') }}</strong>
                                    <a href="{{ route('admin.orders.download-label', $order) }}"
                                       class="text-blue-500 hover:underline">
                                        Download Label
                                    </a>
                                </p>
                            @endif
                            {{-- Display delivery details from the deliveries table --}}
                            @if($order->delivery)
                                <p class="mb-1"><strong>{{ __('Courier:') }}</strong> {{ $order->delivery->courier_name ?? 'N/A' }}</p>
                                <p class="mb-1"><strong>{{ __('Tracking Number:') }}</strong> {{ $order->delivery->tracking_number ?? 'N/A' }}</p>
                                @if($order->delivery->shipped_at)
                                    <p class="mb-1"><strong>{{ __('Shipped At:') }}</strong> {{ $order->delivery->shipped_at->format('d M Y H:i') }}</p>
                                @endif
                                @if($order->delivery->delivered_at)
                                    <p class="mb-1"><strong>{{ __('Delivered At:') }}</strong> {{ $order->delivery->delivered_at->format('d M Y H:i') }}</p>
                                @endif
                            @endif
                        </div>
                    </div>

                    {{-- Order Items --}}
                    <h4 class="text-lg font-semibold mb-2 mt-6 border-t pt-4">{{ __('Order Items') }}</h4>
                    <div class="overflow-x-auto mb-6">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('Product') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('Variant') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('Price') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('Quantity') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('Total') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($order->orderItems as $item)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                            <div class="flex items-center">
                                                @if($item->product_image)
                                                    <img src="{{ Storage::url($item->product_image) }}" alt="{{ $item->product_name }}" class="w-10 h-10 object-cover rounded-md mr-2">
                                                @endif
                                                {{ $item->product_name }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                            @if($item->variant_size || $item->variant_color)
                                                {{ $item->variant_size ?? '' }} {{ $item->variant_color ? '(' . $item->variant_color . ')' : '' }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                            IDR {{ number_format($item->price, 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                            {{ $item->quantity }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900 dark:text-gray-100">
                                            IDR {{ number_format($item->price * $item->quantity, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-300">
                                            {{ __('No items in this order.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Update Status Section --}}
                    <h4 class="text-lg font-semibold mb-2 mt-6 border-t pt-4">{{ __('Update Order Status') }}</h4>
                    {{-- Alpine.js for conditional input --}}
                    <form x-data="{ currentStatus: '{{ $order->status }}', newStatus: '{{ old('status', $order->status) }}' }"
                          action="{{ route('admin.orders.update', $order) }}"
                          method="POST"
                          class="flex flex-col md:flex-row items-start md:items-center space-y-4 md:space-y-0 md:space-x-4">
                        @csrf
                        @method('PATCH')

                        <select name="status" id="status" x-model="newStatus"
                                class="block w-full md:w-auto border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                            @foreach($allStatuses as $statusOption)
                                <option value="{{ $statusOption }}" {{ $order->status == $statusOption ? 'selected' : '' }}>
                                    {{ $statusOption }}
                                </option>
                            @endforeach
                        </select>

                        {{-- Input Courier Name dan Tracking Number (conditional display) --}}
                        <template x-if="newStatus === 'Awaiting Shipment' && currentStatus === 'Order Confirmed'">
                            <div class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-4 w-full md:w-auto">
                                <div>
                                    <label for="courier_name" class="sr-only">Courier Name</label>
                                    <input type="text" name="courier_name" id="courier_name"
                                            placeholder="Courier Name" value="{{ old('courier_name', $order->delivery->courier_name ?? '') }}"
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200">
                                </div>
                                <div>
                                    <label for="tracking_number" class="sr-only">Tracking Number</label>
                                    <input type="text" name="tracking_number" id="tracking_number"
                                            placeholder="Tracking Number" value="{{ old('tracking_number', $order->delivery->tracking_number ?? '') }}"
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200">
                                </div>
                            </div>
                        </template>

                        <x-primary-button>
                            {{ __('Update Status') }}
                        </x-primary-button>

                        @if ($errors->any())
                            <div class="text-red-500 text-xs mt-1 md:mt-0 md:ml-4">
                                @foreach ($errors->all() as $error)
                                    <p>{{ $error }}</p>
                                @endforeach
                            </div>
                        @endif
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>