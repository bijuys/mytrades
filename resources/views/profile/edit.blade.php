@extends('layouts.app')

@section('title', 'Profile & Account Settings - MyTrades')

@section('content')
<div class="max-w-4xl mx-auto space-y-8">

    <!-- Page Header & Breadcrumb -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-200/80 dark:border-slate-800">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-400 mb-1">
                <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('trades.index') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400">Home</a>
                <i class="fa-solid fa-chevron-right text-[10px]"></i>
                <span class="text-slate-600 dark:text-slate-300">Profile & Settings</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight flex items-center gap-3">
                <i class="fa-solid fa-user-gear text-indigo-600 dark:text-indigo-400"></i>
                <span>Account Profile</span>
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                Manage your personal details, custom avatar, security password, and account preferences.
            </p>
        </div>

        <div class="flex items-center gap-2">
            @if($user->isAdmin())
                <span class="px-3 py-1 text-xs font-bold rounded-lg bg-purple-100 dark:bg-purple-900/60 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800">
                    <i class="fa-solid fa-shield-halved mr-1"></i> Super Admin
                </span>
            @else
                <span class="px-3 py-1 text-xs font-bold rounded-lg bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                    <i class="fa-solid fa-circle-check mr-1"></i> Active Trader
                </span>
            @endif
        </div>
    </div>

    <!-- Flash Notifications -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800/80 flex items-center gap-3 text-emerald-800 dark:text-emerald-300">
            <i class="fa-solid fa-circle-check text-lg"></i>
            <span class="text-sm font-semibold">{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/60 border border-rose-200 dark:border-rose-800/80 text-rose-800 dark:text-rose-300">
            <div class="flex items-center gap-2 font-bold text-sm mb-1">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <span>Please fix the following issues:</span>
            </div>
            <ul class="list-disc list-inside text-xs space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- 1. Profile Information & Custom Avatar -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200/80 dark:border-slate-800">
        <div class="flex items-center gap-3 pb-4 mb-6 border-b border-slate-100 dark:border-slate-800">
            <span class="w-9 h-9 rounded-xl bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-sm">
                <i class="fa-solid fa-id-card"></i>
            </span>
            <div>
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">Personal Information & Profile Picture</h2>
                <p class="text-xs text-slate-400">Update your name, contact details, and optional profile image.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PATCH')

            <!-- Avatar Upload & Fallback Initials Section -->
            <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/80 flex flex-col sm:flex-row items-center sm:items-start gap-6">
                <!-- Current Avatar or Fallback Initials Preview -->
                <div class="relative shrink-0 text-center">
                    <div id="avatarContainer" class="w-24 h-24 rounded-full overflow-hidden border-2 border-indigo-500 dark:border-indigo-400 shadow-md mx-auto flex items-center justify-center bg-gradient-to-br from-indigo-500 to-purple-600 text-white font-black text-2xl uppercase tracking-wider">
                        @if($user->avatar_url)
                            <img id="avatarImg" src="{{ $user->avatar_url }}" alt="{{ $user->full_name }}" class="w-full h-full object-cover">
                        @else
                            <span id="avatarInitialsText">{{ $user->initials }}</span>
                        @endif
                    </div>
                    <span class="block mt-2 text-[11px] font-semibold text-slate-400">
                        {{ $user->avatar ? 'Custom Photo' : 'Initials Avatar' }}
                    </span>
                </div>

                <!-- Avatar Actions -->
                <div class="flex-1 space-y-3 text-center sm:text-left">
                    <div>
                        <label class="block text-sm font-bold text-slate-800 dark:text-slate-200 mb-1">
                            Profile Picture (Optional)
                        </label>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            Upload a JPG, PNG, or WebP picture (max 5 MB). If no photo is provided, your First & Last Name initials (<strong>{{ $user->initials }}</strong>) will be used automatically.
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        <label for="avatar" class="cursor-pointer px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold shadow-sm transition-all inline-flex items-center gap-2">
                            <i class="fa-solid fa-camera"></i>
                            <span>{{ $user->avatar ? 'Change Picture' : 'Upload Picture' }}</span>
                        </label>
                        <input type="file" id="avatar" name="avatar" accept="image/*" class="hidden" onchange="previewAvatar(this)">

                        <button type="button" id="clearNewAvatarBtn" onclick="clearAvatarSelection()" class="hidden px-3.5 py-2.5 rounded-xl bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 text-slate-700 dark:text-slate-200 text-xs font-bold transition-colors">
                            Cancel Selection
                        </button>

                        @if($user->avatar)
                            <label class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-900 text-xs font-bold cursor-pointer hover:bg-rose-100 dark:hover:bg-rose-950/70 transition-colors">
                                <input type="checkbox" name="remove_avatar" value="1" class="rounded text-rose-600 focus:ring-rose-500">
                                <span>Remove & Use Initials</span>
                            </label>
                        @endif
                    </div>

                    <p id="avatarFileName" class="text-xs font-mono text-indigo-600 dark:text-indigo-400 font-semibold hidden"></p>
                </div>
            </div>

            <!-- Name Fields (2 Columns) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="first_name" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">
                        First Name <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           id="first_name" 
                           name="first_name" 
                           value="{{ old('first_name', $user->first_name) }}" 
                           required 
                           class="w-full px-4 py-3 text-base rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                </div>

                <div>
                    <label for="last_name" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">
                        Last Name <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           id="last_name" 
                           name="last_name" 
                           value="{{ old('last_name', $user->last_name) }}" 
                           required 
                           class="w-full px-4 py-3 text-base rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                </div>
            </div>

            <!-- Email & Phone (2 Columns) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="email" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">
                        Email Address <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-regular fa-envelope"></i>
                        </div>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               value="{{ old('email', $user->email) }}" 
                               required 
                               class="w-full pl-10 pr-4 py-3 text-base rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                    </div>
                </div>

                <div>
                    <label for="phone" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">
                        Phone Number (Indian) <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-phone"></i>
                        </div>
                        <input type="text" 
                               id="phone" 
                               name="phone" 
                               value="{{ old('phone', $user->phone) }}" 
                               required 
                               placeholder="9876543210" 
                               class="w-full pl-10 pr-4 py-3 text-base rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                    </div>
                </div>
            </div>

            <!-- Trading Experience -->
            <div>
                <label for="trading_experience" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">
                    Trading Experience (Years) <span class="text-rose-500">*</span>
                </label>
                <div class="relative max-w-xs">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-chart-line"></i>
                    </div>
                    <input type="number" 
                           id="trading_experience" 
                           name="trading_experience" 
                           min="0" 
                           max="60" 
                           value="{{ old('trading_experience', $user->trading_experience) }}" 
                           required 
                           class="w-full pl-10 pr-4 py-3 text-base rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                </div>
            </div>

            <div class="flex justify-end pt-3">
                <button type="submit" class="px-6 py-3.5 text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-500 rounded-xl shadow-md shadow-indigo-600/30 hover:shadow-indigo-600/40 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Save Profile Changes</span>
                </button>
            </div>
        </form>
    </div>

    <!-- 2. Security & Password Modification -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200/80 dark:border-slate-800">
        <div class="flex items-center gap-3 pb-4 mb-6 border-b border-slate-100 dark:border-slate-800">
            <span class="w-9 h-9 rounded-xl bg-amber-50 dark:bg-amber-950 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold text-sm">
                <i class="fa-solid fa-lock"></i>
            </span>
            <div>
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">Security & Password</h2>
                <p class="text-xs text-slate-400">Ensure your account uses a strong password with at least 8 characters.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('profile.password') }}" class="space-y-5">
            @csrf
            @method('PUT')

            <!-- Current Password -->
            <div>
                <label for="current_password" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">
                    Current Password <span class="text-rose-500">*</span>
                </label>
                <div class="relative max-w-md">
                    <input type="password" 
                           id="current_password" 
                           name="current_password" 
                           required 
                           placeholder="••••••••" 
                           class="w-full pl-4 pr-11 py-3 text-base rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                    <button type="button" onclick="togglePasswordVisibility('current_password', 'current_pw_eye')" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600">
                        <i id="current_pw_eye" class="fa-regular fa-eye"></i>
                    </button>
                </div>
                @error('current_password')
                    <p class="mt-1.5 text-xs font-semibold text-rose-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- New Password & Confirmation -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="password" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">
                        New Password <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="password" 
                               id="password" 
                               name="password" 
                               required 
                               placeholder="Min. 8 characters" 
                               class="w-full pl-4 pr-11 py-3 text-base rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                        <button type="button" onclick="togglePasswordVisibility('password', 'new_pw_eye')" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600">
                            <i id="new_pw_eye" class="fa-regular fa-eye"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1.5 text-xs font-semibold text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">
                        Confirm New Password <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="password" 
                               id="password_confirmation" 
                               name="password_confirmation" 
                               required 
                               placeholder="Repeat new password" 
                               class="w-full pl-4 pr-11 py-3 text-base rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                        <button type="button" onclick="togglePasswordVisibility('password_confirmation', 'confirm_pw_eye')" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600">
                            <i id="confirm_pw_eye" class="fa-regular fa-eye"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-3">
                <button type="submit" class="px-6 py-3.5 text-sm font-bold text-white bg-slate-900 dark:bg-indigo-600 hover:bg-slate-800 dark:hover:bg-indigo-500 rounded-xl shadow-md transition-all flex items-center gap-2">
                    <i class="fa-solid fa-key"></i>
                    <span>Update Password</span>
                </button>
            </div>
        </form>
    </div>

    <!-- 3. Danger Zone: Close Account -->
    <div class="bg-rose-50/50 dark:bg-rose-950/20 rounded-3xl p-6 sm:p-8 border-2 border-dashed border-rose-200 dark:border-rose-900/60 shadow-sm">
        <div class="flex items-center gap-3 pb-4 mb-4 border-b border-rose-200/80 dark:border-rose-900/60">
            <span class="w-9 h-9 rounded-xl bg-rose-100 dark:bg-rose-950 text-rose-600 dark:text-rose-400 flex items-center justify-center font-bold text-sm">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </span>
            <div>
                <h2 class="text-lg font-bold text-rose-900 dark:text-rose-300">Close Account</h2>
                <p class="text-xs text-rose-700/80 dark:text-rose-400/80">Permanently delete your account and all associated trading data.</p>
            </div>
        </div>

        <p class="text-sm text-slate-600 dark:text-slate-300 mb-6 leading-relaxed">
            Once your account is closed, all of your trading journal records, execution history, uploaded chart screenshots, and custom strategies will be permanently deleted from the database and Amazon S3 storage. <strong>This action cannot be undone.</strong>
        </p>

        <div class="flex items-center justify-start">
            <button type="button" onclick="openCloseAccountModal()" class="px-5 py-3 rounded-xl bg-rose-600 hover:bg-rose-500 text-white text-sm font-bold shadow-md shadow-rose-600/30 transition-all flex items-center gap-2">
                <i class="fa-solid fa-user-xmark"></i>
                <span>Close My Account Permanently</span>
            </button>
        </div>
    </div>

</div>

<!-- Confirmation Modal for Closing Account -->
<div id="closeAccountModal" class="hidden fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 max-w-lg w-full border border-slate-200 dark:border-slate-800 shadow-2xl space-y-6">
        
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 rounded-2xl bg-rose-100 dark:bg-rose-950 text-rose-600 dark:text-rose-400 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div>
                <h3 class="text-xl font-extrabold text-slate-900 dark:text-white">Are you absolutely sure?</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    This will permanently delete your account, journals, and chart images.
                </p>
            </div>
        </div>

        <form method="POST" action="{{ route('profile.destroy') }}" class="space-y-4">
            @csrf
            @method('DELETE')

            <div>
                <label for="close_password" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                    Enter your current password to confirm closure:
                </label>
                <div class="relative">
                    <input type="password" 
                           id="close_password" 
                           name="password" 
                           required 
                           placeholder="Your current password" 
                           class="w-full pl-4 pr-11 py-3 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-rose-500 transition-all">
                    <button type="button" onclick="togglePasswordVisibility('close_password', 'close_pw_eye')" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600">
                        <i id="close_pw_eye" class="fa-regular fa-eye"></i>
                    </button>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" onclick="closeCloseAccountModal()" class="px-5 py-2.5 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-rose-600 hover:bg-rose-500 shadow-md shadow-rose-600/30 transition-all">
                    Permanently Delete Everything
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function togglePasswordVisibility(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    function previewAvatar(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const reader = new FileReader();

            reader.onload = function(e) {
                const container = document.getElementById('avatarContainer');
                container.innerHTML = `<img id="avatarImg" src="${e.target.result}" class="w-full h-full object-cover">`;
                
                document.getElementById('avatarFileName').textContent = `Selected: ${file.name} (${(file.size / 1024).toFixed(1)} KB)`;
                document.getElementById('avatarFileName').classList.remove('hidden');
                document.getElementById('clearNewAvatarBtn').classList.remove('hidden');
            };

            reader.readAsDataURL(file);
        }
    }

    function clearAvatarSelection() {
        const input = document.getElementById('avatar');
        input.value = '';
        document.getElementById('avatarFileName').classList.add('hidden');
        document.getElementById('clearNewAvatarBtn').classList.add('hidden');

        // Revert preview back to original server state
        const container = document.getElementById('avatarContainer');
        @if($user->avatar_url)
            container.innerHTML = `<img id="avatarImg" src="{{ $user->avatar_url }}" alt="{{ $user->full_name }}" class="w-full h-full object-cover">`;
        @else
            container.innerHTML = `<span id="avatarInitialsText">{{ $user->initials }}</span>`;
        @endif
    }

    function openCloseAccountModal() {
        document.getElementById('closeAccountModal').classList.remove('hidden');
        document.getElementById('close_password').focus();
    }

    function closeCloseAccountModal() {
        document.getElementById('closeAccountModal').classList.add('hidden');
    }
</script>
@endpush
@endsection
