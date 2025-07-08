<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'ProTrader')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    {{-- Stack for additional head elements --}}
    @stack('head')
</head>
<body class="bg-gray-50 font-sans min-h-screen flex flex-col">
    <nav class="bg-white border-b border-gray-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center space-x-4">
                    <a href="/" class="flex items-center space-x-2">
                        <div class="bg-blue-600 text-white rounded-full h-8 w-8 flex items-center justify-center text-lg font-bold">
                            PT
                        </div>
                        <span class="text-xl font-bold text-gray-800">ProTrader</span>
                    </a>
                </div>

                <div class="flex items-center space-x-6">
                    <a href="/stock" class="text-gray-700 hover:text-blue-600 font-medium transition {{ request()->is('stock') ? 'text-blue-600 font-semibold' : '' }}">Predict</a>
                    <a href="/models" class="text-gray-700 hover:text-blue-600 font-medium transition {{ request()->is('models') ? 'text-blue-600 font-semibold' : '' }}">Models</a>
                    {{-- === ADDED LEARN LINK === --}}
                    <a href="{{ route('learn') }}" class="text-gray-700 hover:text-blue-600 font-medium transition {{ request()->routeIs('learn') ? 'text-blue-600 font-semibold' : '' }}">Learn</a>
                    {{-- ======================== --}}
                    <a href="/about" class="text-gray-700 hover:text-blue-600 font-medium transition {{ request()->is('about') ? 'text-blue-600 font-semibold' : '' }}">About</a>
                </div>
            </div>
        </div>
    </nav>

    <main class="flex-1 max-w-5xl mx-auto py-10 px-4">
        @yield('content')
    </main>

    <footer class="bg-white border-t border-gray-200 py-4 text-center text-gray-500 text-sm">
        © {{ date('Y') }} ProTrader. All rights reserved.
    </footer>

     {{-- Stack for scripts --}}
    @stack('scripts')
</body>
</html>
