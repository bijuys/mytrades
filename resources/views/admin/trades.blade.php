@extends('layouts.app')

@section('title', 'Platform Trades Audit - Super Admin Console - MyTrades')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-400 mb-1">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-indigo-500">Admin</a>
                <span>/</span>
                <span class="text-indigo-600 dark:text-indigo-400">All Trades Audit</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                Platform Trades Audit
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                Global overview of all journal entries recorded across all registered traders.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <span class="text-xs font-bold px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                Total Volume: {{ $totalCount }} trades
            </span>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 shadow-sm border border-slate-200/80 dark:border-slate-800">
        <form method="GET" action="{{ route('admin.trades') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
            
            <!-- User Filter -->
            <div>
                <label class="block text-[11px] font-bold text-slate-400 uppercase mb-1">Trader</label>
                <select name="user_id" onchange="this.form.submit()" class="w-full px-3 py-2.5 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                    <option value="">All Traders</option>
                    @foreach($allUsers as $u)
                        <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>
                            {{ $u->full_name }} ({{ $u->email }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Search -->
            <div>
                <label class="block text-[11px] font-bold text-slate-400 uppercase mb-1">Search Keyword</label>
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}" 
                       placeholder="Instrument, strategy, notes..." 
                       class="w-full px-3 py-2.5 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
            </div>

            <!-- Outcome -->
            <div>
                <label class="block text-[11px] font-bold text-slate-400 uppercase mb-1">Outcome</label>
                <select name="result" onchange="this.form.submit()" class="w-full px-3 py-2.5 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                    <option value="">All Outcomes</option>
                    <option value="win" {{ request('result') === 'win' ? 'selected' : '' }}>Wins Only</option>
                    <option value="loss" {{ request('result') === 'loss' ? 'selected' : '' }}>Losses Only</option>
                </select>
            </div>

            <!-- Actions -->
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2.5 px-4 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 rounded-xl shadow-sm">
                    Filter
                </button>
                @if(request()->hasAny(['user_id', 'search', 'result', 'from_date', 'to_date']))
                    <a href="{{ route('admin.trades') }}" class="p-2.5 rounded-xl text-slate-400 hover:text-slate-600 border border-slate-200 dark:border-slate-700" title="Clear Filters">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Trades Table Card -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-sm border border-slate-200/80 dark:border-slate-800 overflow-hidden">
        @if($trades->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="border-b border-slate-200/80 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-800/50 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                            <th class="py-4 px-4">Trade #</th>
                            <th class="py-4 px-4">Trader</th>
                            <th class="py-4 px-4">Instrument</th>
                            <th class="py-4 px-4">Direction</th>
                            <th class="py-4 px-4">Qty × Entry</th>
                            <th class="py-4 px-4">Exit Price</th>
                            <th class="py-4 px-4 text-right">Net P&L (₹)</th>
                            <th class="py-4 px-4">Strategy</th>
                            <th class="py-4 px-4 text-center">Chart (WebP)</th>
                            <th class="py-4 px-4">Journal Date</th>
                            <th class="py-4 px-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-xs">
                        @foreach($trades as $trade)
                            <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition-colors">
                                <td class="py-3 px-4 font-mono font-bold text-slate-400">
                                    #{{ $trade->id }}
                                </td>

                                <td class="py-3 px-4 font-bold text-slate-800 dark:text-slate-200 whitespace-nowrap">
                                    {{ $trade->user?->full_name }}
                                    <div class="text-[10px] font-normal text-slate-400">{{ $trade->user?->email }}</div>
                                </td>

                                <td class="py-3 px-4 font-extrabold text-slate-900 dark:text-white">
                                    {{ $trade->instrument }}
                                </td>

                                <td class="py-3 px-4 whitespace-nowrap">
                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded {{ $trade->direction === 'BUY' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                        {{ $trade->direction }}
                                    </span>
                                </td>

                                <td class="py-3 px-4 font-mono whitespace-nowrap">
                                    {{ number_format($trade->quantity, $trade->quantity == (int)$trade->quantity ? 0 : 2) }} @ {{ number_format($trade->entry_price, 2) }}
                                </td>

                                <td class="py-3 px-4 font-mono whitespace-nowrap text-slate-700 dark:text-slate-300">
                                    {{ $trade->exit_price !== null ? number_format($trade->exit_price, 2) : '--' }}
                                </td>

                                <td class="py-3 px-4 text-right font-mono font-bold whitespace-nowrap {{ $trade->net_pnl > 0 ? 'text-emerald-600 dark:text-emerald-400' : ($trade->net_pnl < 0 ? 'text-rose-600 dark:text-rose-400' : 'text-slate-600') }}">
                                    {{ $trade->net_pnl >= 0 ? '+₹' : '-₹' }}{{ number_format(abs($trade->net_pnl), 2) }}
                                </td>

                                <td class="py-3 px-4 whitespace-nowrap">
                                    <span class="px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-[11px]">
                                        {{ $trade->display_strategy }}
                                    </span>
                                </td>

                                <td class="py-3 px-4 text-center">
                                    @if($trade->screenshot)
                                        <a href="{{ $trade->screenshot_url }}" target="_blank" class="p-1 rounded text-indigo-600 hover:underline" title="View WebP chart">
                                            <i class="fa-regular fa-image text-sm"></i>
                                        </a>
                                    @else
                                        <span class="text-slate-300">--</span>
                                    @endif
                                </td>

                                <td class="py-3 px-4 whitespace-nowrap text-slate-400 text-[11px]">
                                    {{ $trade->journal_datetime?->format('d M Y, H:i') }}
                                </td>

                                <td class="py-3 px-4 text-right whitespace-nowrap">
                                    <form method="POST" action="{{ route('admin.trades.delete', $trade) }}" onsubmit="return confirm('Delete this trade record from platform history?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50" title="Delete Trade">
                                            <i class="fa-solid fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($trades->hasPages())
                <div class="p-5 border-t border-slate-200 dark:border-slate-800">
                    {{ $trades->links() }}
                </div>
            @endif
        @else
            <div class="py-16 text-center text-slate-400">
                <i class="fa-solid fa-chart-line text-4xl mb-3"></i>
                <div class="font-bold text-slate-700 dark:text-slate-300">No Trades Found</div>
                <div class="text-xs mt-1">No trading journal records match the current filters.</div>
            </div>
        @endif
    </div>

</div>
@endsection
