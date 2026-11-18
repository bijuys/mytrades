@extends('layouts.app')

@section('title', 'Forgot Password - MyTrades Trading Journal')

@section('content')
<div class="max-w-md mx-auto my-12">
    
    <!-- Dev Helper Banner if in local mode showing direct link -->
    @if(session('dev_reset_url'))
        <div class="mb-6 p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/70 border border-emerald-300 dark:border-emerald-700 text-emerald-900 dark:text-emerald-200 shadow-md">
            <div class="flex items-start gap-3">
                <i class="fa-solid fa-bolt text-emerald-500 text-lg mt-0.5"></i>
                <div class="text-xs">
                    <span class="font-bold">Instant Local Test Link:</span><br>
                    In local development, you can click this link directly to set your new password without opening your mail client:<br>
                    <a href="{{ session('dev_reset_url') }}" class="mt-2 inline-block font-mono bg-emerald-100 dark:bg-emerald-900/80 px-2 py-1 rounded text-emerald-800 dark:text-emerald-100 underline break-all font-semibold">
                        Click here to set new password &rarr;
                    </a>
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
                Reset Password
            </h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm mt-1.5">
                Enter your registered email and we'll send you a secure link to reset your password.
            </p>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
            @csrf

            <!-- Email Address -->
            <div>
                <label for="email" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                    Registered Email Address
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
                           autofocus
                           placeholder="trader@example.com"
                           class="w-full pl-11 pr-4 py-3.5 text-base rounded-xl border @error('email') border-rose-500 ring-1 ring-rose-500 @else border-slate-300 dark:border-slate-700 @enderror bg-white dark:bg-slate-800/80 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all">
                </div>
                @error('email')
                    <p class="mt-2 text-xs font-semibold text-rose-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <button type="submit" class="w-full py-4 px-6 text-base font-bold text-white bg-indigo-600 hover:bg-indigo-500 rounded-xl shadow-lg shadow-indigo-600/30 hover:shadow-indigo-600/40 transform active:scale-[0.99] transition-all flex items-center justify-center gap-2">
                <i class="fa-regular fa-paper-plane text-sm"></i>
                <span>Send Password Reset Link</span>
            </button>
        </form>

        <!-- Back to Login -->
        <div class="mt-8 pt-6 border-t border-slate-200 dark:border-slate-800 text-center text-sm text-slate-500 dark:text-slate-400">
            Remembered your password? 
            <a href="{{ route('login') }}" class="font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
                Back to Sign In
            </a>
        </div>
    </div>
</div>
@endsection
