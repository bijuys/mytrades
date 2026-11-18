@extends('layouts.app')

@section('title', 'Log In - MyTrades Trading Journal')

@section('content')
<div class="max-w-md mx-auto my-8">
    
    <!-- Pending Approval Banner if redirected after signup -->
    @if(session('pending_notice'))
        <div class="mb-6 p-5 rounded-2xl bg-amber-50 dark:bg-amber-950/70 border-2 border-amber-300 dark:border-amber-700 text-amber-900 dark:text-amber-200 shadow-lg shadow-amber-500/10">
            <div class="flex items-start gap-3">
                <div class="p-2 bg-amber-200 dark:bg-amber-800 rounded-xl text-amber-800 dark:text-amber-100">
                    <i class="fa-solid fa-clock text-xl"></i>
                </div>
                <div>
                    <h3 class="font-bold text-base">{{ session('pending_notice')['title'] }}</h3>
                    <p class="text-sm mt-1 text-amber-800/90 dark:text-amber-200/90 leading-relaxed">
                        {{ session('pending_notice')['message'] }}
                    </p>
                    <div class="mt-3 text-xs font-semibold px-2.5 py-1 bg-amber-100 dark:bg-amber-900/60 rounded-md inline-block">
                        Account: {{ session('pending_notice')['email'] }}
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="bg-white dark:bg-slate-900 rounded-3xl p-8 sm:p-10 shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-200/80 dark:border-slate-800">
        
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="mb-4">
                <img src="{{ asset('images/logo.webp') }}" alt="MyTrades" class="h-12 sm:h-14 w-auto mx-auto dark:hidden">
                <img src="{{ asset('images/logo-dark.webp') }}" alt="MyTrades" class="h-12 sm:h-14 w-auto mx-auto hidden dark:block">
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                Welcome Back
            </h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm mt-1.5">
                Sign in to your trading journal & analytics
            </p>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf

            <!-- Email Address -->
            <div>
                <label for="email" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                    Email Address
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-regular fa-envelope"></i>
                    </div>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           value="{{ old('email') }}" 
                           required 
                           autocomplete="email" 
                           autofocus
                           placeholder="trader@example.com"
                           class="w-full pl-11 pr-4 py-3.5 text-base rounded-xl border @error('email') border-rose-500 ring-1 ring-rose-500 @else border-slate-300 dark:border-slate-700 @enderror bg-white dark:bg-slate-800/80 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all">
                </div>
                @error('email')
                    <p class="mt-2 text-xs font-semibold text-rose-500 flex items-center gap-1">
                        <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Password with Eye Toggle -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label for="password" class="block text-sm font-semibold text-slate-700 dark:text-slate-300">
                        Password
                    </label>
                    <a href="{{ route('password.request') }}" class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                        Forgot password?
                    </a>
                </div>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-lock"></i>
                    </div>
                    <input type="password" 
                           id="login_password" 
                           name="password" 
                           required 
                           autocomplete="current-password"
                           placeholder="••••••••"
                           class="w-full pl-11 pr-12 py-3.5 text-base rounded-xl border @error('password') border-rose-500 ring-1 ring-rose-500 @else border-slate-300 dark:border-slate-700 @enderror bg-white dark:bg-slate-800/80 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all">
                    
                    <!-- Eye Icon to toggle password visibility -->
                    <button type="button" 
                            onclick="togglePassword('login_password', 'login_eye_icon')" 
                            class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 focus:outline-none transition-colors"
                            title="Show/Hide Password">
                        <i id="login_eye_icon" class="fa-regular fa-eye text-base"></i>
                    </button>
                </div>
                @error('password')
                    <p class="mt-2 text-xs font-semibold text-rose-500 flex items-center gap-1">
                        <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Remember Me -->
            <div class="flex items-center justify-between pt-1">
                <label class="flex items-center cursor-pointer select-none">
                    <input type="checkbox" name="remember" class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 dark:bg-slate-800 border-slate-300 dark:border-slate-700">
                    <span class="ml-2.5 text-sm text-slate-600 dark:text-slate-400 font-medium">Keep me signed in</span>
                </label>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="w-full py-4 px-6 text-base font-bold text-white bg-indigo-600 hover:bg-indigo-500 rounded-xl shadow-lg shadow-indigo-600/30 hover:shadow-indigo-600/40 transform active:scale-[0.99] transition-all flex items-center justify-center gap-2">
                <span>Sign In to Journal</span>
                <i class="fa-solid fa-arrow-right text-sm"></i>
            </button>
        </form>

        <!-- Signup Link -->
        <div class="mt-8 pt-6 border-t border-slate-200 dark:border-slate-800 text-center text-sm text-slate-500 dark:text-slate-400">
            Don't have an account yet? 
            <a href="{{ route('register') }}" class="font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
                Create one now
            </a>
        </div>
    </div>

    <!-- Demo Credentials Helper Card -->
    <div class="mt-6 p-4 rounded-2xl bg-slate-100/80 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 text-xs text-slate-600 dark:text-slate-400">
        <div class="flex items-center gap-1.5 font-bold text-slate-700 dark:text-slate-300 mb-2">
            <i class="fa-solid fa-key text-indigo-500"></i> Seeded Demo Accounts:
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-[11px]">
            <div class="p-2 bg-white dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700">
                <span class="font-bold text-purple-600 dark:text-purple-400">Super Admin:</span><br>
                Email: <code class="font-mono text-slate-800 dark:text-slate-200">admin@mytrades.com</code><br>
                Password: <code class="font-mono text-slate-800 dark:text-slate-200">password123</code>
            </div>
            <div class="p-2 bg-white dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700">
                <span class="font-bold text-emerald-600 dark:text-emerald-400">Approved Trader:</span><br>
                Email: <code class="font-mono text-slate-800 dark:text-slate-200">trader@mytrades.com</code><br>
                Password: <code class="font-mono text-slate-800 dark:text-slate-200">password123</code>
            </div>
        </div>
    </div>

</div>
@endsection
