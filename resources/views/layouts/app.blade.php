<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'MyTrades - Trading Journal & Performance Analytics')</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Tailwind CSS CDN (v3.4) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    },
                    colors: {
                        brand: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                        },
                        profit: {
                            50: '#ecfdf5',
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                        },
                        loss: {
                            50: '#fef2f2',
                            500: '#ef4444',
                            600: '#dc2626',
                            700: '#b91c1c',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Dark Mode Initializer -->
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }

        function toggleTheme() {
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.theme = 'light';
            } else {
                document.documentElement.classList.add('dark');
                localStorage.theme = 'dark';
            }
            window.dispatchEvent(new Event('theme-changed'));
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
        .gradient-brand {
            background: linear-gradient(135deg, #4f46e5 0%, #2563eb 50%, #06b6d4 100%);
        }
        .profit-badge {
            background-color: rgba(16, 185, 129, 0.12);
            color: #059669;
            border: 1px solid rgba(16, 185, 129, 0.25);
        }
        .dark .profit-badge {
            background-color: rgba(16, 185, 129, 0.2);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.35);
        }
        .loss-badge {
            background-color: rgba(239, 68, 68, 0.12);
            color: #dc2626;
            border: 1px solid rgba(239, 68, 68, 0.25);
        }
        .dark .loss-badge {
            background-color: rgba(239, 68, 68, 0.2);
            color: #f87171;
            border: 1px solid rgba(239, 68, 68, 0.35);
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        .dark ::-webkit-scrollbar-thumb { background: #334155; }
    </style>
</head>
<body class="h-full bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 flex flex-col font-sans transition-colors duration-200">

    <!-- Navigation Header -->
    <header class="sticky top-0 z-40 bg-white/90 dark:bg-slate-900/90 backdrop-blur-md border-b border-slate-200/80 dark:border-slate-800/80 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                
                <!-- Logo & Brand -->
                <div class="flex items-center gap-8">
                    <a href="{{ auth()->check() ? (auth()->user()->isAdmin() ? route('admin.dashboard') : route('trades.index')) : route('login') }}" class="flex items-center group py-1">
                        <img src="{{ asset('images/logo.webp') }}" alt="MyTrades" class="h-9 sm:h-10 w-auto dark:hidden transition-transform duration-200 group-hover:scale-105">
                        <img src="{{ asset('images/logo-dark.webp') }}" alt="MyTrades" class="h-9 sm:h-10 w-auto hidden dark:block transition-transform duration-200 group-hover:scale-105">
                    </a>

                    <!-- Main Navigation Links (Authenticated Users) -->
                    @auth
                    @if(!auth()->user()->isAdmin() || request()->is('trades*') || request()->is('analytics*') || request()->is('strategies*'))
                    <nav class="hidden md:flex items-center gap-1.5 text-sm font-medium">
                        <a href="{{ route('trades.index') }}" 
                           class="px-3.5 py-2 rounded-lg transition-colors {{ request()->routeIs('trades.index') ? 'bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 font-semibold' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                            <i class="fa-solid fa-book-open mr-1.5 opacity-70"></i> Journal
                        </a>
                        <a href="{{ route('analytics.index') }}" 
                           class="px-3.5 py-2 rounded-lg transition-colors {{ request()->routeIs('analytics.index') ? 'bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 font-semibold' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                            <i class="fa-solid fa-chart-pie mr-1.5 opacity-70"></i> Analytics
                        </a>
                        <a href="{{ route('strategies.index') }}" 
                           class="px-3.5 py-2 rounded-lg transition-colors {{ request()->routeIs('strategies.index') ? 'bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 font-semibold' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                            <i class="fa-solid fa-shapes mr-1.5 opacity-70"></i> Strategies
                        </a>
                    </nav>
                    @endif
                    @endauth
                </div>

                <!-- Right Side Actions -->
                <div class="flex items-center gap-3">
                    
                    @auth
                        <!-- New Trade Button (User Area) -->
                        <a href="{{ route('trades.create') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-500 rounded-xl shadow-sm shadow-indigo-600/30 hover:shadow-indigo-600/40 transition-all duration-150">
                            <i class="fa-solid fa-plus text-xs"></i>
                            <span class="hidden sm:inline">Log Trade</span>
                        </a>

                        <!-- Admin Switch Pill (if user is admin) -->
                        @if(auth()->user()->isAdmin())
                            @if(request()->is('admin*'))
                                <a href="{{ route('trades.index') }}" class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-emerald-50 text-emerald-700 dark:bg-emerald-950/70 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 hover:bg-emerald-100 transition-colors">
                                    <i class="fa-solid fa-arrow-right-arrow-left mr-1"></i> User View
                                </a>
                            @else
                                <a href="{{ route('admin.dashboard') }}" class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-amber-50 text-amber-700 dark:bg-amber-950/70 dark:text-amber-300 border border-amber-200 dark:border-amber-800 hover:bg-amber-100 transition-colors">
                                    <i class="fa-solid fa-shield-halved mr-1"></i> Admin Portal
                                </a>
                            @endif
                        @endif
                    @endauth

                    <!-- Theme Switcher Button -->
                    <button type="button" onclick="toggleTheme()" class="p-2.5 rounded-xl text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-100 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200/80 dark:border-slate-800 transition-colors" title="Toggle Light / Dark theme">
                        <i class="fa-solid fa-sun hidden dark:inline text-amber-400 text-sm"></i>
                        <i class="fa-solid fa-moon dark:hidden text-slate-600 text-sm"></i>
                    </button>

                    @auth
                        <!-- User Profile & Logout Dropdown -->
                        <div class="relative flex items-center gap-2 pl-2 border-l border-slate-200 dark:border-slate-800">
                            <div class="text-right hidden sm:block">
                                <div class="text-sm font-bold text-slate-800 dark:text-slate-200 leading-tight">
                                    {{ auth()->user()->first_name }} {{ auth()->user()->last_name }}
                                </div>
                                <div class="text-[11px] text-slate-400 flex items-center justify-end gap-1">
                                    @if(auth()->user()->isAdmin())
                                        <span class="inline-block px-1.5 py-0.2 bg-purple-100 dark:bg-purple-900/60 text-purple-700 dark:text-purple-300 text-[10px] font-bold rounded">ADMIN</span>
                                    @else
                                        <span>{{ auth()->user()->trading_experience }} yrs exp</span>
                                    @endif
                                </div>
                            </div>

                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="p-2.5 rounded-xl text-slate-400 hover:text-rose-600 dark:text-slate-400 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 border border-transparent hover:border-rose-200 dark:hover:border-rose-900/50 transition-colors" title="Log Out">
                                    <i class="fa-solid fa-arrow-right-from-bracket text-sm"></i>
                                </button>
                            </form>
                        </div>
                    @else
                        <!-- Guest Auth Links -->
                        <div class="flex items-center gap-2">
                            <a href="{{ route('login') }}" class="px-3.5 py-2 text-sm font-semibold text-slate-700 dark:text-slate-200 hover:text-indigo-600 dark:hover:text-indigo-400 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                                Log In
                            </a>
                            <a href="{{ route('register') }}" class="px-4 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-500 rounded-xl shadow-sm transition-colors">
                                Sign Up
                            </a>
                        </div>
                    @endauth

                </div>
            </div>
        </div>

        <!-- Sub-Nav for Admin Area -->
        @auth
        @if(auth()->user()->isAdmin() && request()->is('admin*'))
        <div class="bg-slate-100/90 dark:bg-slate-900/90 border-t border-slate-200 dark:border-slate-800 px-4 sm:px-6 lg:px-8 py-2">
            <div class="max-w-7xl mx-auto flex items-center justify-between text-xs font-semibold">
                <div class="flex items-center gap-1 sm:gap-2">
                    <span class="text-slate-400 uppercase tracking-wider mr-2 hidden sm:inline">Admin:</span>
                    <a href="{{ route('admin.dashboard') }}" class="px-3 py-1.5 rounded-lg {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600 text-white' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-800' }}">
                        <i class="fa-solid fa-gauge mr-1"></i> Overview
                    </a>
                    <a href="{{ route('admin.users') }}" class="px-3 py-1.5 rounded-lg {{ request()->routeIs('admin.users*') ? 'bg-indigo-600 text-white' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-800' }}">
                        <i class="fa-solid fa-users mr-1"></i> Manage Users
                    </a>
                    <a href="{{ route('admin.trades') }}" class="px-3 py-1.5 rounded-lg {{ request()->routeIs('admin.trades*') ? 'bg-indigo-600 text-white' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-800' }}">
                        <i class="fa-solid fa-list-check mr-1"></i> All Platform Trades
                    </a>
                </div>
                <div class="text-slate-500 text-[11px] hidden md:block">
                    Super Admin Console
                </div>
            </div>
        </div>
        @endif
        @endauth
    </header>

    <!-- Main Content Area -->
    <main class="flex-1 py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto w-full">

        <!-- Flash Notifications -->
        @if(session('success'))
            <div class="mb-6 p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800/80 text-emerald-800 dark:text-emerald-200 flex items-start gap-3 shadow-sm">
                <i class="fa-solid fa-circle-check text-emerald-500 text-lg mt-0.5"></i>
                <div class="flex-1 text-sm font-medium leading-relaxed">{{ session('success') }}</div>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-emerald-600">&times;</button>
            </div>
        @endif

        @if(session('warning'))
            <div class="mb-6 p-4 rounded-xl bg-amber-50 dark:bg-amber-950/60 border border-amber-200 dark:border-amber-800/80 text-amber-800 dark:text-amber-200 flex items-start gap-3 shadow-sm">
                <i class="fa-solid fa-circle-exclamation text-amber-500 text-lg mt-0.5"></i>
                <div class="flex-1 text-sm font-medium leading-relaxed">{{ session('warning') }}</div>
                <button type="button" onclick="this.parentElement.remove()" class="text-amber-400 hover:text-amber-600">&times;</button>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 rounded-xl bg-rose-50 dark:bg-rose-950/60 border border-rose-200 dark:border-rose-800/80 text-rose-800 dark:text-rose-200 flex items-start gap-3 shadow-sm">
                <i class="fa-solid fa-circle-xmark text-rose-500 text-lg mt-0.5"></i>
                <div class="flex-1 text-sm font-medium leading-relaxed">{{ session('error') }}</div>
                <button type="button" onclick="this.parentElement.remove()" class="text-rose-400 hover:text-rose-600">&times;</button>
            </div>
        @endif

        @if(session('status'))
            <div class="mb-6 p-4 rounded-xl bg-blue-50 dark:bg-blue-950/60 border border-blue-200 dark:border-blue-800/80 text-blue-800 dark:text-blue-200 flex items-start gap-3 shadow-sm">
                <i class="fa-solid fa-circle-info text-blue-500 text-lg mt-0.5"></i>
                <div class="flex-1 text-sm font-medium leading-relaxed">{{ session('status') }}</div>
                <button type="button" onclick="this.parentElement.remove()" class="text-blue-400 hover:text-blue-600">&times;</button>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="mt-auto border-t border-slate-200 dark:border-slate-800/80 bg-white dark:bg-slate-900 py-6 text-center text-xs text-slate-500 dark:text-slate-400">
        <div class="max-w-7xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-3">
            <div class="flex items-center gap-2 font-medium">
                <span>&copy; {{ date('Y') }} <strong>MyTrades</strong>. Built for disciplined traders.</span>
            </div>
            <div class="flex items-center gap-4 text-slate-400">
                <span>Theme: <button type="button" onclick="toggleTheme()" class="underline hover:text-indigo-500">Toggle Mode</button></span>
                <span>•</span>
                <span>Simple & Powerful Trading Journal</span>
            </div>
        </div>
    </footer>

    <!-- Password visibility toggle script -->
    <script>
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            if (!input) return;

            if (input.type === 'password') {
                input.type = 'text';
                if (icon) {
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                }
            } else {
                input.type = 'password';
                if (icon) {
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            }
        }
    </script>

    @stack('scripts')
</body>
</html>
