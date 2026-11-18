@extends('layouts.app')

@section('title', 'Newsletter Broadcasting - Super Admin Console - MyTrades')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-400 mb-1">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-indigo-500">Admin</a>
                <span>/</span>
                <span class="text-indigo-600 dark:text-indigo-400">Newsletter</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                Newsletter Broadcasting
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                Send market updates, trading insights, and platform announcements to your members.
            </p>
        </div>
        <div class="flex items-center gap-3">
            <div class="px-4 py-2 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-xs font-bold flex items-center gap-2">
                <i class="fa-solid fa-users-check text-emerald-500"></i>
                <span>{{ $approvedUsersCount }} Approved Traders</span>
            </div>
            <div class="px-4 py-2 rounded-2xl bg-indigo-50 dark:bg-indigo-950/60 border border-indigo-200 dark:border-indigo-800 text-indigo-800 dark:text-indigo-300 text-xs font-bold flex items-center gap-2">
                <i class="fa-solid fa-users text-indigo-500"></i>
                <span>{{ $totalUsersCount }} Total Users</span>
            </div>
        </div>
    </div>

    <!-- Main Grid: Compose & History -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Compose Newsletter Card (7 Cols) -->
        <div class="lg:col-span-7 bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200/80 dark:border-slate-800">
            <div class="flex items-center justify-between pb-4 mb-6 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-base">
                        <i class="fa-solid fa-paper-plane"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900 dark:text-white">Compose Broadcast</h2>
                        <p class="text-xs text-slate-400">Craft and send an email newsletter</p>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.newsletter.send') }}" id="newsletterForm" class="space-y-5">
                @csrf

                <!-- Subject -->
                <div>
                    <label for="subject" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">
                        Newsletter Subject <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           id="subject" 
                           name="subject" 
                           value="{{ old('subject') }}" 
                           required 
                           placeholder="e.g. Weekly Market Wrap & Top Trading Lessons" 
                           class="w-full px-4 py-3 text-sm rounded-xl border @error('subject') border-rose-500 @else border-slate-300 dark:border-slate-700 @enderror bg-white dark:bg-slate-800 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    @error('subject')
                        <p class="mt-1 text-xs text-rose-500 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Target Audience -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">
                        Target Audience <span class="text-rose-500">*</span>
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <label class="flex items-center gap-3 p-3.5 rounded-2xl border border-slate-200 dark:border-slate-700 hover:border-indigo-500 cursor-pointer bg-slate-50/50 dark:bg-slate-800/40">
                            <input type="radio" name="target_audience" value="approved" checked class="text-indigo-600 focus:ring-indigo-500">
                            <div>
                                <div class="text-xs font-bold text-slate-900 dark:text-white">Approved Traders Only</div>
                                <div class="text-[11px] text-slate-400">{{ $approvedUsersCount }} active journal users</div>
                            </div>
                        </label>
                        <label class="flex items-center gap-3 p-3.5 rounded-2xl border border-slate-200 dark:border-slate-700 hover:border-indigo-500 cursor-pointer bg-slate-50/50 dark:bg-slate-800/40">
                            <input type="radio" name="target_audience" value="all" class="text-indigo-600 focus:ring-indigo-500">
                            <div>
                                <div class="text-xs font-bold text-slate-900 dark:text-white">All Registered Accounts</div>
                                <div class="text-[11px] text-slate-400">{{ $totalUsersCount }} all signups</div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Content -->
                <div>
                    <label for="content" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">
                        Newsletter Content (Markdown/Text) <span class="text-rose-500">*</span>
                    </label>
                    <textarea id="content" 
                              name="content" 
                              rows="9" 
                              required 
                              placeholder="Dear Traders,&#10;&#10;Here are our key insights for this trading week:&#10;&#10;1. Risk Management First: Remember that capital preservation is key...&#10;2. Strategy Analysis: Breakout setups had a 68% win rate this week.&#10;&#10;Keep journaling your trades diligently!&#10;&#10;Happy Trading,&#10;The MyTrades Team" 
                              class="w-full px-4 py-3 text-sm rounded-xl border @error('content') border-rose-500 @else border-slate-300 dark:border-slate-700 @enderror bg-white dark:bg-slate-800 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 leading-relaxed font-sans"></textarea>
                    @error('content')
                        <p class="mt-1 text-xs text-rose-500 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-2">
                    <button type="submit" 
                            formaction="{{ route('admin.newsletter.test') }}" 
                            class="w-full sm:w-auto px-4 py-2.5 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-300 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 transition-colors">
                        <i class="fa-solid fa-vial mr-1.5"></i> Send Preview to My Email
                    </button>

                    <button type="submit" 
                            onclick="return confirm('Are you sure you want to broadcast this newsletter to the selected audience?');" 
                            class="w-full sm:w-auto px-6 py-2.5 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 shadow-lg shadow-indigo-600/30 transition-all flex items-center justify-center gap-2">
                        <i class="fa-solid fa-paper-plane"></i>
                        <span>Broadcast to Audience</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- History & Past Broadcasts (5 Cols) -->
        <div class="lg:col-span-5 bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200/80 dark:border-slate-800 flex flex-col">
            <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-clock-rotate-left text-slate-400"></i>
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">Broadcast History</h2>
                </div>
                <span class="text-xs text-slate-400">{{ $newsletters->total() }} sent</span>
            </div>

            @if($newsletters->count() > 0)
                <div class="divide-y divide-slate-100 dark:divide-slate-800 overflow-y-auto max-h-[560px] pr-1">
                    @foreach($newsletters as $item)
                        <div class="py-4">
                            <div class="flex items-start justify-between gap-2 mb-1.5">
                                <h3 class="text-sm font-bold text-slate-900 dark:text-white line-clamp-1">
                                    {{ $item->subject }}
                                </h3>
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full {{ $item->target_audience === 'approved' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/70 dark:text-emerald-300' : 'bg-indigo-100 text-indigo-700 dark:bg-indigo-950/70 dark:text-indigo-300' }} flex-shrink-0">
                                    {{ ucfirst($item->target_audience) }}
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-2 leading-relaxed mb-2">
                                {{ Str::limit($item->content, 140) }}
                            </p>
                            <div class="flex items-center justify-between text-[11px] text-slate-400 font-medium">
                                <span class="flex items-center gap-1.5">
                                    <i class="fa-solid fa-envelope-circle-check text-emerald-500"></i>
                                    {{ $item->recipients_count }} recipient(s)
                                </span>
                                <span>{{ $item->sent_at?->diffForHumans() ?? $item->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($newsletters->hasPages())
                    <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800">
                        {{ $newsletters->links() }}
                    </div>
                @endif
            @else
                <div class="flex-1 flex flex-col items-center justify-center py-16 text-center text-slate-400">
                    <i class="fa-solid fa-envelope-open-text text-4xl mb-3 text-slate-300 dark:text-slate-700"></i>
                    <div class="font-bold text-sm text-slate-700 dark:text-slate-300">No Newsletters Sent Yet</div>
                    <p class="text-xs mt-1 text-slate-400 max-w-xs">
                        Use the compose form on the left to send your first community announcement.
                    </p>
                </div>
            @endif
        </div>

    </div>

</div>
@endsection
