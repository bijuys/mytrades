@extends('layouts.app')

@section('title', 'Thank You - Password Reset Complete - MyTrades')

@section('content')
<div class="max-w-md mx-auto my-12">
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-8 sm:p-12 shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-200/80 dark:border-slate-800 text-center">
        
        <div class="mb-6">
            <img src="{{ asset('images/logo.webp') }}" alt="MyTrades" class="h-12 sm:h-14 w-auto mx-auto dark:hidden">
            <img src="{{ asset('images/logo-dark.webp') }}" alt="MyTrades" class="h-12 sm:h-14 w-auto mx-auto hidden dark:block">
        </div>

        <!-- Big Checkmark Circle -->
        <div class="w-16 h-16 rounded-full bg-emerald-100 dark:bg-emerald-950/70 text-emerald-600 dark:text-emerald-400 mx-auto flex items-center justify-center text-3xl shadow-lg shadow-emerald-500/20 mb-4">
            <i class="fa-solid fa-circle-check"></i>
        </div>

        <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
            Thank You!
        </h1>

        <p class="text-slate-600 dark:text-slate-300 text-base mt-3 leading-relaxed">
            Your password has been successfully reset. Your account is now secured with your new password.
        </p>

        <div class="mt-8 p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/60 text-xs text-slate-500 dark:text-slate-400">
            <i class="fa-solid fa-shield-halved text-indigo-500 mr-1.5"></i>
            You can now log in using your updated credentials and get right back to your trading journal.
        </div>

        <div class="mt-8">
            <a href="{{ route('login') }}" class="w-full py-4 px-6 text-base font-bold text-white bg-indigo-600 hover:bg-indigo-500 rounded-xl shadow-lg shadow-indigo-600/30 hover:shadow-indigo-600/40 transition-all flex items-center justify-center gap-2">
                <span>Proceed to Sign In</span>
                <i class="fa-solid fa-arrow-right text-sm"></i>
            </a>
        </div>

    </div>
</div>
@endsection
