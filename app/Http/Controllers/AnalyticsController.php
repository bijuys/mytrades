<?php

namespace App\Http\Controllers;

use App\Models\Strategy;
use App\Models\Trade;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AnalyticsController extends Controller
{
    /**
     * Show comprehensive performance summary in many ways.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();

        $query = Trade::with('strategy')->where('user_id', $user->id);

        // Date Range filtering
        $range = $request->input('range', 'all');
        if ($range === '7d') {
            $query->where('entry_datetime', '>=', Carbon::now()->subDays(7));
        } elseif ($range === '30d') {
            $query->where('entry_datetime', '>=', Carbon::now()->subDays(30));
        } elseif ($range === '90d') {
            $query->where('entry_datetime', '>=', Carbon::now()->subDays(90));
        } elseif ($range === 'ytd') {
            $query->whereYear('entry_datetime', Carbon::now()->year);
        } elseif ($range === 'custom') {
            if ($request->filled('from')) {
                $query->whereDate('entry_datetime', '>=', $request->from);
            }
            if ($request->filled('to')) {
                $query->whereDate('entry_datetime', '<=', $request->to);
            }
        }

        if ($strategyFilter = $request->input('strategy_id')) {
            $query->where('strategy_id', $strategyFilter);
        }

        $allTrades = $query->orderBy('entry_datetime', 'asc')->get();

        // High Level Metrics
        $totalTrades = $allTrades->count();
        $totalGrossPnl = (float) $allTrades->sum('gross_pnl');
        $totalCharges = (float) $allTrades->sum('charges');
        $totalNetPnl = (float) $allTrades->sum('net_pnl');

        $winningTrades = $allTrades->filter(fn ($t) => (float) $t->net_pnl > 0);
        $losingTrades = $allTrades->filter(fn ($t) => (float) $t->net_pnl < 0);
        $beTrades = $allTrades->filter(fn ($t) => (float) $t->net_pnl == 0);

        $winCount = $winningTrades->count();
        $lossCount = $losingTrades->count();
        $beCount = $beTrades->count();

        $winRate = $totalTrades > 0 ? round(($winCount / $totalTrades) * 100, 1) : 0;
        $lossRate = $totalTrades > 0 ? round(($lossCount / $totalTrades) * 100, 1) : 0;

        $grossWins = (float) $winningTrades->sum('net_pnl');
        $grossLosses = abs((float) $losingTrades->sum('net_pnl'));
        $profitFactor = $grossLosses > 0 ? round($grossWins / $grossLosses, 2) : ($grossWins > 0 ? 99.99 : 0);

        $avgWin = $winCount > 0 ? round($grossWins / $winCount, 2) : 0;
        $avgLoss = $lossCount > 0 ? round($grossLosses / $lossCount, 2) : 0;
        $winLossRatio = $avgLoss > 0 ? round($avgWin / $avgLoss, 2) : ($avgWin > 0 ? 99.99 : 0);

        $bestTrade = $allTrades->sortByDesc('net_pnl')->first();
        $worstTrade = $allTrades->sortBy('net_pnl')->first();

        // Calculate Win/Loss Streaks
        $currentStreak = 0;
        $maxWinStreak = 0;
        $maxLossStreak = 0;
        $tempWins = 0;
        $tempLosses = 0;

        foreach ($allTrades as $t) {
            if ((float) $t->net_pnl > 0) {
                $tempWins++;
                $tempLosses = 0;
                $maxWinStreak = max($maxWinStreak, $tempWins);
            } elseif ((float) $t->net_pnl < 0) {
                $tempLosses++;
                $tempWins = 0;
                $maxLossStreak = max($maxLossStreak, $tempLosses);
            }
        }

        // 1. Performance By Strategy
        $strategyStats = [];
        $groupedByStrategy = $allTrades->groupBy(fn ($t) => $t->strategy?->name ?? $t->strategy_name ?? 'Unspecified');

        foreach ($groupedByStrategy as $stratName => $stratTrades) {
            $sTotal = $stratTrades->count();
            $sWins = $stratTrades->filter(fn ($t) => (float) $t->net_pnl > 0)->count();
            $sLosses = $stratTrades->filter(fn ($t) => (float) $t->net_pnl < 0)->count();
            $sNetPnl = (float) $stratTrades->sum('net_pnl');
            $sWinRate = $sTotal > 0 ? round(($sWins / $sTotal) * 100, 1) : 0;
            $sColor = $stratTrades->first()->strategy?->color ?? '#6366f1';

            $strategyStats[] = [
                'name' => $stratName,
                'color' => $sColor,
                'total' => $sTotal,
                'wins' => $sWins,
                'losses' => $sLosses,
                'win_rate' => $sWinRate,
                'net_pnl' => round($sNetPnl, 2),
            ];
        }

        usort($strategyStats, fn ($a, $b) => $b['net_pnl'] <=> $a['net_pnl']);

        // 2. Performance By Instrument
        $instrumentStats = [];
        $groupedByInstrument = $allTrades->groupBy('instrument');

        foreach ($groupedByInstrument as $instName => $instTrades) {
            $iTotal = $instTrades->count();
            $iWins = $instTrades->filter(fn ($t) => (float) $t->net_pnl > 0)->count();
            $iNetPnl = (float) $instTrades->sum('net_pnl');
            $iWinRate = $iTotal > 0 ? round(($iWins / $iTotal) * 100, 1) : 0;

            $instrumentStats[] = [
                'instrument' => $instName,
                'total' => $iTotal,
                'wins' => $iWins,
                'losses' => $iTotal - $iWins,
                'win_rate' => $iWinRate,
                'net_pnl' => round($iNetPnl, 2),
            ];
        }

        usort($instrumentStats, fn ($a, $b) => $b['net_pnl'] <=> $a['net_pnl']);

        // 3. Performance By Direction (Buy/Long vs Sell/Short)
        $directionStats = [
            'BUY' => ['total' => 0, 'wins' => 0, 'net_pnl' => 0.0, 'win_rate' => 0],
            'SELL' => ['total' => 0, 'wins' => 0, 'net_pnl' => 0.0, 'win_rate' => 0],
        ];

        foreach (['BUY', 'SELL'] as $dir) {
            $dirTrades = $allTrades->filter(fn ($t) => strtoupper($t->direction) === $dir);
            $dTotal = $dirTrades->count();
            $dWins = $dirTrades->filter(fn ($t) => (float) $t->net_pnl > 0)->count();
            $dNet = (float) $dirTrades->sum('net_pnl');
            $directionStats[$dir] = [
                'total' => $dTotal,
                'wins' => $dWins,
                'losses' => $dTotal - $dWins,
                'net_pnl' => round($dNet, 2),
                'win_rate' => $dTotal > 0 ? round(($dWins / $dTotal) * 100, 1) : 0,
            ];
        }

        // 4. Cumulative Equity Curve Data (Timeline of cumulative PnL)
        $equityLabels = [];
        $equityData = [];
        $runningPnl = 0.0;

        foreach ($allTrades as $idx => $t) {
            $runningPnl += (float) $t->net_pnl;
            $equityLabels[] = $t->entry_datetime ? $t->entry_datetime->format('M d') : 'Trade #'.($idx + 1);
            $equityData[] = round($runningPnl, 2);
        }

        // 5. Monthly P&L Data
        $monthlyPnl = [];
        foreach ($allTrades as $t) {
            if ($t->entry_datetime) {
                $mKey = $t->entry_datetime->format('Y-m');
                $mLabel = $t->entry_datetime->format('M Y');
                if (! isset($monthlyPnl[$mKey])) {
                    $monthlyPnl[$mKey] = ['label' => $mLabel, 'net_pnl' => 0.0, 'trades' => 0];
                }
                $monthlyPnl[$mKey]['net_pnl'] += (float) $t->net_pnl;
                $monthlyPnl[$mKey]['trades']++;
            }
        }
        ksort($monthlyPnl);

        // 6. Day of Week Performance
        $dayOfWeekStats = [
            'Monday' => ['net_pnl' => 0.0, 'trades' => 0, 'wins' => 0],
            'Tuesday' => ['net_pnl' => 0.0, 'trades' => 0, 'wins' => 0],
            'Wednesday' => ['net_pnl' => 0.0, 'trades' => 0, 'wins' => 0],
            'Thursday' => ['net_pnl' => 0.0, 'trades' => 0, 'wins' => 0],
            'Friday' => ['net_pnl' => 0.0, 'trades' => 0, 'wins' => 0],
        ];

        foreach ($allTrades as $t) {
            if ($t->entry_datetime) {
                $dayName = $t->entry_datetime->format('l');
                if (isset($dayOfWeekStats[$dayName])) {
                    $dayOfWeekStats[$dayName]['net_pnl'] += (float) $t->net_pnl;
                    $dayOfWeekStats[$dayName]['trades']++;
                    if ((float) $t->net_pnl > 0) {
                        $dayOfWeekStats[$dayName]['wins']++;
                    }
                }
            }
        }

        $userStrategies = Strategy::where('user_id', $user->id)->orderBy('name')->get();

        return view('analytics.index', compact(
            'totalTrades',
            'totalGrossPnl',
            'totalCharges',
            'totalNetPnl',
            'winCount',
            'lossCount',
            'beCount',
            'winRate',
            'lossRate',
            'profitFactor',
            'avgWin',
            'avgLoss',
            'winLossRatio',
            'bestTrade',
            'worstTrade',
            'maxWinStreak',
            'maxLossStreak',
            'strategyStats',
            'instrumentStats',
            'directionStats',
            'equityLabels',
            'equityData',
            'monthlyPnl',
            'dayOfWeekStats',
            'userStrategies',
            'range'
        ));
    }
}
