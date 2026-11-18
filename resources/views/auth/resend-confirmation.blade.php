@extends('layouts.app')

@section('title', 'Resend Email Confirmation - MyTrades')

@section('content')
<div class="max-w-md mx-auto my-8">
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-8 sm:p-10 shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-200/80 dark:border-slate-800">
        
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="w-14 h-14 mx-auto mb-4 rounded-2xl bg-indigo-50 dark:bg-indigo-950/60 border border-indigo-200 dark:border-indigo-800 flex items-center justify-center text-indigo-600 dark:text-indigo-400 text-2xl">
                <i class="fa-solid fa-paper-plane"></i>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                Resend Confirmation Email
            </h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm mt-1.5 leading-relaxed">
                Enter your registered email address and we'll dispatch a fresh welcome and verification link.
            </p>
        </div>

        @if(session('status'))
            <div class="mb-6 p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 text-xs font-semibold flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-emerald-500 text-sm"></i>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @if(session('dev_confirm_url'))
            <div class="mb-6 p-4 rounded-2xl bg-blue-50 dark:bg-blue-950/70 border border-blue-200 dark:border-blue-800 text-blue-900 dark:text-blue-200 text-xs">
                <span class="font-bold bg-blue-200 dark:bg-blue-800 px-2 py-0.5 rounded text-[10px] mr-1 uppercase">Dev Simulator</span>
                <a href="{{ session('dev_confirm_url') }}" class="underline font-bold text-blue-700 dark:text-blue-300">
                    Click here to simulate confirming email &rarr;
                </a>
            </div>
        @endif

        <form method="POST" action="{{ route('verification.resend') }}" class="space-y-6">
            @csrf

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
                           value="{{ old('email', request('email')) }}" 
                           required 
                           autofocus
                           placeholder="trader@example.com"
                           class="w-full pl-11 pr-4 py-3.5 text-base rounded-xl border @error('email') border-rose-500 ring-1 ring-rose-500 @else border-slate-300 dark:border-slate-700 @enderror bg-white dark:bg-slate-800/80 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                @error('email')
                    <p class="mt-2 text-xs font-semibold text-rose-500 flex items-center gap-1">
                        <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            <button type="submit" class="w-full py-3.5 px-4 rounded-xl text-base font-bold text-white bg-indigo-600 hover:bg-indigo-500 transition-all shadow-lg shadow-indigo-600/30">
                Send Confirmation Email &rarr;
            </button>
        </form>

        <div class="mt-8 pt-6 border-t border-slate-100 dark:border-slate-800 text-center">
            <a href="{{ route('login') }}" class="text-xs font-bold text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200">
                &larr; Return to Sign In
            </a>
        </div>
    </div>
</div>
@endsection
