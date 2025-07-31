<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-8">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{-- Bagian Sapaan Dinamis --}}
                    <span id="dynamicGreeting" class="font-semibold text-xl leading-tight text-gray-900 dark:text-gray-100"></span>
                    <span class="typing-cursor text-gray-900 dark:text-gray-100"></span>
                    <span class="font-semibold text-xl leading-tight text-gray-900 dark:text-gray-100">, {{ $adminName }}</span>
                </div>
            </div>

            {{-- Bagian Metrik Utama --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">

                {{-- Kartu Jumlah Order Aktif --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-semibold mb-2">{{ __('Number of Active Orders') }}</h3>
                        <p class="text-4xl font-bold text-[#B39C59] dark:text-[#AD9D6D]">
                            {{ $activeOrdersCount }}
                        </p>
                        <p class="text-sm text-gray-500 mt-1">{{ __('Active Orders') }}</p>
                    </div>
                </div>

                {{-- Kartu Pendapatan Bulan Ini --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-semibold mb-2">{{ __('This Month Revenue') }}</h3>
                        <p class="text-4xl font-bold text-green-600 dark:text-green-400">
                            {{ $formatedMonthlyRevenue }}
                        </p>
                        <p class="text-sm text-gray-500 mt-1">{{ __('Estimated revenue') }}</p>
                    </div>
                </div>

                {{-- Kartu Total Produk --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-semibold mb-2">{{ __('Available Stock') }}</h3>
                        <p class="text-4xl font-bold text-blue-600 dark:text-blue-400">
                            {{ $totalStock }}
                        </p>
                        <p class="text-sm text-gray-500 mt-1">{{ __('Item(s)') }}</p>
                    </div>
                </div>

            </div>

            {{-- Bagian Order Terbaru --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-8">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">{{ __('Newest Order') }}</h3>
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
                                        {{ __('Subtotal') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('Status') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('Date') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($recentOrders as $order) {{-- Loop data order di sini --}}
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">#ORD{{ $order->id }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $order->original_user_name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">IDR {{ number_format($order->subtotal, 0, ',', '.') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @php
                                                $statusClass = '';
                                                switch ($order->status) {
                                                    case 'Awaiting Payment':
                                                        $statusClass = 'bg-yellow-100 text-yellow-800';
                                                        break;
                                                    case 'Order Confirmed':
                                                    case 'Awaiting Shipment':
                                                        $statusClass = 'bg-blue-100 text-blue-800';
                                                        break;
                                                    case 'Shipped':
                                                        $statusClass = 'bg-purple-100 text-purple-800';
                                                        break;
                                                    case 'Delivered':
                                                    case 'Completed':
                                                        $statusClass = 'bg-green-100 text-green-800';
                                                        break;
                                                    case 'Canceled':
                                                    case 'Returned':
                                                    case 'Refunded':
                                                        $statusClass = 'bg-red-100 text-red-800';
                                                        break;
                                                    default:
                                                        $statusClass = 'bg-gray-100 text-gray-800';
                                                        break;
                                                }
                                            @endphp
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                                {{ $order->status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $order->created_at->format('Y-m-d H:i') }}</td> {{-- Menambahkan jam dan menit --}}
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-300">Tidak ada order terbaru.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Bagian Lainnya --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg col-span-1 lg:col-span-2"> {{-- Tambahkan col-span untuk mengambil seluruh lebar --}}
                        <div class="p-6 text-gray-900 dark:text-gray-100">
                            <h3 class="text-lg font-semibold mb-2">{{ __('Sale Summary') }}</h3>
                            <p class="text-sm text-gray-700 dark:text-gray-300">
                                Today revenue: <span class="font-bold">IDR {{ $todayRevenue }}</span>
                            </p>
                            <p class="text-sm text-gray-700 dark:text-gray-300">
                                Weekly revenue: <span class="font-bold">IDR {{ $weekRevenue }}</span>
                            </p>
                            <p class="text-sm text-gray-700 dark:text-gray-300">
                                All time revenue: <span class="font-bold">IDR {{ $allTimeRevenue }}</span>
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Script untuk Animasi Pengetikan --}}
    <style>
        /* Optional: Style for the typing cursor */
        .typing-cursor {
            display: inline-block;
            width: 2px; /* Lebar kursor */
            height: 1.2em; /* Tinggi mirip teks */
            background-color: currentColor; /* Mencocokkan warna teks */
            vertical-align: middle;
            animation: blink 1s infinite;
            margin-left: 2px;
        }

        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0; }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const greetingElement = document.getElementById('dynamicGreeting');
            const greetings = [
                "Hi",
                "Halo",
                "Bonjour",
                "Hola",
                "Selamat datang"
            ];
            let greetingIndex = 0;
            let charIndex = 0;
            let isDeleting = false;
            const typingSpeed = 150; // milliseconds per character
            const deletingSpeed = 100; // milliseconds per character
            const pauseTime = 2000;

            function typeWriter() {
                const currentGreeting = greetings[greetingIndex];

                if (isDeleting) {
                    if (charIndex > 0) {
                        greetingElement.textContent = currentGreeting.substring(0, charIndex - 1);
                        charIndex--;
                        setTimeout(typeWriter, deletingSpeed);
                    } else {
                        isDeleting = false;
                        greetingIndex = (greetingIndex + 1) % greetings.length;
                        setTimeout(typeWriter, typingSpeed);
                    }
                } else {
                    if (charIndex < currentGreeting.length) {
                        greetingElement.textContent = currentGreeting.substring(0, charIndex + 1);
                        charIndex++;
                        setTimeout(typeWriter, typingSpeed);
                    } else {
                        isDeleting = true;
                        setTimeout(typeWriter, pauseTime);
                    }
                }
            }

            typeWriter();
        });
    </script>
</x-app-layout>
