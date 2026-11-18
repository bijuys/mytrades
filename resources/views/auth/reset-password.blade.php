@extends('layouts.app')

@section('title', 'Set New Password - MyTrades Trading Journal')

@section('content')
<div class="max-w-md mx-auto my-12">
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-8 sm:p-10 shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-200/80 dark:border-slate-800">
        
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="mb-4">
                <img src="{{ asset('images/logo.webp') }}" alt="MyTrades" class="h-12 sm:h-14 w-auto mx-auto dark:hidden">
                <img src="{{ asset('images/logo-dark.webp') }}" alt="MyTrades" class="h-12 sm:h-14 w-auto mx-auto hidden dark:block">
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                Set New Password
            </h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm mt-1.5">
                Choose a strong new password for <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $email }}</span>
            </p>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('password.update') }}" class="space-y-6">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email }}">

            <!-- New Password with Eye Toggle -->
            <div>
                <label for="new_password" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                    New Password
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-lock"></i>
                    </div>
                    <input type="password" 
                           id="new_password" 
                           name="password" 
                           required 
                           autofocus
                           placeholder="At least 8 characters"
                           class="w-full pl-11 pr-12 py-3.5 text-base rounded-xl border @error('password') border-rose-500 ring-1 ring-rose-500 @else border-slate-300 dark:border-slate-700 @enderror bg-white dark:bg-slate-800/80 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all">
                    
                    <!-- Eye Icon -->
                    <button type="button" 
                            onclick="togglePassword('new_password', 'new_password_eye_icon')" 
                            class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 focus:outline-none transition-colors"
                            title="Show/Hide Password">
                        <i id="new_password_eye_icon" class="fa-regular fa-eye text-base"></i>
                    </button>
                </div>
                @error('password')
                    <p class="mt-2 text-xs font-semibold text-rose-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Confirm New Password with Eye Toggle -->
            <div>
                <label for="new_password_confirmation" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                    Confirm New Password
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-check-double"></i>
                    </div>
                    <input type="password" 
                           id="new_password_confirmation" 
                           name="password_confirmation" 
                           required 
                           placeholder="Re-type new password"
                           class="w-full pl-11 pr-12 py-3.5 text-base rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800/80 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all">
                    
                    <!-- Eye Icon -->
                    <button type="button" 
                            onclick="togglePassword('new_password_confirmation', 'new_password_confirm_eye_icon')" 
                            class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 focus:outline-none transition-colors"
                            title="Show/Hide Password">
                        <i id="new_password_confirm_eye_icon" class="fa-regular fa-eye text-base"></i>
                    </button>
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="w-full py-4 px-6 text-base font-bold text-white bg-indigo-600 hover:bg-indigo-500 rounded-xl shadow-lg shadow-indigo-600/30 hover:shadow-indigo-600/40 transform active:scale-[0.99] transition-all flex items-center justify-center gap-2">
                <span>Save New Password</span>
                <i class="fa-solid fa-check text-sm"></i>
            </button>
        </form>

    </div>
</div>
@endsection
