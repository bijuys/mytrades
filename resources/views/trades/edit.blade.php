@extends('layouts.app')

@section('title', 'Edit Trade #' . $trade->id . ' - MyTrades Trading Journal')

@section('content')
<div class="max-w-4xl mx-auto my-4 sm:my-8">

    <!-- Top Breadcrumb & Title -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-400 mb-1">
                <a href="{{ route('trades.index') }}" class="hover:text-indigo-500">Journal</a>
                <span>/</span>
                <a href="{{ route('trades.show', $trade) }}" class="hover:text-indigo-500">Trade #{{ $trade->id }}</a>
                <span>/</span>
                <span class="text-indigo-600 dark:text-indigo-400">Edit</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                Edit Journal Entry #{{ $trade->id }}
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                Modify trade details, exit levels, or update your screenshot.
            </p>
        </div>
        <a href="{{ route('trades.show', $trade) }}" class="px-4 py-2.5 text-sm font-semibold rounded-xl border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
            <i class="fa-solid fa-xmark mr-1.5"></i> Cancel
        </a>
    </div>

    <!-- Main Form Card -->
    <form id="tradeForm" method="POST" action="{{ route('trades.update', $trade) }}" enctype="multipart/form-data" class="space-y-8">
        @csrf
        @method('PUT')

        <!-- 1. Instrument & Direction Card -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200/80 dark:border-slate-800">
            <div class="flex items-center gap-3 pb-4 mb-6 border-b border-slate-100 dark:border-slate-800">
                <span class="w-8 h-8 rounded-xl bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-sm">1</span>
                <div>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">Trade Setup & Market Direction</h2>
                    <p class="text-xs text-slate-400">Instrument and entry position</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Instrument -->
                <div>
                    <label for="instrument" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">
                        Instrument / Ticker <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           id="instrument" 
                           name="instrument" 
                           value="{{ old('instrument', $trade->instrument) }}" 
                           required 
                           class="w-full px-4 py-3.5 text-base font-semibold uppercase rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                </div>

                <!-- Direction -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">
                        Position Direction <span class="text-rose-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="cursor-pointer">
                            <input type="radio" name="direction" value="BUY" class="peer sr-only" {{ old('direction', $trade->direction) === 'BUY' ? 'checked' : '' }} onchange="calculatePnl()">
                            <div class="py-3.5 px-4 rounded-xl border-2 border-slate-200 dark:border-slate-700 peer-checked:border-emerald-500 peer-checked:bg-emerald-50 dark:peer-checked:bg-emerald-950/40 peer-checked:text-emerald-700 dark:peer-checked:text-emerald-300 flex items-center justify-center gap-2 font-bold text-base transition-all">
                                <i class="fa-solid fa-arrow-trend-up text-emerald-500"></i>
                                <span>LONG (Buy)</span>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="direction" value="SELL" class="peer sr-only" {{ old('direction', $trade->direction) === 'SELL' ? 'checked' : '' }} onchange="calculatePnl()">
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
                           value="{{ old('entry_datetime', $trade->entry_datetime?->format('Y-m-d\TH:i')) }}" 
                           required 
                           class="w-full px-4 py-3.5 text-base rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                </div>

                <!-- Exit Date & Time -->
                <div>
                    <label for="exit_datetime" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">
                        Exit Date & Time
                    </label>
                    <input type="datetime-local" 
                           id="exit_datetime" 
                           name="exit_datetime" 
                           value="{{ old('exit_datetime', $trade->exit_datetime?->format('Y-m-d\TH:i')) }}" 
                           class="w-full px-4 py-3.5 text-base rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
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
                           value="{{ old('quantity', $trade->quantity) }}" 
                           required 
                           oninput="calculatePnl()" 
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
                           value="{{ old('entry_price', $trade->entry_price) }}" 
                           required 
                           oninput="calculatePnl()" 
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
                           value="{{ old('exit_price', $trade->exit_price) }}" 
                           oninput="calculatePnl()" 
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
                           value="{{ old('stop_loss', $trade->stop_loss) }}" 
                           oninput="calculatePnl()" 
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
                           value="{{ old('target', $trade->target) }}" 
                           oninput="calculatePnl()" 
                           class="w-full px-4 py-3.5 text-base font-semibold rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                </div>

                <!-- Charges -->
                <div>
                    <label for="charges" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">
                        Charges & Brokerage
                    </label>
                    <input type="number" 
                           step="0.01" 
                           id="charges" 
                           name="charges" 
                           value="{{ old('charges', $trade->charges) }}" 
                           oninput="calculatePnl()" 
                           class="w-full px-4 py-3.5 text-base font-semibold rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                </div>
            </div>

            <!-- Live Calculation Banner -->
            <div class="mt-6 p-5 rounded-2xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 w-full">
                    <div>
                        <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Gross P&L</div>
                        <div id="displayGrossPnl" class="text-xl font-extrabold text-slate-700 dark:text-slate-200">
                            {{ $trade->gross_pnl >= 0 ? '+' : '' }}{{ number_format($trade->gross_pnl, 2) }}
                        </div>
                    </div>
                    <div>
                        <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Charges</div>
                        <div id="displayCharges" class="text-xl font-extrabold text-slate-600 dark:text-slate-300">
                            {{ number_format($trade->charges, 2) }}
                        </div>
                    </div>
                    <div>
                        <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Net P&L</div>
                        <div id="displayNetPnl" class="text-2xl font-black {{ $trade->net_pnl > 0 ? 'text-emerald-600 dark:text-emerald-400' : ($trade->net_pnl < 0 ? 'text-rose-600 dark:text-rose-400' : 'text-slate-800 dark:text-slate-100') }}">
                            {{ $trade->net_pnl >= 0 ? '+' : '' }}{{ number_format($trade->net_pnl, 2) }}
                        </div>
                    </div>
                    <div>
                        <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Planned R:R</div>
                        <div id="displayRR" class="text-base font-bold text-indigo-600 dark:text-indigo-400">
                            --
                        </div>
                    </div>
                </div>

                <input type="hidden" id="gross_pnl" name="gross_pnl" value="{{ $trade->gross_pnl }}">
                <input type="hidden" id="net_pnl" name="net_pnl" value="{{ $trade->net_pnl }}">
            </div>
        </div>

        <!-- 3. Strategy, Review & WebP Screenshot -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200/80 dark:border-slate-800">
            <div class="flex items-center gap-3 pb-4 mb-6 border-b border-slate-100 dark:border-slate-800">
                <span class="w-8 h-8 rounded-xl bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-sm">3</span>
                <div>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">Strategy, Review & Chart Screenshot</h2>
                    <p class="text-xs text-slate-400">Strategy selection & chart image</p>
                </div>
            </div>

            <div class="space-y-6">
                <!-- Strategy & Exit Reason Row -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label for="strategy_id" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">
                            Strategy (User Set Values)
                        </label>
                        <select id="strategy_id" 
                                name="strategy_id" 
                                class="w-full px-4 py-3.5 text-base font-semibold rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                            <option value="">-- Select Strategy --</option>
                            @foreach($strategies as $strat)
                                <option value="{{ $strat->id }}" {{ old('strategy_id', $trade->strategy_id) == $strat->id ? 'selected' : '' }}>
                                    {{ $strat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="exit_reason" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">
                            Exit Reason
                        </label>
                        <select id="exit_reason" 
                                name="exit_reason" 
                                class="w-full px-4 py-3.5 text-base font-semibold rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                            <option value="">-- Select Exit Reason --</option>
                            @foreach($exitReasons as $reason)
                                <option value="{{ $reason }}" {{ old('exit_reason', $trade->exit_reason) === $reason ? 'selected' : '' }}>
                                    {{ $reason }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Screenshot with current preview -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">
                        Chart Screenshot (WebP Optimized)
                    </label>

                    @if($trade->screenshot)
                        <div class="mb-4 p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 flex flex-col sm:flex-row items-center gap-4">
                            <img src="{{ $trade->screenshot_url }}" alt="Current Screenshot" class="h-28 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 object-cover">
                            <div class="text-xs text-slate-500 dark:text-slate-400 flex-1">
                                <span class="font-bold text-slate-800 dark:text-slate-200">Current screenshot saved in WebP:</span><br>
                                <code>{{ basename($trade->screenshot) }}</code><br>
                                <label class="inline-flex items-center gap-2 mt-2 text-rose-600 font-semibold cursor-pointer">
                                    <input type="checkbox" name="remove_screenshot" value="1" class="rounded text-rose-600">
                                    <span>Remove this screenshot</span>
                                </label>
                            </div>
                        </div>
                    @endif

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
                                {{ $trade->screenshot ? 'Click to replace screenshot with a new chart' : 'Click or drop chart image to upload' }}
                            </div>
                            <div class="text-xs text-slate-400 mt-1">
                                PNG, JPG, or WEBP (auto-converted to WebP)
                            </div>
                        </div>

                        <div id="imagePreviewBox" class="hidden">
                            <img id="imagePreview" src="#" alt="Screenshot preview" class="max-h-72 mx-auto rounded-xl shadow-md border border-slate-200 dark:border-slate-700">
                            <button type="button" onclick="event.stopPropagation(); clearImagePreview();" class="mt-3 px-3 py-1 text-xs font-bold text-rose-600 bg-rose-50 dark:bg-rose-950/60 rounded-lg border border-rose-200 dark:border-rose-800">
                                <i class="fa-solid fa-trash mr-1"></i> Cancel New Image
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Lessons -->
                <div>
                    <label for="lessons" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">
                        Lessons Learned & Execution Review
                    </label>
                    <textarea id="lessons" 
                              name="lessons" 
                              rows="3" 
                              class="w-full px-4 py-3.5 text-base rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">{{ old('lessons', $trade->lessons) }}</textarea>
                </div>

                <!-- Remarks -->
                <div>
                    <label for="remarks" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">
                        Remarks & Additional Notes
                    </label>
                    <textarea id="remarks" 
                              name="remarks" 
                              rows="2" 
                              class="w-full px-4 py-3.5 text-base rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">{{ old('remarks', $trade->remarks) }}</textarea>
                </div>
            </div>
        </div>

        <!-- Submit & Save Buttons -->
        <div class="flex items-center justify-end gap-4 pt-4">
            <a href="{{ route('trades.show', $trade) }}" class="px-6 py-4 text-sm font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-800 rounded-xl transition-colors">
                Cancel
            </a>
            <button type="submit" class="py-4 px-8 text-base font-bold text-white bg-indigo-600 hover:bg-indigo-500 rounded-xl shadow-lg shadow-indigo-600/30 hover:shadow-indigo-600/40 transform active:scale-[0.99] transition-all flex items-center gap-2">
                <i class="fa-solid fa-check"></i>
                <span>Update Journal Entry</span>
            </button>
        </div>
    </form>
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

        document.getElementById('gross_pnl').value = gross.toFixed(2);
        document.getElementById('net_pnl').value = net.toFixed(2);

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

    calculatePnl();
</script>
@endpush
@endsection
