@extends('layouts.app')

@section('title', 'Sign Up - MyTrades Trading Journal')

@section('content')
<div class="max-w-2xl mx-auto my-8">

    <!-- Admin Approval Notice Banner -->
    <div class="mb-6 p-5 rounded-2xl bg-indigo-50 dark:bg-indigo-950/60 border border-indigo-200 dark:border-indigo-800 text-indigo-950 dark:text-indigo-200 shadow-sm flex items-start gap-4">
        <div class="p-2.5 bg-indigo-100 dark:bg-indigo-900 rounded-xl text-indigo-600 dark:text-indigo-400">
            <i class="fa-solid fa-user-shield text-xl"></i>
        </div>
        <div>
            <h3 class="font-bold text-base text-indigo-900 dark:text-indigo-200">Admin Approval Required</h3>
            <p class="text-xs sm:text-sm text-indigo-800/80 dark:text-indigo-300/80 mt-1 leading-relaxed">
                To maintain a high quality community for serious traders, every new account goes through a quick review and approval by our Super Admin before access is granted.
            </p>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-3xl p-8 sm:p-12 shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-200/80 dark:border-slate-800">
        
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="mb-4">
                <img src="{{ asset('images/logo.webp') }}" alt="MyTrades" class="h-12 sm:h-14 w-auto mx-auto dark:hidden">
                <img src="{{ asset('images/logo-dark.webp') }}" alt="MyTrades" class="h-12 sm:h-14 w-auto mx-auto hidden dark:block">
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                Create Your Trading Journal
            </h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm mt-1.5">
                Track your trades, master your psychology, and elevate your edge
            </p>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('register') }}" class="space-y-6">
            @csrf

            <!-- Name Fields (First and Last Name) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="first_name" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                        First Name <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-regular fa-user"></i>
                        </div>
                        <input type="text" 
                               id="first_name" 
                               name="first_name" 
                               value="{{ old('first_name') }}" 
                               required 
                               placeholder="e.g. Rahul"
                               class="w-full pl-11 pr-4 py-3.5 text-base rounded-xl border @error('first_name') border-rose-500 ring-1 ring-rose-500 @else border-slate-300 dark:border-slate-700 @enderror bg-white dark:bg-slate-800/80 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all">
                    </div>
                    @error('first_name')
                        <p class="mt-1.5 text-xs font-semibold text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="last_name" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                        Last Name <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           id="last_name" 
                           name="last_name" 
                           value="{{ old('last_name') }}" 
                           required 
                           placeholder="e.g. Sharma"
                           class="w-full px-4 py-3.5 text-base rounded-xl border @error('last_name') border-rose-500 ring-1 ring-rose-500 @else border-slate-300 dark:border-slate-700 @enderror bg-white dark:bg-slate-800/80 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all">
                    @error('last_name')
                        <p class="mt-1.5 text-xs font-semibold text-rose-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Email Address -->
            <div>
                <label for="email" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                    Email Address <span class="text-rose-500">*</span>
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
                           placeholder="rahul.trader@example.com"
                           class="w-full pl-11 pr-4 py-3.5 text-base rounded-xl border @error('email') border-rose-500 ring-1 ring-rose-500 @else border-slate-300 dark:border-slate-700 @enderror bg-white dark:bg-slate-800/80 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all">
                </div>
                @error('email')
                    <p class="mt-1.5 text-xs font-semibold text-rose-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Indian Phone Number & Trading Experience -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="phone" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                        Phone Number (Indian) <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <span class="text-xs font-bold px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200">
                                🇮🇳 +91
                            </span>
                        </div>
                        <input type="tel" 
                               id="phone" 
                               name="phone" 
                               value="{{ old('phone') }}" 
                               required 
                               placeholder="9876543210"
                               class="w-full pl-20 pr-4 py-3.5 text-base rounded-xl border @error('phone') border-rose-500 ring-1 ring-rose-500 @else border-slate-300 dark:border-slate-700 @enderror bg-white dark:bg-slate-800/80 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all">
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1">10-digit mobile number</p>
                    @error('phone')
                        <p class="mt-1.5 text-xs font-semibold text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="trading_experience" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                        Experience in Trading <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <select id="trading_experience" 
                                name="trading_experience" 
                                required
                                class="w-full px-4 py-3.5 text-base rounded-xl border @error('trading_experience') border-rose-500 ring-1 ring-rose-500 @else border-slate-300 dark:border-slate-700 @enderror bg-white dark:bg-slate-800/80 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all">
                            <option value="" disabled {{ old('trading_experience') === null ? 'selected' : '' }}>Select years of experience...</option>
                            <option value="0" {{ old('trading_experience') === '0' ? 'selected' : '' }}>Beginner (< 1 year)</option>
                            <option value="1" {{ old('trading_experience') === '1' ? 'selected' : '' }}>1 Year</option>
                            <option value="2" {{ old('trading_experience') === '2' ? 'selected' : '' }}>2 Years</option>
                            <option value="3" {{ old('trading_experience') === '3' ? 'selected' : '' }}>3 Years</option>
                            <option value="4" {{ old('trading_experience') === '4' ? 'selected' : '' }}>4 Years</option>
                            <option value="5" {{ old('trading_experience') === '5' ? 'selected' : '' }}>5 Years</option>
                            <option value="7" {{ old('trading_experience') === '7' ? 'selected' : '' }}>6 - 8 Years</option>
                            <option value="10" {{ old('trading_experience') === '10' ? 'selected' : '' }}>9 - 12 Years</option>
                            <option value="15" {{ old('trading_experience') === '15' ? 'selected' : '' }}>12+ Years (Veteran)</option>
                        </select>
                    </div>
                    @error('trading_experience')
                        <p class="mt-1.5 text-xs font-semibold text-rose-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Password with Eye Toggle -->
            <div>
                <label for="reg_password" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                    Password <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-lock"></i>
                    </div>
                    <input type="password" 
                           id="reg_password" 
                           name="password" 
                           required 
                           autocomplete="new-password"
                           placeholder="At least 8 characters"
                           class="w-full pl-11 pr-12 py-3.5 text-base rounded-xl border @error('password') border-rose-500 ring-1 ring-rose-500 @else border-slate-300 dark:border-slate-700 @enderror bg-white dark:bg-slate-800/80 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all">
                    
                    <!-- Eye Icon -->
                    <button type="button" 
                            onclick="togglePassword('reg_password', 'reg_eye_icon')" 
                            class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 focus:outline-none transition-colors"
                            title="Toggle Password Visibility">
                        <i id="reg_eye_icon" class="fa-regular fa-eye text-base"></i>
                    </button>
                </div>
                @error('password')
                    <p class="mt-1.5 text-xs font-semibold text-rose-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Confirm Password with Eye Toggle -->
            <div>
                <label for="reg_password_confirmation" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                    Confirm Password <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-check-double"></i>
                    </div>
                    <input type="password" 
                           id="reg_password_confirmation" 
                           name="password_confirmation" 
                           required 
                           autocomplete="new-password"
                           placeholder="Re-type password"
                           class="w-full pl-11 pr-12 py-3.5 text-base rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800/80 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all">
                    
                    <!-- Eye Icon -->
                    <button type="button" 
                            onclick="togglePassword('reg_password_confirmation', 'reg_eye_confirm_icon')" 
                            class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 focus:outline-none transition-colors"
                            title="Toggle Password Visibility">
                        <i id="reg_eye_confirm_icon" class="fa-regular fa-eye text-base"></i>
                    </button>
                </div>
            </div>

            <!-- Agree to Terms Checkbox -->
            <div class="pt-2">
                <label class="flex items-start gap-3 cursor-pointer select-none">
                    <input type="checkbox" 
                           name="agreed_to_terms" 
                           value="1" 
                           {{ old('agreed_to_terms') ? 'checked' : '' }}
                           required
                           class="mt-1 w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 dark:bg-slate-800 border-slate-300 dark:border-slate-700">
                    <span class="text-sm text-slate-600 dark:text-slate-400 leading-normal">
                        I agree to the <span class="font-semibold text-indigo-600 dark:text-indigo-400 underline cursor-pointer">Terms of Service</span> and <span class="font-semibold text-indigo-600 dark:text-indigo-400 underline cursor-pointer">Trading Journal Privacy Policy</span>. I understand that account activation requires admin approval.
                    </span>
                </label>
                @error('agreed_to_terms')
                    <p class="mt-1.5 text-xs font-semibold text-rose-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <button type="submit" class="w-full py-4 px-6 text-base font-bold text-white bg-indigo-600 hover:bg-indigo-500 rounded-xl shadow-lg shadow-indigo-600/30 hover:shadow-indigo-600/40 transform active:scale-[0.99] transition-all flex items-center justify-center gap-2">
                <span>Submit Registration for Approval</span>
                <i class="fa-solid fa-arrow-right text-sm"></i>
            </button>
        </form>

        <!-- Login Link -->
        <div class="mt-8 pt-6 border-t border-slate-200 dark:border-slate-800 text-center text-sm text-slate-500 dark:text-slate-400">
            Already have an account? 
            <a href="{{ route('login') }}" class="font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
                Sign in here
            </a>
        </div>
    </div>
</div>
@endsection
