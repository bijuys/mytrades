@extends('layouts.app')

@section('title', 'Admin Overview - MyTrades Console')

@section('content')
<div class="space-y-8">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-400 mb-1">
                <span>Super Admin</span>
                <span>/</span>
                <span class="text-indigo-600 dark:text-indigo-400">Dashboard</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                Administration Overview
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                Manage user approvals, review platform trading volume, and audit journal entries.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.users', ['status' => 'pending']) }}" class="px-4 py-2.5 text-sm font-bold text-amber-800 dark:text-amber-200 bg-amber-50 dark:bg-amber-950/60 border border-amber-200 dark:border-amber-800 rounded-xl hover:bg-amber-100 transition-colors flex items-center gap-2">
                <i class="fa-solid fa-user-clock text-amber-600"></i>
                <span>Pending Approvals ({{ $pendingUsersCount }})</span>
            </a>
            <a href="{{ route('admin.trades') }}" class="px-4 py-2.5 text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-500 rounded-xl shadow transition-colors flex items-center gap-2">
                <i class="fa-solid fa-list-check text-xs"></i>
                <span>Audit Trades</span>
            </a>
        </div>
    </div>

    <!-- Platform KPI Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <!-- Total Users -->
        <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Users</span>
                <span class="p-2 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 text-sm">
                    <i class="fa-solid fa-users"></i>
                </span>
            </div>
            <div class="text-3xl font-black text-slate-900 dark:text-white">
                {{ $totalUsers }}
            </div>
            <div class="text-xs text-slate-400 mt-1">
                {{ $approvedUsersCount }} active & approved
            </div>
        </div>

        <!-- Pending Approvals -->
        <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border {{ $pendingUsersCount > 0 ? 'border-amber-300 dark:border-amber-700 bg-amber-50/20' : 'border-slate-200/80 dark:border-slate-800' }} shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Awaiting Approval</span>
                <span class="p-2 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 text-sm">
                    <i class="fa-solid fa-user-clock"></i>
                </span>
            </div>
            <div class="text-3xl font-black {{ $pendingUsersCount > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-slate-900 dark:text-white' }}">
                {{ $pendingUsersCount }}
            </div>
            <div class="text-xs text-slate-400 mt-1 flex items-center justify-between">
                <span>{{ $pendingUsersCount > 0 ? 'Email confirmed, action required' : 'All registrations reviewed' }}</span>
                @if($unconfirmedUsersCount > 0)
                    <a href="{{ route('admin.users', ['status' => 'unconfirmed']) }}" class="text-[11px] font-bold text-slate-400 hover:text-indigo-500">
                        ({{ $unconfirmedUsersCount }} unconfirmed)
                    </a>
                @endif
            </div>
        </div>

        <!-- Total Trades Logged -->
        <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Trades Logged</span>
                <span class="p-2 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 text-sm">
                    <i class="fa-solid fa-chart-line"></i>
                </span>
            </div>
            <div class="text-3xl font-black text-slate-900 dark:text-white">
                {{ $totalTrades }}
            </div>
            <div class="text-xs text-slate-400 mt-1">
                Across all registered accounts
            </div>
        </div>

        <!-- Platform Net PnL -->
        <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Platform Cumulative P&L</span>
                <span class="p-2 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 text-sm">
                    <i class="fa-solid fa-wallet"></i>
                </span>
            </div>
            <div class="text-3xl font-black {{ $totalPlatformPnl >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                {{ $totalPlatformPnl >= 0 ? '+' : '' }}{{ number_format($totalPlatformPnl, 2) }}
            </div>
            <div class="text-xs text-slate-400 mt-1">
                Net sum of all member trades
            </div>
        </div>
    </div>

    <!-- Urgent Pending User Approvals Card -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200/80 dark:border-slate-800">
        <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-100 dark:border-slate-800">
            <div>
                <h2 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-user-clock text-amber-500"></i> Pending User Approvals (Email Confirmed)
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Traders who verified their email and are awaiting your Super Admin approval before they can log in</p>
            </div>
            <a href="{{ route('admin.users', ['status' => 'pending']) }}" class="text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
                View All Users &rarr;
            </a>
        </div>

        @if($pendingUsers->count() > 0)
            <div class="divide-y divide-slate-100 dark:divide-slate-800">
                @foreach($pendingUsers as $pUser)
                    <div class="py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-start sm:items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-amber-100 dark:bg-amber-900/60 text-amber-700 dark:text-amber-300 font-bold flex items-center justify-center flex-shrink-0">
                                {{ strtoupper(substr($pUser->first_name, 0, 1) . substr($pUser->last_name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="font-extrabold text-slate-900 dark:text-white text-base">
                                    {{ $pUser->full_name }}
                                </div>
                                <div class="text-xs text-slate-400 flex flex-wrap items-center gap-2 mt-0.5">
                                    <span><i class="fa-regular fa-envelope mr-1"></i> {{ $pUser->email }}</span>
                                    <span>•</span>
                                    <span><i class="fa-solid fa-phone mr-1"></i> {{ $pUser->phone }}</span>
                                    <span>•</span>
                                    <span class="font-semibold text-indigo-600 dark:text-indigo-400">{{ $pUser->trading_experience }} yrs experience</span>
                                </div>
                            </div>
                        </div>

                        <!-- 1-Click Approve / Reject -->
                        <div class="flex items-center gap-2 self-end sm:self-center">
                            <form method="POST" action="{{ route('admin.users.approve', $pUser) }}">
                                @csrf
                                <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-500 rounded-xl shadow-sm transition-all flex items-center gap-1.5">
                                    <i class="fa-solid fa-check"></i>
                                    <span>Approve Account</span>
                                </button>
                            </form>

                            <button type="button" onclick="openRejectModal({{ $pUser->id }}, '{{ addslashes($pUser->full_name) }}')" class="px-3 py-2 text-xs font-bold text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-950/60 hover:bg-rose-100 rounded-xl border border-rose-200 dark:border-rose-900 transition-colors">
                                <i class="fa-solid fa-xmark mr-1"></i> Reject
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="py-10 text-center text-slate-400 text-sm">
                <i class="fa-solid fa-circle-check text-emerald-500 text-3xl mb-2"></i>
                <div class="font-bold text-slate-700 dark:text-slate-300">All Caught Up!</div>
                <div class="text-xs text-slate-400 mt-1">There are no pending user registrations awaiting approval right now.</div>
            </div>
        @endif
    </div>

    <!-- Recent Platform Trades Audit -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200/80 dark:border-slate-800">
        <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-100 dark:border-slate-800">
            <div>
                <h2 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-clock-rotate-left text-indigo-500"></i> Recent Platform Activity
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Latest journal entries logged across all traders</p>
            </div>
            <a href="{{ route('admin.trades') }}" class="text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
                View All Trades &rarr;
            </a>
        </div>

        @if($recentTrades->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="border-b border-slate-100 dark:border-slate-800 text-[10px] text-slate-400 uppercase font-bold">
                            <th class="py-3 px-3">Trader</th>
                            <th class="py-3 px-3">Instrument</th>
                            <th class="py-3 px-3">Direction</th>
                            <th class="py-3 px-3">Qty × Entry</th>
                            <th class="py-3 px-3 text-right">Net P&L</th>
                            <th class="py-3 px-3 text-center">Chart</th>
                            <th class="py-3 px-3 text-right">Logged At</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach($recentTrades as $trade)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40">
                                <td class="py-3 px-3 font-bold text-slate-800 dark:text-slate-200">
                                    {{ $trade->user?->full_name }}
                                </td>
                                <td class="py-3 px-3 font-extrabold text-slate-900 dark:text-white">
                                    {{ $trade->instrument }}
                                </td>
                                <td class="py-3 px-3">
                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded {{ $trade->direction === 'BUY' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                        {{ $trade->direction }}
                                    </span>
                                </td>
                                <td class="py-3 px-3 font-mono">
                                    {{ $trade->quantity }} @ {{ number_format($trade->entry_price, 2) }}
                                </td>
                                <td class="py-3 px-3 text-right font-mono font-bold {{ $trade->net_pnl > 0 ? 'text-emerald-600 dark:text-emerald-400' : ($trade->net_pnl < 0 ? 'text-rose-600 dark:text-rose-400' : 'text-slate-600') }}">
                                    {{ $trade->net_pnl >= 0 ? '+' : '' }}{{ number_format($trade->net_pnl, 2) }}
                                </td>
                                <td class="py-3 px-3 text-center">
                                    @if($trade->screenshot)
                                        <a href="{{ $trade->screenshot_url }}" target="_blank" class="text-indigo-600 hover:underline">
                                            <i class="fa-regular fa-image"></i>
                                        </a>
                                    @else
                                        <span class="text-slate-300">--</span>
                                    @endif
                                </td>
                                <td class="py-3 px-3 text-right text-slate-400">
                                    {{ $trade->journal_datetime?->diffForHumans() }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="py-8 text-center text-slate-400 text-xs italic">
                No trades logged yet on the platform.
            </div>
        @endif
    </div>

</div>

<!-- Reject Reason Modal -->
<div id="rejectModal" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 max-w-md w-full border border-slate-200 dark:border-slate-800 shadow-2xl">
        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">Reject User Registration</h3>
        <p class="text-xs text-slate-500 mb-4">Provide a reason for declining <span id="rejectUserName" class="font-bold text-slate-800 dark:text-slate-200"></span>.</p>
        
        <form id="rejectForm" method="POST" action="">
            @csrf
            <div class="mb-4">
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Reason for Rejection</label>
                <textarea name="rejection_reason" rows="3" required placeholder="e.g. Incomplete phone number verification, not an active trader, etc." class="w-full px-4 py-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm"></textarea>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeRejectModal()" class="px-4 py-2.5 text-xs font-bold text-slate-500 hover:bg-slate-100 rounded-xl">Cancel</button>
                <button type="submit" class="px-5 py-2.5 text-xs font-bold text-white bg-rose-600 hover:bg-rose-500 rounded-xl">Confirm Rejection</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openRejectModal(userId, userName) {
        document.getElementById('rejectUserName').textContent = userName;
        document.getElementById('rejectForm').action = '/admin/users/' + userId + '/reject';
        document.getElementById('rejectModal').classList.remove('hidden');
    }

    function closeRejectModal() {
        document.getElementById('rejectModal').classList.add('hidden');
    }
</script>
@endpush
@endsection
