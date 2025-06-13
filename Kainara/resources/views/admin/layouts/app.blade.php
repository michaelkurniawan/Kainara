<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Admin Marketplace</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome CDN untuk ikon -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        /* Custom scrollbar untuk sidebar jika diperlukan */
        .sidebar-scroll {
            overflow-y: auto;
            scrollbar-width: thin; /* Firefox */
            scrollbar-color: #4a5568 #2d3748; /* thumb and track color */
        }

        .sidebar-scroll::-webkit-scrollbar {
            width: 8px;
        }

        .sidebar-scroll::-webkit-scrollbar-track {
            background: #2d3748;
        }

        .sidebar-scroll::-webkit-scrollbar-thumb {
            background-color: #4a5568;
            border-radius: 4px;
            border: 2px solid #2d3748;
        }
    </style>
</head>

<body class="bg-gray-100 font-sans antialiased">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <aside class="w-64 text-white flex-shrink-0 shadow-lg sidebar-scroll" style="background-color: #B6B09F;">
            <div class="p-14 text-2xl font-bold text-center">
                <a href="{{ route('admin.dashboard') }}" class="text-white hover:text-gray-300 transition duration-200">
                    <i class="Logo"></i> Kainara
                </a>
            </div>
            <nav class="mt-6">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-700 hover:text-white transition duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-gray-700 text-white' : '' }}">
                    <i class="mr-3"></i> Dashboard
                </a>
                <a href="{{ route('admin.products.index') }}" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-700 hover:text-white transition duration-200 {{ request()->routeIs('admin.products.*') ? 'bg-gray-700 text-white' : '' }}">
                    <i class="mr-3"></i> Products
                </a>
                <a href="{{ route('admin.users.index') }}" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-700 hover:text-white transition duration-200 {{ request()->routeIs('admin.users.*') ? 'bg-gray-700 text-white' : '' }}">
                    <i class="mr-3"></i> Users
                </a>
                <a href="{{ route('admin.articles.index') }}" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-700 hover:text-white transition duration-200 {{ request()->routeIs('admin.articles.*') ? 'bg-gray-700 text-white' : '' }}">
                    <i class="mr-3"></i> Articles
                </a>
                @if (Schema::hasTable('categories')) {{-- Hanya tampilkan jika tabel categories ada --}}
                <a href="{{ route('admin.categories.index') }}" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-700 hover:text-white transition duration-200 {{ request()->routeIs('admin.categories.*') ? 'bg-gray-700 text-white' : '' }}">
                    <i class="mr-3"></i> Category
                </a>
                @endif
                <!-- Tambahkan navigasi admin lainnya di sini -->
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="p-14 flex justify-between items-center z-10" style="background-color: #B6B09F;">
                <h1 class="text-2xl font-semibold text-gray-800">@yield('title')</h1>
                <div class="relative group">
                    <button class="flex items-center text-gray-600 hover:text-gray-900 focus:outline-none transition duration-200">
                        <span class="mr-2 font-medium">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</span>
                        <i class="fas fa-user-circle text-xl"></i>
                    </button>
                    <!-- Dropdown profil (contoh, bisa dikembangkan lebih lanjut) -->
                    <div class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 opacity-0 group-hover:opacity-100 group-focus-within:opacity-100 transition-opacity duration-200 invisible group-hover:visible group-focus-within:visible">
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profil</a>
                        <div class="border-t border-gray-100"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-700 hover:bg-red-100">Logout</button>
                        </form>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6">
                <!-- Pesan flash untuk sukses atau error -->
                @if (session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <strong class="font-bold">Sukses!</strong>
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif

                @if (session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <strong class="font-bold">Error!</strong>
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <strong class="font-bold">Ada Kesalahan!</strong>
                        <ul class="mt-2 list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
</body>

</html>

<!-- 
<div class="absolute bottom-0 w-full p-6 border-t border-gray-700">
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="flex items-center w-full px-6 py-3 text-left text-gray-300 hover:bg-gray-700 hover:text-white transition duration-200 rounded-md">
            <i class="fas fa-sign-out-alt mr-3"></i> Logout
        </button>
    </form>
</div> 
-->
            