@extends('layouts.app')

@section('title', 'Manage Strategies - MyTrades')

@section('content')
<div class="max-w-5xl mx-auto space-y-8">

    <!-- Header & Add Button -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-400 mb-1">
                <a href="{{ route('trades.index') }}" class="hover:text-indigo-500">Journal</a>
                <span>/</span>
                <span class="text-indigo-600 dark:text-indigo-400">Strategies</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                Trading Strategies
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                Define and manage your setups. These populate your strategy dropdown when logging trades.
            </p>
        </div>
        <button type="button" onclick="document.getElementById('addStrategyForm').classList.toggle('hidden')" class="px-5 py-2.5 text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-500 rounded-xl shadow-md transition-all flex items-center gap-2">
            <i class="fa-solid fa-plus text-xs"></i>
            <span>Add Strategy</span>
        </button>
    </div>

    <!-- Inline Add Strategy Form Card (Collapsible) -->
    <div id="addStrategyForm" class="hidden bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 shadow-sm border-2 border-indigo-200 dark:border-indigo-800">
        <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
            <i class="fa-solid fa-shapes text-indigo-500"></i> New Strategy Definition
        </h2>
        <form method="POST" action="{{ route('strategies.store') }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Strategy Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" required placeholder="e.g. 15-Minute EMA Trend Pullback" class="w-full px-4 py-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Color Accent</label>
                    <input type="color" name="color" value="#4f46e5" class="w-full h-11 p-1 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 cursor-pointer">
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Setup Criteria & Rules (Optional)</label>
                <textarea name="description" rows="2" placeholder="Entry trigger, required indicators, confirmation checklist..." class="w-full px-4 py-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500"></textarea>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="document.getElementById('addStrategyForm').classList.add('hidden')" class="px-4 py-2 text-xs font-bold text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl">Cancel</button>
                <button type="submit" class="px-5 py-2.5 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 rounded-xl shadow">Save Strategy</button>
            </div>
        </form>
    </div>

    <!-- Strategies Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($strategies as $strategy)
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 shadow-sm border border-slate-200/80 dark:border-slate-800 flex flex-col justify-between group hover:border-indigo-300 dark:hover:border-indigo-700 transition-colors">
                <div>
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div class="flex items-center gap-2.5">
                            <span class="w-3.5 h-3.5 rounded-full ring-2 ring-offset-2 ring-slate-200 dark:ring-slate-800 flex-shrink-0" style="background-color: {{ $strategy->color }}"></span>
                            <h3 class="font-bold text-base text-slate-900 dark:text-white leading-snug">
                                {{ $strategy->name }}
                            </h3>
                        </div>
                        <span class="px-2.5 py-1 text-[11px] font-extrabold rounded-md bg-indigo-50 dark:bg-indigo-950/70 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800/80 whitespace-nowrap">
                            {{ $strategy->trades_count }} trades
                        </span>
                    </div>

                    <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-3 leading-relaxed min-h-[3rem]">
                        {{ $strategy->description ?: 'No detailed setup description added.' }}
                    </p>
                </div>

                <div class="pt-4 mt-4 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between">
                    <a href="{{ route('trades.index', ['strategy_id' => $strategy->id]) }}" class="text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
                        View Trades &rarr;
                    </a>

                    <form method="POST" action="{{ route('strategies.destroy', $strategy) }}" onsubmit="return confirm('Delete strategy \'{{ $strategy->name }}\'? Trades using this strategy will keep their logged name.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-slate-800 transition-colors" title="Delete strategy">
                            <i class="fa-solid fa-trash text-xs"></i>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="sm:col-span-3 py-16 text-center bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800">
                <i class="fa-solid fa-shapes text-4xl text-slate-300 dark:text-slate-600 mb-3"></i>
                <h3 class="font-bold text-slate-700 dark:text-slate-300">No Strategies Configured Yet</h3>
                <p class="text-xs text-slate-400 mt-1 mb-4">Add your favorite setups so you can tag your trades with them.</p>
                <button type="button" onclick="document.getElementById('addStrategyForm').classList.remove('hidden')" class="px-4 py-2 text-xs font-bold text-white bg-indigo-600 rounded-xl">
                    Create First Strategy
                </button>
            </div>
        @endforelse
    </div>

</div>
@endsection
