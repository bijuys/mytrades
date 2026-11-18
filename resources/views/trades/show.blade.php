@extends('layouts.app')

@section('title', $trade->instrument . ' (' . $trade->direction . ') - Trade #' . $trade->id . ' - MyTrades')

@section('content')
<div class="max-w-5xl mx-auto my-4 sm:my-8 space-y-6">

    <!-- Top Action Bar & Status -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('trades.index') }}" class="p-2.5 rounded-xl border border-slate-200 dark:border-slate-800 text-slate-500 hover:text-slate-800 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors" title="Back to Journal">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                        {{ $trade->instrument }}
                    </h1>
                    <span class="px-2.5 py-1 text-xs font-black uppercase rounded-md {{ $trade->direction === 'BUY' ? 'bg-emerald-100 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-300' : 'bg-rose-100 dark:bg-rose-950/80 text-rose-700 dark:text-rose-300' }}">
                        {{ $trade->direction === 'BUY' ? 'LONG' : 'SHORT' }}
                    </span>
                    <span class="px-2.5 py-1 text-xs font-bold rounded-md bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                        #{{ $trade->id }}
                    </span>
                </div>
                <div class="text-xs text-slate-400 mt-1 flex items-center gap-3">
                    <span><i class="fa-regular fa-calendar mr-1"></i> {{ $trade->entry_datetime?->format('d M Y, h:i A') }}</span>
                    <span>•</span>
                    <span><i class="fa-solid fa-server mr-1"></i> Journaled: {{ $trade->journal_datetime?->format('d M Y, h:i A') }} (Server)</span>
                </div>
            </div>
        </div>

        <!-- Edit & Delete Action Buttons -->
        <div class="flex items-center gap-3">
            <a href="{{ route('trades.edit', $trade) }}" class="px-4 py-2.5 text-sm font-bold text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/60 rounded-xl transition-colors shadow-sm flex items-center gap-1.5">
                <i class="fa-solid fa-pen-to-square"></i>
                <span>Edit Entry</span>
            </a>
            <form method="POST" action="{{ route('trades.destroy', $trade) }}" onsubmit="return confirm('Are you sure you want to permanently delete this trade journal entry?');" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2.5 text-sm font-bold text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-950/50 border border-rose-200 dark:border-rose-900 hover:bg-rose-100 rounded-xl transition-colors flex items-center gap-1.5">
                    <i class="fa-solid fa-trash"></i>
                    <span>Delete</span>
                </button>
            </form>
        </div>
    </div>

    <!-- P&L and Core Metrics Banner -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <!-- Net PnL -->
        <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm">
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Net Profit / Loss</div>
            <div class="text-2xl sm:text-3xl font-black {{ $trade->net_pnl > 0 ? 'text-emerald-600 dark:text-emerald-400' : ($trade->net_pnl < 0 ? 'text-rose-600 dark:text-rose-400' : 'text-slate-700 dark:text-slate-300') }}">
                {{ $trade->net_pnl >= 0 ? '+' : '' }}{{ number_format($trade->net_pnl, 2) }}
            </div>
            <div class="text-xs font-semibold mt-1 {{ $trade->net_pnl >= 0 ? 'text-emerald-600/80 dark:text-emerald-400/80' : 'text-rose-600/80 dark:text-rose-400/80' }}">
                ROI: {{ $trade->roi }}%
            </div>
        </div>

        <!-- Gross PnL -->
        <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm">
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Gross Profit / Loss</div>
            <div class="text-2xl sm:text-3xl font-black text-slate-800 dark:text-slate-100">
                {{ $trade->gross_pnl >= 0 ? '+' : '' }}{{ number_format($trade->gross_pnl, 2) }}
            </div>
            <div class="text-xs text-slate-400 mt-1">Pre-charges execution</div>
        </div>

        <!-- Charges -->
        <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm">
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Total Charges & Taxes</div>
            <div class="text-2xl sm:text-3xl font-black text-slate-700 dark:text-slate-200">
                {{ number_format($trade->charges, 2) }}
            </div>
            <div class="text-xs text-slate-400 mt-1">Brokerage, STT, turnover</div>
        </div>

        <!-- Strategy Badge -->
        <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm">
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Trading Strategy</div>
            <div class="text-lg font-extrabold text-indigo-600 dark:text-indigo-400 truncate">
                {{ $trade->display_strategy }}
            </div>
            <div class="text-xs text-slate-400 mt-1">
                Exit: <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $trade->exit_reason ?: 'Discretionary' }}</span>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left 2 Cols: Details & Screenshot -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Price Execution Matrix -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200/80 dark:border-slate-800">
                <h2 class="text-base font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-calculator text-indigo-500"></i> Execution Breakdown
                </h2>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                    <div class="p-3.5 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-100 dark:border-slate-800">
                        <div class="text-[11px] font-bold text-slate-400 uppercase">Quantity</div>
                        <div class="text-lg font-extrabold text-slate-800 dark:text-slate-100 mt-0.5">
                            {{ number_format($trade->quantity, $trade->quantity == (int)$trade->quantity ? 0 : 2) }}
                        </div>
                    </div>

                    <div class="p-3.5 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-100 dark:border-slate-800">
                        <div class="text-[11px] font-bold text-slate-400 uppercase">Entry Price</div>
                        <div class="text-lg font-extrabold text-slate-800 dark:text-slate-100 mt-0.5">
                            {{ number_format($trade->entry_price, 2) }}
                        </div>
                    </div>

                    <div class="p-3.5 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-100 dark:border-slate-800">
                        <div class="text-[11px] font-bold text-slate-400 uppercase">Exit Price</div>
                        <div class="text-lg font-extrabold text-slate-800 dark:text-slate-100 mt-0.5">
                            {{ $trade->exit_price !== null ? number_format($trade->exit_price, 2) : 'Open Position' }}
                        </div>
                    </div>

                    <div class="p-3.5 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-100 dark:border-slate-800">
                        <div class="text-[11px] font-bold text-slate-400 uppercase">Stop Loss (SL)</div>
                        <div class="text-lg font-extrabold text-rose-600 dark:text-rose-400 mt-0.5">
                            {{ $trade->stop_loss ? number_format($trade->stop_loss, 2) : '--' }}
                        </div>
                    </div>

                    <div class="p-3.5 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-100 dark:border-slate-800">
                        <div class="text-[11px] font-bold text-slate-400 uppercase">Target</div>
                        <div class="text-lg font-extrabold text-emerald-600 dark:text-emerald-400 mt-0.5">
                            {{ $trade->target ? number_format($trade->target, 2) : '--' }}
                        </div>
                    </div>

                    <div class="p-3.5 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-100 dark:border-slate-800">
                        <div class="text-[11px] font-bold text-slate-400 uppercase">Trade Duration</div>
                        <div class="text-base font-bold text-slate-700 dark:text-slate-300 mt-0.5">
                            @if($trade->entry_datetime && $trade->exit_datetime)
                                {{ $trade->entry_datetime->diffForHumans($trade->exit_datetime, ['parts' => 2, 'short' => true, 'syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE]) }}
                            @else
                                --
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chart Screenshot Section -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200/80 dark:border-slate-800">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <i class="fa-solid fa-image text-indigo-500"></i> Chart Screenshot
                    </h2>
                    @if($trade->screenshot)
                        <span class="text-xs px-2.5 py-1 rounded-full bg-cyan-50 dark:bg-cyan-950/70 text-cyan-700 dark:text-cyan-300 border border-cyan-200 dark:border-cyan-800 font-bold">
                            Optimized WebP Image
                        </span>
                    @endif
                </div>

                @if($trade->screenshot)
                    <div class="rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-800 bg-slate-950/40 group relative">
                        <a href="{{ $trade->screenshot_url }}" target="_blank" title="Click to view full resolution image">
                            <img src="{{ $trade->screenshot_url }}" alt="Trade Chart" class="w-full object-contain max-h-[500px] hover:scale-[1.01] transition-transform duration-200">
                        </a>
                        <div class="p-3 bg-slate-50 dark:bg-slate-800 text-xs text-slate-500 dark:text-slate-400 flex items-center justify-between border-t border-slate-200 dark:border-slate-700">
                            <span><i class="fa-solid fa-file-image mr-1"></i> {{ basename($trade->screenshot) }}</span>
                            <a href="{{ $trade->screenshot_url }}" target="_blank" class="text-indigo-600 dark:text-indigo-400 font-bold hover:underline">
                                <i class="fa-solid fa-arrow-up-right-from-square mr-1"></i> Full View
                            </a>
                        </div>
                    </div>
                @else
                    <div class="p-8 text-center rounded-2xl border-2 border-dashed border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30">
                        <i class="fa-regular fa-image text-4xl text-slate-300 dark:text-slate-600 mb-2"></i>
                        <p class="text-sm font-semibold text-slate-600 dark:text-slate-400">No chart screenshot uploaded for this trade.</p>
                        <a href="{{ route('trades.edit', $trade) }}" class="mt-3 inline-block text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
                            + Add Chart Screenshot
                        </a>
                    </div>
                @endif
            </div>

        </div>

        <!-- Right 1 Col: Lessons, Psychology & Notes -->
        <div class="space-y-6">

            <!-- Lessons Learned -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-7 shadow-sm border border-slate-200/80 dark:border-slate-800">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-lightbulb text-amber-500"></i> Lessons & Review
                </h3>
                @if($trade->lessons)
                    <div class="p-4 rounded-2xl bg-amber-50/60 dark:bg-amber-950/30 border border-amber-200/60 dark:border-amber-900/40 text-sm text-slate-700 dark:text-slate-300 leading-relaxed whitespace-pre-line">
                        {{ $trade->lessons }}
                    </div>
                @else
                    <p class="text-xs text-slate-400 italic">No specific lessons logged yet.</p>
                @endif
            </div>

            <!-- Remarks -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-7 shadow-sm border border-slate-200/80 dark:border-slate-800">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-comment-dots text-indigo-500"></i> Remarks & Notes
                </h3>
                @if($trade->remarks)
                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/60 text-sm text-slate-700 dark:text-slate-300 leading-relaxed whitespace-pre-line">
                        {{ $trade->remarks }}
                    </div>
                @else
                    <p class="text-xs text-slate-400 italic">No additional remarks recorded.</p>
                @endif
            </div>

            <!-- Server Timestamps & Audit -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 shadow-sm border border-slate-200/80 dark:border-slate-800 text-xs text-slate-500 dark:text-slate-400 space-y-2">
                <div class="font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                    Record Audit
                </div>
                <div class="flex justify-between">
                    <span>Journal Stored Time:</span>
                    <span class="font-mono text-slate-800 dark:text-slate-200">{{ $trade->journal_datetime?->format('Y-m-d H:i:s') }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Database ID:</span>
                    <span class="font-mono text-slate-800 dark:text-slate-200">#{{ $trade->id }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Created:</span>
                    <span>{{ $trade->created_at?->diffForHumans() }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Last Updated:</span>
                    <span>{{ $trade->updated_at?->diffForHumans() }}</span>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
