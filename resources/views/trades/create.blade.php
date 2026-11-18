@extends('layouts.app')

@section('title', 'Log New Trade - MyTrades Trading Journal')

@section('content')
<div class="max-w-4xl mx-auto my-4 sm:my-8">

    <!-- Top Breadcrumb & Title -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-400 mb-1">
                <a href="{{ route('trades.index') }}" class="hover:text-indigo-500">Journal</a>
                <span>/</span>
                <span class="text-indigo-600 dark:text-indigo-400">New Entry</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                Log Journal Entry
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                Effortless trade logging with instant P&L calculation and automatic WebP image optimization.
            </p>
        </div>
        <a href="{{ route('trades.index') }}" class="px-4 py-2.5 text-sm font-semibold rounded-xl border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
            <i class="fa-solid fa-xmark mr-1.5"></i> Cancel
        </a>
    </div>

    <!-- Main Form Card -->
    <form id="tradeForm" method="POST" action="{{ route('trades.store') }}" enctype="multipart/form-data" class="space-y-8">
        @csrf

        <!-- 1. Instrument & Direction Card -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200/80 dark:border-slate-800">
            <div class="flex items-center gap-3 pb-4 mb-6 border-b border-slate-100 dark:border-slate-800">
                <span class="w-8 h-8 rounded-xl bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-sm">1</span>
                <div>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">Trade Setup & Market Direction</h2>
                    <p class="text-xs text-slate-400">Select what you traded and whether you were Long or Short</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Instrument -->
                <div>
                    <label for="instrument" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">
                        Instrument / Ticker <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="text" 
                               id="instrument" 
                               name="instrument" 
                               value="{{ old('instrument') }}" 
                               required 
                               placeholder="e.g. NIFTY, BANKNIFTY, RELIANCE, BTC/USDT" 
                               class="w-full px-4 py-3.5 text-base font-semibold uppercase rounded-xl border @error('instrument') border-rose-500 ring-1 ring-rose-500 @else border-slate-300 dark:border-slate-700 @enderror bg-white dark:bg-slate-800 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                    </div>
                    <!-- Quick Instrument Suggestions -->
                    <div class="flex flex-wrap gap-1.5 mt-2.5">
                        <span class="text-[11px] text-slate-400 self-center mr-1">Quick picks:</span>
                        @foreach(['NIFTY', 'BANKNIFTY', 'RELIANCE', 'BTC/USDT', 'GOLD'] as $quick)
                            <button type="button" onclick="document.getElementById('instrument').value='{{ $quick }}'" class="text-[11px] font-semibold px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-indigo-50 hover:text-indigo-600 transition-colors">
                                {{ $quick }}
                            </button>
                        @endforeach
                    </div>
                    @error('instrument')
                        <p class="mt-1.5 text-xs font-semibold text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Direction (BUY / SELL) -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">
                        Position Direction <span class="text-rose-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="cursor-pointer">
                            <input type="radio" name="direction" value="BUY" class="peer sr-only" {{ old('direction', 'BUY') === 'BUY' ? 'checked' : '' }} onchange="calculatePnl()">
                            <div class="py-3.5 px-4 rounded-xl border-2 border-slate-200 dark:border-slate-700 peer-checked:border-emerald-500 peer-checked:bg-emerald-50 dark:peer-checked:bg-emerald-950/40 peer-checked:text-emerald-700 dark:peer-checked:text-emerald-300 flex items-center justify-center gap-2 font-bold text-base transition-all">
                                <i class="fa-solid fa-arrow-trend-up text-emerald-500"></i>
                                <span>LONG (Buy)</span>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="direction" value="SELL" class="peer sr-only" {{ old('direction') === 'SELL' ? 'checked' : '' }} onchange="calculatePnl()">
                            <div class="py-3.5 px-4 rounded-xl border-2 border-slate-200 dark:border-slate-700 peer-checked:border-rose-500 peer-checked:bg-rose-50 dark:peer-checked:bg-rose-950/40 peer-checked:text-rose-700 dark:peer-checked:text-rose-300 flex items-center justify-center gap-2 font-bold text-base transition-all">
                                <i class="fa-solid fa-arrow-trend-down text-rose-500"></i>
                                <span>SHORT (Sell)</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Entry Date & Time -->
                <div>
                    <label for="entry_datetime" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">
                        Entry Date & Time <span class="text-rose-500">*</span>
                    </label>
                    <input type="datetime-local" 
                           id="entry_datetime" 
                           name="entry_datetime" 
                           value="{{ old('entry_datetime', now()->format('Y-m-d\TH:i')) }}" 
                           required 
                           class="w-full px-4 py-3.5 text-base rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                    @error('entry_datetime')
                        <p class="mt-1.5 text-xs font-semibold text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Exit Date & Time -->
                <div>
                    <label for="exit_datetime" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">
                        Exit Date & Time
                    </label>
                    <input type="datetime-local" 
                           id="exit_datetime" 
                           name="exit_datetime" 
                           value="{{ old('exit_datetime', now()->format('Y-m-d\TH:i')) }}" 
                           class="w-full px-4 py-3.5 text-base rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                    @error('exit_datetime')
                        <p class="mt-1.5 text-xs font-semibold text-rose-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- 2. Execution Prices & Live P&L Calculator -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200/80 dark:border-slate-800">
            <div class="flex items-center gap-3 pb-4 mb-6 border-b border-slate-100 dark:border-slate-800">
                <span class="w-8 h-8 rounded-xl bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-sm">2</span>
                <div>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">Execution Numbers & Live Calculator</h2>
                    <p class="text-xs text-slate-400">Values automatically compute your Gross and Net P&L in real time</p>
                </div>
            </div>

            <!-- Inputs Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                <!-- Quantity -->
                <div>
                    <label for="quantity" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">
                        Quantity <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" 
                           step="any" 
                           id="quantity" 
                           name="quantity" 
                           value="{{ old('quantity') }}" 
                           required 
                           oninput="calculatePnl()" 
                           placeholder="e.g. 50" 
                           class="w-full px-4 py-3.5 text-base font-semibold rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                </div>

                <!-- Entry Price -->
                <div>
                    <label for="entry_price" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">
                        Entry Price <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" 
                           step="0.01" 
                           id="entry_price" 
                           name="entry_price" 
                           value="{{ old('entry_price') }}" 
                           required 
                           oninput="calculatePnl()" 
                           placeholder="e.g. 21500.50" 
                           class="w-full px-4 py-3.5 text-base font-semibold rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                </div>

                <!-- Exit Price -->
                <div>
                    <label for="exit_price" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">
                        Exit Price
                    </label>
                    <input type="number" 
                           step="0.01" 
                           id="exit_price" 
                           name="exit_price" 
                           value="{{ old('exit_price') }}" 
                           oninput="calculatePnl()" 
                           placeholder="e.g. 21620.00" 
                           class="w-full px-4 py-3.5 text-base font-semibold rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                </div>

                <!-- Stop Loss -->
                <div>
                    <label for="stop_loss" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">
                        Stop Loss (SL)
                    </label>
                    <input type="number" 
                           step="0.01" 
                           id="stop_loss" 
                           name="stop_loss" 
                           value="{{ old('stop_loss') }}" 
                           oninput="calculatePnl()" 
                           placeholder="e.g. 21450.00" 
                           class="w-full px-4 py-3.5 text-base font-semibold rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                </div>

                <!-- Target -->
                <div>
                    <label for="target" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">
                        Target Price
                    </label>
                    <input type="number" 
                           step="0.01" 
                           id="target" 
                           name="target" 
                           value="{{ old('target') }}" 
                           oninput="calculatePnl()" 
                           placeholder="e.g. 21650.00" 
                           class="w-full px-4 py-3.5 text-base font-semibold rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                </div>

                <!-- Charges / Brokerage / Taxes -->
                <div>
                    <label for="charges" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">
                        Charges & Brokerage
                    </label>
                    <input type="number" 
                           step="0.01" 
                           id="charges" 
                           name="charges" 
                           value="{{ old('charges', '40.00') }}" 
                           oninput="calculatePnl()" 
                           placeholder="e.g. 40.00" 
                           class="w-full px-4 py-3.5 text-base font-semibold rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                </div>
            </div>

            <!-- Live Calculation Banner -->
            <div class="mt-6 p-5 rounded-2xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 w-full">
                    <div>
                        <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Gross P&L</div>
                        <div id="displayGrossPnl" class="text-xl font-extrabold text-slate-700 dark:text-slate-200">
                            0.00
                        </div>
                    </div>
                    <div>
                        <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Charges</div>
                        <div id="displayCharges" class="text-xl font-extrabold text-slate-600 dark:text-slate-300">
                            40.00
                        </div>
                    </div>
                    <div>
                        <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Net P&L</div>
                        <div id="displayNetPnl" class="text-2xl font-black text-slate-800 dark:text-slate-100">
                            -40.00
                        </div>
                    </div>
                    <div>
                        <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Planned R:R</div>
                        <div id="displayRR" class="text-base font-bold text-indigo-600 dark:text-indigo-400">
                            --
                        </div>
                    </div>
                </div>

                <!-- Hidden inputs passed with form if auto-calculated -->
                <input type="hidden" id="gross_pnl" name="gross_pnl" value="0.00">
                <input type="hidden" id="net_pnl" name="net_pnl" value="-40.00">
            </div>
        </div>

        <!-- 3. Strategy, Review & WebP Screenshot -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200/80 dark:border-slate-800">
            <div class="flex items-center gap-3 pb-4 mb-6 border-b border-slate-100 dark:border-slate-800">
                <span class="w-8 h-8 rounded-xl bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-sm">3</span>
                <div>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">Strategy, Review & Chart Screenshot</h2>
                    <p class="text-xs text-slate-400">Strategy dropdown of your set values & auto-optimized WebP chart</p>
                </div>
            </div>

            <div class="space-y-6">
                <!-- Strategy & Exit Reason Row -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    
                    <!-- Strategy Dropdown -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label for="strategy_id" class="block text-sm font-bold text-slate-700 dark:text-slate-300">
                                Strategy (User Set Values)
                            </label>
                            <button type="button" onclick="openNewStrategyModal()" class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                                + Add New Strategy
                            </button>
                        </div>
                        <select id="strategy_id" 
                                name="strategy_id" 
                                class="w-full px-4 py-3.5 text-base font-semibold rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                            <option value="">-- Select or Add Strategy --</option>
                            @foreach($strategies as $strat)
                                <option value="{{ $strat->id }}" {{ old('strategy_id') == $strat->id ? 'selected' : '' }}>
                                    {{ $strat->name }}
                                </option>
                            @endforeach
                        </select>
                        <input type="text" 
                               id="new_strategy_name" 
                               name="new_strategy_name" 
                               placeholder="Or type a quick new strategy here..." 
                               class="hidden mt-2 w-full px-4 py-2.5 text-sm rounded-xl border border-indigo-300 bg-indigo-50/50 dark:bg-indigo-950/30 text-indigo-900 dark:text-indigo-100 focus:outline-none">
                    </div>

                    <!-- Exit Reason -->
                    <div>
                        <label for="exit_reason" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">
                            Exit Reason
                        </label>
                        <select id="exit_reason" 
                                name="exit_reason" 
                                class="w-full px-4 py-3.5 text-base font-semibold rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                            <option value="">-- Select Exit Reason --</option>
                            @foreach($exitReasons as $reason)
                                <option value="{{ $reason }}" {{ old('exit_reason') === $reason ? 'selected' : '' }}>
                                    {{ $reason }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Screenshot Upload with WebP conversion notice & instant preview -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">
                        Chart Screenshot
                        <span class="text-xs font-normal text-indigo-600 dark:text-indigo-400 ml-1">
                            (Automatically converted to WebP format to save server space)
                        </span>
                    </label>
                    
                    <div class="border-2 border-dashed border-slate-300 dark:border-slate-700 hover:border-indigo-500 dark:hover:border-indigo-400 rounded-2xl p-6 text-center cursor-pointer transition-colors relative bg-slate-50/50 dark:bg-slate-800/40" onclick="document.getElementById('screenshot').click()">
                        <input type="file" 
                               id="screenshot" 
                               name="screenshot" 
                               accept="image/png,image/jpeg,image/webp" 
                               class="sr-only" 
                               onchange="previewImage(this)">
                        
                        <div id="uploadPlaceholder">
                            <i class="fa-solid fa-cloud-arrow-up text-3xl text-slate-400 mb-2"></i>
                            <div class="text-sm font-bold text-slate-700 dark:text-slate-300">
                                Click or drag & drop trade chart image here
                            </div>
                            <div class="text-xs text-slate-400 mt-1">
                                PNG, JPG, or WEBP up to 10MB • Preserves crystal clear chart clarity while compressing file size
                            </div>
                        </div>

                        <!-- Image Preview Box -->
                        <div id="imagePreviewBox" class="hidden">
                            <img id="imagePreview" src="#" alt="Screenshot preview" class="max-h-72 mx-auto rounded-xl shadow-md border border-slate-200 dark:border-slate-700">
                            <button type="button" onclick="event.stopPropagation(); clearImagePreview();" class="mt-3 px-3 py-1 text-xs font-bold text-rose-600 bg-rose-50 dark:bg-rose-950/60 rounded-lg border border-rose-200 dark:border-rose-800">
                                <i class="fa-solid fa-trash mr-1"></i> Remove Image
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Lessons & Psychological Notes -->
                <div>
                    <label for="lessons" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">
                        Lessons Learned & Execution Review
                    </label>
                    <textarea id="lessons" 
                              name="lessons" 
                              rows="3" 
                              placeholder="What went well? Did you follow your trading rules? What emotional state were you in?" 
                              class="w-full px-4 py-3.5 text-base rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">{{ old('lessons') }}</textarea>
                </div>

                <!-- Remarks -->
                <div>
                    <label for="remarks" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">
                        Remarks & Additional Notes
                    </label>
                    <textarea id="remarks" 
                              name="remarks" 
                              rows="2" 
                              placeholder="Market environment, high impact economic data, key support/resistance levels..." 
                              class="w-full px-4 py-3.5 text-base rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">{{ old('remarks') }}</textarea>
                </div>
            </div>
        </div>

        <!-- Submit & Save Buttons -->
        <div class="flex items-center justify-end gap-4 pt-4">
            <a href="{{ route('trades.index') }}" class="px-6 py-4 text-sm font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-800 rounded-xl transition-colors">
                Cancel
            </a>
            <button type="submit" class="py-4 px-8 text-base font-bold text-white bg-indigo-600 hover:bg-indigo-500 rounded-xl shadow-lg shadow-indigo-600/30 hover:shadow-indigo-600/40 transform active:scale-[0.99] transition-all flex items-center gap-2">
                <i class="fa-solid fa-check"></i>
                <span>Save Journal Entry</span>
            </button>
        </div>
    </form>
</div>

<!-- Quick Add Strategy Modal -->
<div id="strategyModal" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 max-w-md w-full border border-slate-200 dark:border-slate-800 shadow-2xl">
        <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-100 dark:border-slate-800">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">Add New Trading Strategy</h3>
            <button type="button" onclick="closeNewStrategyModal()" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
        </div>
        <div class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 mb-1.5">Strategy Name</label>
                <input type="text" id="modalStratName" placeholder="e.g. 5-Min Opening Drive" class="w-full px-4 py-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 mb-1.5">Strategy Description (optional)</label>
                <input type="text" id="modalStratDesc" placeholder="Brief rules / trigger" class="w-full px-4 py-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm">
            </div>
            <div class="flex justify-end gap-3 pt-3">
                <button type="button" onclick="closeNewStrategyModal()" class="px-4 py-2.5 text-xs font-bold text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl">Cancel</button>
                <button type="button" onclick="submitNewStrategyAjax()" class="px-5 py-2.5 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 rounded-xl shadow">Create Strategy</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function calculatePnl() {
        const qty = parseFloat(document.getElementById('quantity').value) || 0;
        const entry = parseFloat(document.getElementById('entry_price').value) || 0;
        const exit = parseFloat(document.getElementById('exit_price').value);
        const charges = parseFloat(document.getElementById('charges').value) || 0;
        const direction = document.querySelector('input[name="direction"]:checked')?.value || 'BUY';

        let gross = 0;
        if (!isNaN(exit) && exit > 0 && qty > 0 && entry > 0) {
            if (direction === 'BUY') {
                gross = (exit - entry) * qty;
            } else {
                gross = (entry - exit) * qty;
            }
        }

        const net = gross - charges;

        // Update hidden inputs
        document.getElementById('gross_pnl').value = gross.toFixed(2);
        document.getElementById('net_pnl').value = net.toFixed(2);

        // Update visual display
        const displayGross = document.getElementById('displayGrossPnl');
        const displayNet = document.getElementById('displayNetPnl');
        const displayCharges = document.getElementById('displayCharges');

        displayCharges.textContent = charges.toFixed(2);

        displayGross.textContent = (gross >= 0 ? '+' : '') + gross.toFixed(2);
        displayNet.textContent = (net >= 0 ? '+' : '') + net.toFixed(2);

        if (net > 0) {
            displayNet.className = "text-2xl font-black text-emerald-600 dark:text-emerald-400";
            displayGross.className = "text-xl font-extrabold text-emerald-600 dark:text-emerald-400";
        } else if (net < 0) {
            displayNet.className = "text-2xl font-black text-rose-600 dark:text-rose-400";
            displayGross.className = "text-xl font-extrabold text-rose-600 dark:text-rose-400";
        } else {
            displayNet.className = "text-2xl font-black text-slate-800 dark:text-slate-100";
            displayGross.className = "text-xl font-extrabold text-slate-700 dark:text-slate-200";
        }

        // Calculate Risk / Reward
        const sl = parseFloat(document.getElementById('stop_loss').value);
        const target = parseFloat(document.getElementById('target').value);
        const rrDisplay = document.getElementById('displayRR');

        if (!isNaN(entry) && !isNaN(sl) && !isNaN(target) && entry > 0) {
            let risk = 0, reward = 0;
            if (direction === 'BUY') {
                risk = entry - sl;
                reward = target - entry;
            } else {
                risk = sl - entry;
                reward = entry - target;
            }

            if (risk > 0 && reward > 0) {
                const ratio = (reward / risk).toFixed(1);
                rrDisplay.textContent = `1 : ${ratio}`;
            } else {
                rrDisplay.textContent = '--';
            }
        } else {
            rrDisplay.textContent = '--';
        }
    }

    function previewImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('imagePreview').src = e.target.result;
                document.getElementById('imagePreviewBox').classList.remove('hidden');
                document.getElementById('uploadPlaceholder').classList.add('hidden');
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function clearImagePreview() {
        document.getElementById('screenshot').value = '';
        document.getElementById('imagePreview').src = '#';
        document.getElementById('imagePreviewBox').classList.add('hidden');
        document.getElementById('uploadPlaceholder').classList.remove('hidden');
    }

    function openNewStrategyModal() {
        document.getElementById('strategyModal').classList.remove('hidden');
        document.getElementById('modalStratName').focus();
    }

    function closeNewStrategyModal() {
        document.getElementById('strategyModal').classList.add('hidden');
    }

    function submitNewStrategyAjax() {
        const name = document.getElementById('modalStratName').value.trim();
        const desc = document.getElementById('modalStratDesc').value.trim();
        if (!name) return alert('Please enter a strategy name.');

        fetch('{{ route("strategies.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ name: name, description: desc })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success && data.strategy) {
                const select = document.getElementById('strategy_id');
                const opt = document.createElement('option');
                opt.value = data.strategy.id;
                opt.textContent = data.strategy.name;
                opt.selected = true;
                select.appendChild(opt);
                closeNewStrategyModal();
            } else {
                alert('Could not save strategy. Please try again.');
            }
        })
        .catch(err => {
            console.error(err);
            // Fallback: show input field
            document.getElementById('new_strategy_name').classList.remove('hidden');
            document.getElementById('new_strategy_name').value = name;
            closeNewStrategyModal();
        });
    }

    // Run initial calculate
    calculatePnl();
</script>
@endpush
@endsection
