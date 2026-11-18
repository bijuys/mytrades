@extends('layouts.app')

@section('title', 'Trading Journal - MyTrades')

@section('content')
<div class="space-y-6">

    <!-- Top Header & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                Trading Journal
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                Review past executions, refine your edge, and track your performance.
            </p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('analytics.index') }}" class="px-4 py-2.5 text-sm font-bold text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800 rounded-xl transition-colors shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-chart-pie text-indigo-500"></i>
                <span>Analytics</span>
            </a>
            <a href="{{ route('trades.create') }}" class="px-5 py-2.5 text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-500 rounded-xl shadow-md shadow-indigo-600/30 hover:shadow-indigo-600/40 transition-all flex items-center gap-2">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>Log New Trade</span>
            </a>
        </div>
    </div>

    <!-- Summary KPI Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Net P&L -->
        <div class="p-5 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm">
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Total Net P&L</div>
            <div class="text-2xl sm:text-3xl font-black {{ $totalNetPnl > 0 ? 'text-emerald-600 dark:text-emerald-400' : ($totalNetPnl < 0 ? 'text-rose-600 dark:text-rose-400' : 'text-slate-800 dark:text-slate-100') }}">
                {{ $totalNetPnl >= 0 ? '+' : '' }}{{ number_format($totalNetPnl, 2) }}
            </div>
            <div class="text-xs text-slate-400 mt-1">
                Gross: <span class="font-semibold text-slate-600 dark:text-slate-300">{{ number_format($totalGrossPnl, 2) }}</span>
            </div>
        </div>

        <!-- Win Rate -->
        <div class="p-5 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm">
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Win Rate</div>
            <div class="text-2xl sm:text-3xl font-black text-indigo-600 dark:text-indigo-400">
                {{ $winRate }}%
            </div>
            <div class="text-xs text-slate-400 mt-1">
                <span class="text-emerald-600 font-bold">{{ $winCount }}W</span> / <span class="text-rose-600 font-bold">{{ $lossCount }}L</span> ({{ $totalTrades }} trades)
            </div>
        </div>

        <!-- Profit Factor -->
        <div class="p-5 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm">
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Profit Factor</div>
            <div class="text-2xl sm:text-3xl font-black text-slate-800 dark:text-slate-100">
                {{ $profitFactor }}
            </div>
            <div class="text-xs text-slate-400 mt-1">Gains to losses ratio</div>
        </div>

        <!-- Total Charges -->
        <div class="p-5 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm">
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Total Charges</div>
            <div class="text-2xl sm:text-3xl font-black text-slate-700 dark:text-slate-200">
                {{ number_format($totalCharges, 2) }}
            </div>
            <div class="text-xs text-slate-400 mt-1">Brokerage & exchange costs</div>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 shadow-sm border border-slate-200/80 dark:border-slate-800">
        <form method="GET" action="{{ route('trades.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3.5">
            
            <!-- Search -->
            <div class="lg:col-span-2 relative">
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}" 
                       placeholder="Search instrument, lessons, strategy..." 
                       class="w-full pl-10 pr-4 py-2.5 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3 text-slate-400 text-sm"></i>
            </div>

            <!-- Strategy Filter -->
            <div>
                <select name="strategy_id" onchange="this.form.submit()" class="w-full px-3 py-2.5 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">All Strategies</option>
                    @foreach($userStrategies as $strat)
                        <option value="{{ $strat->id }}" {{ request('strategy_id') == $strat->id ? 'selected' : '' }}>
                            {{ $strat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Result Filter -->
            <div>
                <select name="result" onchange="this.form.submit()" class="w-full px-3 py-2.5 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">All Outcomes</option>
                    <option value="win" {{ request('result') === 'win' ? 'selected' : '' }}>Wins Only</option>
                    <option value="loss" {{ request('result') === 'loss' ? 'selected' : '' }}>Losses Only</option>
                    <option value="be" {{ request('result') === 'be' ? 'selected' : '' }}>Break-Even Only</option>
                </select>
            </div>

            <!-- Date Range Filter -->
            <div class="flex items-center gap-2">
                <select name="date_range" onchange="this.form.submit()" class="w-full px-3 py-2.5 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">All Time</option>
                    <option value="today" {{ request('date_range') === 'today' ? 'selected' : '' }}>Today</option>
                    <option value="week" {{ request('date_range') === 'week' ? 'selected' : '' }}>This Week</option>
                    <option value="month" {{ request('date_range') === 'month' ? 'selected' : '' }}>This Month</option>
                    <option value="year" {{ request('date_range') === 'year' ? 'selected' : '' }}>This Year</option>
                </select>

                @if(request()->hasAny(['search', 'strategy_id', 'result', 'date_range', 'direction']))
                    <a href="{{ route('trades.index') }}" class="p-2.5 rounded-xl text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800" title="Reset Filters">
                        <i class="fa-solid fa-rotate-left text-sm"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Trades List Table / Card View -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-sm border border-slate-200/80 dark:border-slate-800 overflow-hidden">
        @if($trades->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200/80 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-800/50 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                            <th class="py-4 px-5">Instrument</th>
                            <th class="py-4 px-4">Date & Time</th>
                            <th class="py-4 px-4">Execution (Qty × Entry)</th>
                            <th class="py-4 px-4">Exit Price</th>
                            <th class="py-4 px-4 text-right">Net P&L</th>
                            <th class="py-4 px-4">Strategy</th>
                            <th class="py-4 px-4 text-center">Chart</th>
                            <th class="py-4 px-5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80 text-sm">
                        @foreach($trades as $trade)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors group">
                                
                                <!-- Instrument & Direction -->
                                <td class="py-4 px-5 font-bold">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('trades.show', $trade) }}" class="text-slate-900 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400 font-extrabold text-base">
                                            {{ $trade->instrument }}
                                        </a>
                                        <span class="px-2 py-0.5 text-[10px] font-black uppercase rounded {{ $trade->direction === 'BUY' ? 'bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300' : 'bg-rose-100 dark:bg-rose-950 text-rose-700 dark:text-rose-300' }}">
                                            {{ $trade->direction === 'BUY' ? 'LONG' : 'SHORT' }}
                                        </span>
                                    </div>
                                    <div class="text-xs text-slate-400 font-normal mt-0.5">
                                        {{ $trade->exit_reason ?: 'Regular trade' }}
                                    </div>
                                </td>

                                <!-- Entry Date & Time -->
                                <td class="py-4 px-4 text-xs text-slate-500 dark:text-slate-400 whitespace-nowrap">
                                    <div class="font-semibold text-slate-700 dark:text-slate-200">
                                        {{ $trade->entry_datetime?->format('d M Y') }}
                                    </div>
                                    <div class="text-[11px] text-slate-400">
                                        {{ $trade->entry_datetime?->format('h:i A') }}
                                    </div>
                                </td>

                                <!-- Quantity & Entry -->
                                <td class="py-4 px-4 font-mono text-xs">
                                    <span class="font-bold text-slate-800 dark:text-slate-200">{{ number_format($trade->quantity, $trade->quantity == (int)$trade->quantity ? 0 : 2) }}</span>
                                    <span class="text-slate-400">@</span>
                                    <span class="font-semibold text-slate-700 dark:text-slate-300">{{ number_format($trade->entry_price, 2) }}</span>
                                </td>

                                <!-- Exit Price -->
                                <td class="py-4 px-4 font-mono text-xs text-slate-700 dark:text-slate-300">
                                    {{ $trade->exit_price !== null ? number_format($trade->exit_price, 2) : '--' }}
                                </td>

                                <!-- Net P&L -->
                                <td class="py-4 px-4 text-right whitespace-nowrap">
                                    <div class="font-mono font-extrabold text-base {{ $trade->net_pnl > 0 ? 'text-emerald-600 dark:text-emerald-400' : ($trade->net_pnl < 0 ? 'text-rose-600 dark:text-rose-400' : 'text-slate-600 dark:text-slate-300') }}">
                                        {{ $trade->net_pnl >= 0 ? '+' : '' }}{{ number_format($trade->net_pnl, 2) }}
                                    </div>
                                    <div class="text-[10px] text-slate-400">
                                        Fee: {{ number_format($trade->charges, 2) }}
                                    </div>
                                </td>

                                <!-- Strategy Badge -->
                                <td class="py-4 px-4 whitespace-nowrap">
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-lg bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800/80">
                                        {{ $trade->display_strategy }}
                                    </span>
                                </td>

                                <!-- Chart Screenshot Icon -->
                                <td class="py-4 px-4 text-center">
                                    @if($trade->screenshot)
                                        <a href="{{ $trade->screenshot_url }}" target="_blank" class="inline-block p-1.5 rounded-lg text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-950/60" title="View chart screenshot (WebP)">
                                            <i class="fa-regular fa-image text-lg"></i>
                                        </a>
                                    @else
                                        <span class="text-slate-300 dark:text-slate-700 text-xs">--</span>
                                    @endif
                                </td>

                                <!-- Actions -->
                                <td class="py-4 px-5 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('trades.show', $trade) }}" class="p-2 rounded-lg text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-slate-800 transition-colors" title="View Full Details">
                                            <i class="fa-solid fa-eye text-sm"></i>
                                        </a>
                                        <a href="{{ route('trades.edit', $trade) }}" class="p-2 rounded-lg text-slate-500 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-slate-800 transition-colors" title="Edit Entry">
                                            <i class="fa-solid fa-pen-to-square text-sm"></i>
                                        </a>
                                        <form method="POST" action="{{ route('trades.destroy', $trade) }}" onsubmit="return confirm('Delete this trade entry?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-slate-800 transition-colors" title="Delete Entry">
                                                <i class="fa-solid fa-trash text-sm"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($trades->hasPages())
                <div class="p-5 border-t border-slate-200 dark:border-slate-800">
                    {{ $trades->links() }}
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="py-16 px-6 text-center">
                <div class="w-16 h-16 rounded-3xl gradient-brand mx-auto flex items-center justify-center text-white text-2xl shadow-lg shadow-indigo-500/20 mb-4">
                    <i class="fa-solid fa-book-open"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-800 dark:text-slate-200">No Journal Entries Found</h3>
                <p class="text-sm text-slate-400 max-w-sm mx-auto mt-1 mb-6">
                    Start recording your trades to build discipline and unlock rich performance analytics.
                </p>
                <a href="{{ route('trades.create') }}" class="inline-flex items-center gap-2 px-6 py-3 text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-500 rounded-xl shadow-md transition-all">
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span>Log First Trade</span>
                </a>
            </div>
        @endif
    </div>

</div>
@endsection
