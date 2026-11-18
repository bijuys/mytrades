@extends('layouts.app')

@section('title', 'Direct User Messaging - Super Admin Console - MyTrades')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-400 mb-1">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-indigo-500">Admin</a>
                <span>/</span>
                <span class="text-indigo-600 dark:text-indigo-400">Messages</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                User Direct Messaging
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                Communicate directly with individual traders regarding accounts, trading logs, or support.
            </p>
        </div>
        @if($selectedUser)
            <div class="px-4 py-2 rounded-2xl bg-indigo-50 dark:bg-indigo-950/60 border border-indigo-200 dark:border-indigo-800 text-indigo-800 dark:text-indigo-300 text-xs font-bold flex items-center gap-2">
                <i class="fa-solid fa-user-tag text-indigo-500"></i>
                <span>Messaging: {{ $selectedUser->full_name }} ({{ $selectedUser->email }})</span>
                <a href="{{ route('admin.messages.index') }}" class="ml-2 text-slate-400 hover:text-slate-600 dark:hover:text-white" title="Clear Filter">
                    <i class="fa-solid fa-xmark"></i>
                </a>
            </div>
        @endif
    </div>

    <!-- Main Grid: Compose Message & Sent History -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Compose Message Card (5 Cols) -->
        <div class="lg:col-span-5 bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200/80 dark:border-slate-800">
            <div class="flex items-center gap-3 pb-4 mb-6 border-b border-slate-100 dark:border-slate-800">
                <div class="w-10 h-10 rounded-2xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-base">
                    <i class="fa-solid fa-paper-plane"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">Send Direct Message</h2>
                    <p class="text-xs text-slate-400">Dispatch message &amp; email notification</p>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.messages.send') }}" class="space-y-4">
                @csrf

                <!-- Recipient Selector -->
                <div>
                    <label for="user_id" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">
                        Recipient User <span class="text-rose-500">*</span>
                    </label>
                    <select id="user_id" 
                            name="user_id" 
                            required 
                            class="w-full px-4 py-3 text-sm rounded-xl border @error('user_id') border-rose-500 @else border-slate-300 dark:border-slate-700 @enderror bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- Choose a user --</option>
                        @foreach($allUsers as $u)
                            <option value="{{ $u->id }}" {{ (string)old('user_id', $selectedUserId) === (string)$u->id ? 'selected' : '' }}>
                                {{ $u->full_name }} ({{ $u->email }}) — {{ ucfirst($u->status) }}
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')
                        <p class="mt-1 text-xs text-rose-500 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Subject -->
                <div>
                    <label for="subject" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">
                        Subject <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           id="subject" 
                           name="subject" 
                           value="{{ old('subject') }}" 
                           required 
                           placeholder="e.g. Question regarding your recent trading log / Account note" 
                           class="w-full px-4 py-3 text-sm rounded-xl border @error('subject') border-rose-500 @else border-slate-300 dark:border-slate-700 @enderror bg-white dark:bg-slate-800 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    @error('subject')
                        <p class="mt-1 text-xs text-rose-500 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Message Body -->
                <div>
                    <label for="message" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">
                        Message Content <span class="text-rose-500">*</span>
                    </label>
                    <textarea id="message" 
                              name="message" 
                              rows="6" 
                              required 
                              placeholder="Write your personal note to this user..." 
                              class="w-full px-4 py-3 text-sm rounded-xl border @error('message') border-rose-500 @else border-slate-300 dark:border-slate-700 @enderror bg-white dark:bg-slate-800 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 leading-relaxed"></textarea>
                    @error('message')
                        <p class="mt-1 text-xs text-rose-500 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email Notification Checkbox -->
                <div class="flex items-center gap-2 pt-1">
                    <input type="checkbox" 
                           id="send_email" 
                           name="send_email" 
                           value="1" 
                           checked 
                           class="rounded text-indigo-600 focus:ring-indigo-500 w-4 h-4">
                    <label for="send_email" class="text-xs font-medium text-slate-600 dark:text-slate-300 cursor-pointer">
                        Send instant email notification to recipient
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                        class="w-full py-3 px-4 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 shadow-lg shadow-indigo-600/30 transition-all flex items-center justify-center gap-2 mt-2">
                    <i class="fa-solid fa-paper-plane"></i>
                    <span>Send Message</span>
                </button>
            </form>
        </div>

        <!-- Sent Messages History (7 Cols) -->
        <div class="lg:col-span-7 bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200/80 dark:border-slate-800 flex flex-col">
            <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-inbox text-slate-400"></i>
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">Communication History</h2>
                </div>
                <div class="text-xs text-slate-400">
                    {{ $messages->total() }} recorded message(s)
                </div>
            </div>

            @if($messages->count() > 0)
                <div class="divide-y divide-slate-100 dark:divide-slate-800 overflow-y-auto max-h-[580px] pr-1">
                    @foreach($messages as $msg)
                        <div class="py-4 hover:bg-slate-50/50 dark:hover:bg-slate-800/30 rounded-2xl px-3 transition-colors">
                            <div class="flex items-start justify-between gap-3 mb-2">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-950/70 text-indigo-700 dark:text-indigo-300 font-bold flex items-center justify-center text-xs flex-shrink-0">
                                        {{ substr($msg->user?->first_name ?? 'U', 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="text-xs font-bold text-slate-900 dark:text-white">
                                            To: {{ $msg->user?->full_name ?? 'Deleted User' }}
                                        </div>
                                        <div class="text-[11px] text-slate-400">
                                            {{ $msg->user?->email }}
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="text-[11px] text-slate-400">
                                        {{ $msg->created_at->diffForHumans() }}
                                    </span>
                                    <div class="flex items-center justify-end gap-1.5 mt-1">
                                        @if($msg->sent_email)
                                            <span class="text-[10px] px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-700 dark:bg-emerald-950/70 dark:text-emerald-300 font-bold" title="Dispatched via Email">
                                                <i class="fa-solid fa-envelope text-[9px]"></i> Email Sent
                                            </span>
                                        @endif
                                        <form method="POST" action="{{ route('admin.messages.destroy', $msg) }}" onsubmit="return confirm('Delete this message log?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1 text-slate-400 hover:text-rose-500 rounded" title="Delete Log">
                                                <i class="fa-solid fa-trash-can text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-slate-50 dark:bg-slate-800/50 rounded-xl p-3 border border-slate-100 dark:border-slate-800 text-xs">
                                <div class="font-bold text-slate-800 dark:text-slate-200 mb-1">
                                    {{ $msg->subject }}
                                </div>
                                <div class="text-slate-600 dark:text-slate-400 leading-relaxed whitespace-pre-line font-sans">
                                    {{ $msg->message }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($messages->hasPages())
                    <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800">
                        {{ $messages->links() }}
                    </div>
                @endif
            @else
                <div class="flex-1 flex flex-col items-center justify-center py-16 text-center text-slate-400">
                    <i class="fa-regular fa-comment-dots text-4xl mb-3 text-slate-300 dark:text-slate-700"></i>
                    <div class="font-bold text-sm text-slate-700 dark:text-slate-300">No Messages Logged Yet</div>
                    <p class="text-xs mt-1 text-slate-400 max-w-xs">
                        Use the compose form on the left or click "Contact" on any user in User Management.
                    </p>
                </div>
            @endif
        </div>

    </div>

</div>
@endsection
