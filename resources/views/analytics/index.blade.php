@extends('layouts.app')

@section('title', 'Performance Summary - MyTrades')

@section('content')
<div class="space-y-8">

    <!-- Top Header & Filter Bar -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-400 mb-1">
                <a href="{{ route('trades.index') }}" class="hover:text-indigo-500">Journal</a>
                <span>/</span>
                <span class="text-indigo-600 dark:text-indigo-400">Performance Summary</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                Performance Summary & Analytics
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                Multi-dimensional insights into your execution edge, consistency, and risk metrics.
            </p>
        </div>

        <!-- Time Range Selector -->
        <div class="flex items-center gap-2 bg-white dark:bg-slate-900 p-1.5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm text-xs font-bold">
            <a href="{{ route('analytics.index', ['range' => 'all']) }}" class="px-3 py-1.5 rounded-xl transition-colors {{ $range === 'all' ? 'bg-indigo-600 text-white' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">All Time</a>
            <a href="{{ route('analytics.index', ['range' => '7d']) }}" class="px-3 py-1.5 rounded-xl transition-colors {{ $range === '7d' ? 'bg-indigo-600 text-white' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">Last 7D</a>
            <a href="{{ route('analytics.index', ['range' => '30d']) }}" class="px-3 py-1.5 rounded-xl transition-colors {{ $range === '30d' ? 'bg-indigo-600 text-white' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">Last 30D</a>
            <a href="{{ route('analytics.index', ['range' => '90d']) }}" class="px-3 py-1.5 rounded-xl transition-colors {{ $range === '90d' ? 'bg-indigo-600 text-white' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">Last 90D</a>
            <a href="{{ route('analytics.index', ['range' => 'ytd']) }}" class="px-3 py-1.5 rounded-xl transition-colors {{ $range === 'ytd' ? 'bg-indigo-600 text-white' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">YTD</a>
        </div>
    </div>

    <!-- 1. Executive Performance KPI Grid -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        <!-- Net PnL -->
        <div class="p-5 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm col-span-2 sm:col-span-1 lg:col-span-2">
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Total Net P&L</div>
            <div class="text-2xl sm:text-3xl font-black {{ $totalNetPnl > 0 ? 'text-emerald-600 dark:text-emerald-400' : ($totalNetPnl < 0 ? 'text-rose-600 dark:text-rose-400' : 'text-slate-800 dark:text-slate-100') }}">
                {{ $totalNetPnl >= 0 ? '+' : '' }}{{ number_format($totalNetPnl, 2) }}
            </div>
            <div class="text-xs text-slate-400 mt-1">
                Gross: <span class="font-semibold text-slate-600 dark:text-slate-300">{{ number_format($totalGrossPnl, 2) }}</span> • Fees: <span class="font-semibold text-slate-600 dark:text-slate-300">{{ number_format($totalCharges, 2) }}</span>
            </div>
        </div>

        <!-- Win Rate -->
        <div class="p-5 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm">
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Win Rate</div>
            <div class="text-2xl sm:text-3xl font-black text-indigo-600 dark:text-indigo-400">
                {{ $winRate }}%
            </div>
            <div class="text-xs text-slate-400 mt-1">
                {{ $winCount }}W / {{ $lossCount }}L / {{ $beCount }}BE
            </div>
        </div>

        <!-- Profit Factor -->
        <div class="p-5 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm">
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Profit Factor</div>
            <div class="text-2xl sm:text-3xl font-black text-slate-800 dark:text-slate-100">
                {{ $profitFactor }}
            </div>
            <div class="text-xs text-slate-400 mt-1">
                {{ $profitFactor >= 2 ? '🔥 Excellent edge' : ($profitFactor >= 1.3 ? '✅ Positive edge' : '⚠️ Refine edge') }}
            </div>
        </div>

        <!-- Avg Win vs Loss -->
        <div class="p-5 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm">
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Avg Win / Loss</div>
            <div class="text-lg font-black text-slate-800 dark:text-slate-100">
                <span class="text-emerald-600 dark:text-emerald-400">+{{ number_format($avgWin, 0) }}</span> / <span class="text-rose-600 dark:text-rose-400">-{{ number_format($avgLoss, 0) }}</span>
            </div>
            <div class="text-xs text-slate-400 mt-1">
                Payoff: <span class="font-bold text-slate-700 dark:text-slate-200">{{ $winLossRatio }}x</span>
            </div>
        </div>

        <!-- Max Streaks -->
        <div class="p-5 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm">
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Max Streaks</div>
            <div class="text-lg font-black text-slate-800 dark:text-slate-100">
                <span class="text-emerald-600 dark:text-emerald-400">{{ $maxWinStreak }}W</span> / <span class="text-rose-600 dark:text-rose-400">{{ $maxLossStreak }}L</span>
            </div>
            <div class="text-xs text-slate-400 mt-1">Consecutive outcomes</div>
        </div>
    </div>

    <!-- 2. Equity Curve Chart -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200/80 dark:border-slate-800">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-chart-line text-indigo-500"></i> Cumulative Equity Curve
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Tracking running Net P&L trajectory across all closed trades</p>
            </div>
            @if($totalTrades > 0)
                <div class="text-xs font-bold px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                    {{ $totalTrades }} logged trades
                </div>
            @endif
        </div>

        @if(count($equityData) > 0)
            <div class="h-72 w-full">
                <canvas id="equityChart"></canvas>
            </div>
        @else
            <div class="py-16 text-center text-slate-400 text-sm">
                Log trades to render your cumulative equity growth curve.
            </div>
        @endif
    </div>

    <!-- 3. Performance by Strategy & Monthly P&L -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Strategy Breakdown Table -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200/80 dark:border-slate-800">
            <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                <i class="fa-solid fa-shapes text-indigo-500"></i> Performance by Strategy
            </h2>

            @if(count($strategyStats) > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="border-b border-slate-100 dark:border-slate-800 text-slate-400 uppercase font-bold text-[10px] pb-2">
                                <th class="pb-3">Strategy</th>
                                <th class="pb-3 text-center">Trades</th>
                                <th class="pb-3 text-center">Win %</th>
                                <th class="pb-3 text-right">Net P&L</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @foreach($strategyStats as $stat)
                                <tr>
                                    <td class="py-3 font-semibold text-slate-800 dark:text-slate-200">
                                        <div class="flex items-center gap-2">
                                            <span class="w-2.5 h-2.5 rounded-full" style="background-color: {{ $stat['color'] }}"></span>
                                            <span>{{ $stat['name'] }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3 text-center font-mono text-slate-600 dark:text-slate-400">
                                        {{ $stat['total'] }} ({{ $stat['wins'] }}W)
                                    </td>
                                    <td class="py-3 text-center font-bold">
                                        <span class="{{ $stat['win_rate'] >= 50 ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-600 dark:text-slate-400' }}">
                                            {{ $stat['win_rate'] }}%
                                        </span>
                                    </td>
                                    <td class="py-3 text-right font-mono font-bold {{ $stat['net_pnl'] > 0 ? 'text-emerald-600 dark:text-emerald-400' : ($stat['net_pnl'] < 0 ? 'text-rose-600 dark:text-rose-400' : 'text-slate-600') }}">
                                        {{ $stat['net_pnl'] >= 0 ? '+' : '' }}{{ number_format($stat['net_pnl'], 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="py-8 text-center text-slate-400 text-xs italic">
                    No strategy data recorded yet.
                </div>
            @endif
        </div>

        <!-- Monthly P&L Chart -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200/80 dark:border-slate-800">
            <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                <i class="fa-solid fa-calendar-days text-indigo-500"></i> Monthly Net P&L
            </h2>

            @if(count($monthlyPnl) > 0)
                <div class="h-64 w-full">
                    <canvas id="monthlyChart"></canvas>
                </div>
            @else
                <div class="py-16 text-center text-slate-400 text-xs italic">
                    No monthly trading data available for this range.
                </div>
            @endif
        </div>

    </div>

    <!-- 4. Performance by Instrument & Direction Breakdown -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Top Instruments (2 Cols) -->
        <div class="lg:col-span-2 bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200/80 dark:border-slate-800">
            <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                <i class="fa-solid fa-coins text-indigo-500"></i> Top Traded Instruments
            </h2>

            @if(count($instrumentStats) > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    @foreach(array_slice($instrumentStats, 0, 8) as $inst)
                        <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800 flex items-center justify-between">
                            <div>
                                <div class="font-extrabold text-base text-slate-900 dark:text-white">{{ $inst['instrument'] }}</div>
                                <div class="text-xs text-slate-400 mt-0.5">
                                    {{ $inst['total'] }} trades • <span class="font-bold text-indigo-600 dark:text-indigo-400">{{ $inst['win_rate'] }}% Win</span>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="font-mono font-black text-base {{ $inst['net_pnl'] > 0 ? 'text-emerald-600 dark:text-emerald-400' : ($inst['net_pnl'] < 0 ? 'text-rose-600 dark:text-rose-400' : 'text-slate-600') }}">
                                    {{ $inst['net_pnl'] >= 0 ? '+' : '' }}{{ number_format($inst['net_pnl'], 2) }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="py-8 text-center text-slate-400 text-xs italic">
                    No instrument records found.
                </div>
            @endif
        </div>

        <!-- Direction (Long vs Short) -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200/80 dark:border-slate-800 flex flex-col justify-between">
            <div>
                <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-arrows-up-down text-indigo-500"></i> Long vs Short
                </h2>

                <div class="space-y-4">
                    <!-- Long (BUY) -->
                    <div class="p-4 rounded-2xl bg-emerald-50/60 dark:bg-emerald-950/40 border border-emerald-200/60 dark:border-emerald-800/60">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-emerald-800 dark:text-emerald-200 uppercase tracking-wider">LONG (Buy)</span>
                            <span class="text-xs font-bold text-emerald-700 dark:text-emerald-300">{{ $directionStats['BUY']['win_rate'] }}% Win Rate</span>
                        </div>
                        <div class="text-xl font-black text-slate-800 dark:text-slate-100 mt-1">
                            {{ $directionStats['BUY']['net_pnl'] >= 0 ? '+' : '' }}{{ number_format($directionStats['BUY']['net_pnl'], 2) }}
                        </div>
                        <div class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            {{ $directionStats['BUY']['total'] }} trades ({{ $directionStats['BUY']['wins'] }}W / {{ $directionStats['BUY']['losses'] }}L)
                        </div>
                    </div>

                    <!-- Short (SELL) -->
                    <div class="p-4 rounded-2xl bg-rose-50/60 dark:bg-rose-950/40 border border-rose-200/60 dark:border-rose-800/60">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-rose-800 dark:text-rose-200 uppercase tracking-wider">SHORT (Sell)</span>
                            <span class="text-xs font-bold text-rose-700 dark:text-rose-300">{{ $directionStats['SELL']['win_rate'] }}% Win Rate</span>
                        </div>
                        <div class="text-xl font-black text-slate-800 dark:text-slate-100 mt-1">
                            {{ $directionStats['SELL']['net_pnl'] >= 0 ? '+' : '' }}{{ number_format($directionStats['SELL']['net_pnl'], 2) }}
                        </div>
                        <div class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            {{ $directionStats['SELL']['total'] }} trades ({{ $directionStats['SELL']['wins'] }}W / {{ $directionStats['SELL']['losses'] }}L)
                        </div>
                    </div>
                </div>
            </div>

            <!-- Best / Worst Trades Callout -->
            <div class="pt-6 border-t border-slate-100 dark:border-slate-800 text-xs space-y-2">
                @if($bestTrade)
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400 font-medium">Best Trade:</span>
                        <a href="{{ route('trades.show', $bestTrade) }}" class="font-bold text-emerald-600 dark:text-emerald-400 hover:underline">
                            +{{ number_format($bestTrade->net_pnl, 2) }} ({{ $bestTrade->instrument }})
                        </a>
                    </div>
                @endif
                @if($worstTrade)
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400 font-medium">Worst Trade:</span>
                        <a href="{{ route('trades.show', $worstTrade) }}" class="font-bold text-rose-600 dark:text-rose-400 hover:underline">
                            {{ number_format($worstTrade->net_pnl, 2) }} ({{ $worstTrade->instrument }})
                        </a>
                    </div>
                @endif
            </div>
        </div>

    </div>

</div>

@push('scripts')
<script>
    // 1. Equity Curve Line Chart
    @if(count($equityData) > 0)
    const ctxEquity = document.getElementById('equityChart').getContext('2d');
    const isDark = document.documentElement.classList.contains('dark');

    const equityChart = new Chart(ctxEquity, {
        type: 'line',
        data: {
            labels: {!! json_encode($equityLabels) !!},
            datasets: [{
                label: 'Cumulative Net P&L',
                data: {!! json_encode($equityData) !!},
                borderColor: '#4f46e5',
                backgroundColor: 'rgba(79, 70, 229, 0.08)',
                borderWidth: 2.5,
                fill: true,
                tension: 0.3,
                pointRadius: 3,
                pointBackgroundColor: '#4f46e5',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Net P&L: ' + (context.raw >= 0 ? '+' : '') + context.raw;
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { color: isDark ? '#94a3b8' : '#64748b' }
                },
                y: {
                    grid: { color: isDark ? 'rgba(51, 65, 85, 0.4)' : 'rgba(226, 232, 240, 0.8)' },
                    ticks: { color: isDark ? '#94a3b8' : '#64748b' }
                }
            }
        }
    });
    @endif

    // 2. Monthly P&L Bar Chart
    @if(count($monthlyPnl) > 0)
    const ctxMonthly = document.getElementById('monthlyChart').getContext('2d');
    const monthlyLabels = {!! json_encode(array_column($monthlyPnl, 'label')) !!};
    const monthlyData = {!! json_encode(array_column($monthlyPnl, 'net_pnl')) !!};
    const monthlyColors = monthlyData.map(val => val >= 0 ? '#10b981' : '#ef4444');

    new Chart(ctxMonthly, {
        type: 'bar',
        data: {
            labels: monthlyLabels,
            datasets: [{
                label: 'Monthly Net P&L',
                data: monthlyData,
                backgroundColor: monthlyColors,
                borderRadius: 8,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                x: { grid: { display: false } },
                y: { grid: { color: 'rgba(226, 232, 240, 0.5)' } }
            }
        }
    });
    @endif
</script>
@endpush
@endsection
