<?php

namespace App\Http\Controllers;

use App\Models\Strategy;
use App\Models\Trade;
use App\Services\ImageService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TradeJournalController extends Controller
{
    public function __construct(
        protected ImageService $imageService
    ) {}

    /**
     * Display a listing of past journal entries.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();

        $query = Trade::with('strategy')
            ->where('user_id', $user->id);

        // Search by instrument, notes, or strategy name
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('instrument', 'like', "%{$search}%")
                    ->orWhere('strategy_name', 'like', "%{$search}%")
                    ->orWhere('exit_reason', 'like', "%{$search}%")
                    ->orWhere('lessons', 'like', "%{$search}%")
                    ->orWhere('remarks', 'like', "%{$search}%");
            });
        }

        // Filter by Strategy
        if ($strategyId = $request->input('strategy_id')) {
            $query->where('strategy_id', $strategyId);
        }

        // Filter by Result (Win / Loss / Break-even)
        if ($result = $request->input('result')) {
            if ($result === 'win') {
                $query->where('net_pnl', '>', 0);
            } elseif ($result === 'loss') {
                $query->where('net_pnl', '<', 0);
            } elseif ($result === 'be') {
                $query->where('net_pnl', '=', 0);
            }
        }

        // Filter by Direction
        if ($direction = $request->input('direction')) {
            $query->where('direction', strtoupper($direction));
        }

        // Date range filters
        if ($dateRange = $request->input('date_range')) {
            if ($dateRange === 'today') {
                $query->whereDate('entry_datetime', Carbon::today());
            } elseif ($dateRange === 'week') {
                $query->whereBetween('entry_datetime', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
            } elseif ($dateRange === 'month') {
                $query->whereMonth('entry_datetime', Carbon::now()->month)
                    ->whereYear('entry_datetime', Carbon::now()->year);
            } elseif ($dateRange === 'year') {
                $query->whereYear('entry_datetime', Carbon::now()->year);
            }
        }

        if ($fromDate = $request->input('from_date')) {
            $query->whereDate('entry_datetime', '>=', $fromDate);
        }
        if ($toDate = $request->input('to_date')) {
            $query->whereDate('entry_datetime', '<=', $toDate);
        }

        // Summary calculations for the filtered set
        $allMatchingTrades = (clone $query)->get();
        $totalTrades = $allMatchingTrades->count();
        $totalNetPnl = $allMatchingTrades->sum('net_pnl');
        $totalGrossPnl = $allMatchingTrades->sum('gross_pnl');
        $totalCharges = $allMatchingTrades->sum('charges');
        $wins = $allMatchingTrades->where('net_pnl', '>', 0);
        $losses = $allMatchingTrades->where('net_pnl', '<', 0);
        $winCount = $wins->count();
        $lossCount = $losses->count();
        $winRate = $totalTrades > 0 ? round(($winCount / $totalTrades) * 100, 1) : 0;

        $grossWins = $wins->sum('net_pnl');
        $grossLosses = abs($losses->sum('net_pnl'));
        $profitFactor = $grossLosses > 0 ? round($grossWins / $grossLosses, 2) : ($grossWins > 0 ? 99.99 : 0);

        // Sorting & Pagination
        $sort = $request->input('sort', 'entry_datetime');
        $direction = $request->input('order', 'desc');
        $trades = $query->orderBy($sort, $direction)->paginate(15)->withQueryString();

        $userStrategies = Strategy::where('user_id', $user->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('trades.index', compact(
            'trades',
            'userStrategies',
            'totalTrades',
            'totalNetPnl',
            'totalGrossPnl',
            'totalCharges',
            'winCount',
            'lossCount',
            'winRate',
            'profitFactor'
        ));
    }

    /**
     * Show form to create a new Journal entry.
     */
    public function create(): View
    {
        $user = Auth::user();
        $strategies = Strategy::where('user_id', $user->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $commonInstruments = [
            'NIFTY 50', 'BANK NIFTY', 'FIN NIFTY', 'MIDCP NIFTY',
            'RELIANCE', 'HDFCBANK', 'ICICIBANK', 'INFY', 'TCS', 'TATAMOTORS', 'SBIN',
            'BTC/USDT', 'ETH/USDT', 'SOL/USDT',
            'EUR/USD', 'GBP/USD', 'USD/JPY', 'XAU/USD (Gold)', 'Crude Oil',
            'AAPL', 'TSLA', 'NVDA', 'MSFT', 'SPY', 'QQQ',
        ];

        $exitReasons = [
            'Target Hit',
            'Stop Loss Hit',
            'Trailing Stop Hit',
            'Manual Profit Taking',
            'Manual Risk Cut / Early Exit',
            'End of Day / Market Close',
            'Invalidation of Setup',
            'High Impact News / Event',
            'Break-Even Exit',
        ];

        return view('trades.create', compact('strategies', 'commonInstruments', 'exitReasons'));
    }

    /**
     * Store a new Journal entry.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'instrument' => ['required', 'string', 'max:50'],
            'direction' => ['required', 'in:BUY,SELL,LONG,SHORT'],
            'entry_datetime' => ['required', 'date'],
            'exit_datetime' => ['nullable', 'date'],
            'quantity' => ['required', 'numeric', 'gt:0'],
            'entry_price' => ['required', 'numeric', 'gt:0'],
            'exit_price' => ['nullable', 'numeric', 'gte:0'],
            'stop_loss' => ['nullable', 'numeric', 'gte:0'],
            'target' => ['nullable', 'numeric', 'gte:0'],
            'exit_reason' => ['nullable', 'string', 'max:100'],
            'gross_pnl' => ['nullable', 'numeric'],
            'charges' => ['nullable', 'numeric', 'gte:0'],
            'net_pnl' => ['nullable', 'numeric'],
            'strategy_id' => ['nullable', 'exists:strategies,id'],
            'new_strategy_name' => ['nullable', 'string', 'max:50'],
            'screenshot' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:10240'], // up to 10MB
            'lessons' => ['nullable', 'string', 'max:3000'],
            'remarks' => ['nullable', 'string', 'max:3000'],
        ]);

        $user = Auth::user();

        // Standardize direction
        $direction = in_array(strtoupper($validated['direction']), ['BUY', 'LONG']) ? 'BUY' : 'SELL';

        // Check if user entered a new strategy inline
        $strategyId = $validated['strategy_id'] ?? null;
        $strategyName = null;

        if (! empty($request->new_strategy_name)) {
            $newStrat = Strategy::firstOrCreate([
                'user_id' => $user->id,
                'name' => trim($request->new_strategy_name),
            ], [
                'color' => '#10b981',
                'description' => 'User defined strategy',
                'is_active' => true,
            ]);
            $strategyId = $newStrat->id;
            $strategyName = $newStrat->name;
        } elseif ($strategyId) {
            $strat = Strategy::where('user_id', $user->id)->find($strategyId);
            $strategyName = $strat?->name;
        }

        // Calculate Gross P&L if exit_price is provided and gross_pnl wasn't explicitly supplied
        $quantity = (float) $validated['quantity'];
        $entryPrice = (float) $validated['entry_price'];
        $exitPrice = isset($validated['exit_price']) && $validated['exit_price'] !== null ? (float) $validated['exit_price'] : null;
        $charges = isset($validated['charges']) ? (float) $validated['charges'] : 0.00;

        if ($request->filled('gross_pnl')) {
            $grossPnl = (float) $request->gross_pnl;
        } elseif ($exitPrice !== null) {
            if ($direction === 'BUY') {
                $grossPnl = ($exitPrice - $entryPrice) * $quantity;
            } else {
                $grossPnl = ($entryPrice - $exitPrice) * $quantity;
            }
        } else {
            $grossPnl = 0.00;
        }

        // Calculate Net P&L
        if ($request->filled('net_pnl')) {
            $netPnl = (float) $request->net_pnl;
        } else {
            $netPnl = $grossPnl - $charges;
        }

        // Handle Screenshot conversion to WebP format
        $screenshotPath = null;
        if ($request->hasFile('screenshot')) {
            $screenshotPath = $this->imageService->convertAndStoreAsWebp(
                $request->file('screenshot'),
                'trades',
                82
            );
        }

        $trade = Trade::create([
            'user_id' => $user->id,
            'strategy_id' => $strategyId,
            'strategy_name' => $strategyName,
            'instrument' => strtoupper(trim($validated['instrument'])),
            'direction' => $direction,
            'entry_datetime' => $validated['entry_datetime'],
            'exit_datetime' => $validated['exit_datetime'] ?? null,
            'quantity' => $quantity,
            'entry_price' => $entryPrice,
            'exit_price' => $exitPrice,
            'stop_loss' => $validated['stop_loss'] ?? null,
            'target' => $validated['target'] ?? null,
            'exit_reason' => $validated['exit_reason'] ?? null,
            'gross_pnl' => round($grossPnl, 2),
            'charges' => round($charges, 2),
            'net_pnl' => round($netPnl, 2),
            'screenshot' => $screenshotPath,
            'lessons' => $validated['lessons'] ?? null,
            'remarks' => $validated['remarks'] ?? null,
            'journal_datetime' => Carbon::now(), // Server date time when stored in db
            'trade_status' => $exitPrice !== null ? 'CLOSED' : 'OPEN',
        ]);

        return redirect()->route('trades.show', $trade)
            ->with('success', 'Trade logged successfully into your journal!');
    }

    /**
     * Display a specific Journal entry.
     */
    public function show(Trade $trade): View
    {
        $this->authorizeTrade($trade);
        $trade->load('strategy');

        return view('trades.show', compact('trade'));
    }

    /**
     * Show form to edit a Journal entry.
     */
    public function edit(Trade $trade): View
    {
        $this->authorizeTrade($trade);

        $user = Auth::user();
        $strategies = Strategy::where('user_id', $user->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $commonInstruments = [
            'NIFTY 50', 'BANK NIFTY', 'FIN NIFTY', 'MIDCP NIFTY',
            'RELIANCE', 'HDFCBANK', 'ICICIBANK', 'INFY', 'TCS', 'TATAMOTORS', 'SBIN',
            'BTC/USDT', 'ETH/USDT', 'SOL/USDT',
            'EUR/USD', 'GBP/USD', 'USD/JPY', 'XAU/USD (Gold)', 'Crude Oil',
            'AAPL', 'TSLA', 'NVDA', 'MSFT', 'SPY', 'QQQ',
        ];

        $exitReasons = [
            'Target Hit',
            'Stop Loss Hit',
            'Trailing Stop Hit',
            'Manual Profit Taking',
            'Manual Risk Cut / Early Exit',
            'End of Day / Market Close',
            'Invalidation of Setup',
            'High Impact News / Event',
            'Break-Even Exit',
        ];

        return view('trades.edit', compact('trade', 'strategies', 'commonInstruments', 'exitReasons'));
    }

    /**
     * Update an existing Journal entry.
     */
    public function update(Request $request, Trade $trade): RedirectResponse
    {
        $this->authorizeTrade($trade);

        $validated = $request->validate([
            'instrument' => ['required', 'string', 'max:50'],
            'direction' => ['required', 'in:BUY,SELL,LONG,SHORT'],
            'entry_datetime' => ['required', 'date'],
            'exit_datetime' => ['nullable', 'date'],
            'quantity' => ['required', 'numeric', 'gt:0'],
            'entry_price' => ['required', 'numeric', 'gt:0'],
            'exit_price' => ['nullable', 'numeric', 'gte:0'],
            'stop_loss' => ['nullable', 'numeric', 'gte:0'],
            'target' => ['nullable', 'numeric', 'gte:0'],
            'exit_reason' => ['nullable', 'string', 'max:100'],
            'gross_pnl' => ['nullable', 'numeric'],
            'charges' => ['nullable', 'numeric', 'gte:0'],
            'net_pnl' => ['nullable', 'numeric'],
            'strategy_id' => ['nullable', 'exists:strategies,id'],
            'new_strategy_name' => ['nullable', 'string', 'max:50'],
            'screenshot' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:10240'],
            'remove_screenshot' => ['nullable', 'boolean'],
            'lessons' => ['nullable', 'string', 'max:3000'],
            'remarks' => ['nullable', 'string', 'max:3000'],
        ]);

        $user = Auth::user();
        $direction = in_array(strtoupper($validated['direction']), ['BUY', 'LONG']) ? 'BUY' : 'SELL';

        $strategyId = $validated['strategy_id'] ?? null;
        $strategyName = null;

        if (! empty($request->new_strategy_name)) {
            $newStrat = Strategy::firstOrCreate([
                'user_id' => $user->id,
                'name' => trim($request->new_strategy_name),
            ], [
                'color' => '#10b981',
                'description' => 'User defined strategy',
                'is_active' => true,
            ]);
            $strategyId = $newStrat->id;
            $strategyName = $newStrat->name;
        } elseif ($strategyId) {
            $strat = Strategy::where('user_id', $user->id)->find($strategyId);
            $strategyName = $strat?->name;
        }

        $quantity = (float) $validated['quantity'];
        $entryPrice = (float) $validated['entry_price'];
        $exitPrice = isset($validated['exit_price']) && $validated['exit_price'] !== null ? (float) $validated['exit_price'] : null;
        $charges = isset($validated['charges']) ? (float) $validated['charges'] : 0.00;

        if ($request->filled('gross_pnl')) {
            $grossPnl = (float) $request->gross_pnl;
        } elseif ($exitPrice !== null) {
            if ($direction === 'BUY') {
                $grossPnl = ($exitPrice - $entryPrice) * $quantity;
            } else {
                $grossPnl = ($entryPrice - $exitPrice) * $quantity;
            }
        } else {
            $grossPnl = 0.00;
        }

        if ($request->filled('net_pnl')) {
            $netPnl = (float) $request->net_pnl;
        } else {
            $netPnl = $grossPnl - $charges;
        }

        $screenshotPath = $trade->screenshot;

        if ($request->boolean('remove_screenshot')) {
            $this->imageService->deleteScreenshot($trade->screenshot);
            $screenshotPath = null;
        }

        if ($request->hasFile('screenshot')) {
            // Delete old screenshot
            $this->imageService->deleteScreenshot($trade->screenshot);
            // Convert new image to WebP
            $screenshotPath = $this->imageService->convertAndStoreAsWebp(
                $request->file('screenshot'),
                'trades',
                82
            );
        }

        $trade->update([
            'strategy_id' => $strategyId,
            'strategy_name' => $strategyName,
            'instrument' => strtoupper(trim($validated['instrument'])),
            'direction' => $direction,
            'entry_datetime' => $validated['entry_datetime'],
            'exit_datetime' => $validated['exit_datetime'] ?? null,
            'quantity' => $quantity,
            'entry_price' => $entryPrice,
            'exit_price' => $exitPrice,
            'stop_loss' => $validated['stop_loss'] ?? null,
            'target' => $validated['target'] ?? null,
            'exit_reason' => $validated['exit_reason'] ?? null,
            'gross_pnl' => round($grossPnl, 2),
            'charges' => round($charges, 2),
            'net_pnl' => round($netPnl, 2),
            'screenshot' => $screenshotPath,
            'lessons' => $validated['lessons'] ?? null,
            'remarks' => $validated['remarks'] ?? null,
            'trade_status' => $exitPrice !== null ? 'CLOSED' : 'OPEN',
        ]);

        return redirect()->route('trades.show', $trade)
            ->with('success', 'Trade entry updated successfully!');
    }

    /**
     * Delete a Journal entry.
     */
    public function destroy(Trade $trade): RedirectResponse
    {
        $this->authorizeTrade($trade);

        if ($trade->screenshot) {
            $this->imageService->deleteScreenshot($trade->screenshot);
        }

        $trade->delete();

        return redirect()->route('trades.index')
            ->with('success', 'Trade journal entry removed.');
    }

    /**
     * Authorize that the current user owns this trade (or is super admin).
     */
    protected function authorizeTrade(Trade $trade): void
    {
        if ($trade->user_id !== Auth::id() && ! Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized access to this trade entry.');
        }
    }
}
