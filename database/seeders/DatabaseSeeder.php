<?php

namespace Database\Seeders;

use App\Models\Strategy;
use App\Models\Trade;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Super Admin
        $admin = User::create([
            'first_name' => 'Vikram',
            'last_name' => 'Aditya',
            'name' => 'Vikram Aditya',
            'email' => 'admin@mytrades.com',
            'password' => Hash::make('password123'),
            'phone' => '+919876543210',
            'trading_experience' => 8,
            'agreed_to_terms' => true,
            'role' => 'admin',
            'status' => 'approved',
            'approved_at' => Carbon::now(),
        ]);

        // 2. Create Active Approved Trader
        $trader = User::create([
            'first_name' => 'Rahul',
            'last_name' => 'Verma',
            'name' => 'Rahul Verma',
            'email' => 'trader@mytrades.com',
            'password' => Hash::make('password123'),
            'phone' => '+919812345678',
            'trading_experience' => 4,
            'agreed_to_terms' => true,
            'role' => 'user',
            'status' => 'approved',
            'approved_at' => Carbon::now()->subDays(30),
            'approved_by' => $admin->id,
        ]);

        // 3. Create Pending User (to demo Admin approval flow)
        User::create([
            'first_name' => 'Amit',
            'last_name' => 'Patel',
            'name' => 'Amit Patel',
            'email' => 'amit.patel@example.com',
            'password' => Hash::make('password123'),
            'phone' => '+919988776655',
            'trading_experience' => 2,
            'agreed_to_terms' => true,
            'role' => 'user',
            'status' => 'pending',
        ]);

        // Another pending user
        User::create([
            'first_name' => 'Pooja',
            'last_name' => 'Iyer',
            'name' => 'Pooja Iyer',
            'email' => 'pooja.iyer@example.com',
            'password' => Hash::make('password123'),
            'phone' => '+919871122334',
            'trading_experience' => 5,
            'agreed_to_terms' => true,
            'role' => 'user',
            'status' => 'pending',
        ]);

        // 4. Create Strategies for the Trader
        $stratBreakout = Strategy::create([
            'user_id' => $trader->id,
            'name' => 'Breakout & Retest',
            'color' => '#3b82f6',
            'description' => 'Key resistance breakout on heavy volume, entry on throwback retest confirmation.',
            'is_active' => true,
        ]);

        $stratCPR = Strategy::create([
            'user_id' => $trader->id,
            'name' => 'CPR & Pivot Points',
            'color' => '#10b981',
            'description' => 'Central Pivot Range bounce in trend direction, targeting R1/S1 pivots.',
            'is_active' => true,
        ]);

        $stratORB = Strategy::create([
            'user_id' => $trader->id,
            'name' => 'Opening Range Breakout (ORB)',
            'color' => '#8b5cf6',
            'description' => 'High momentum breakout of first 15-minute range.',
            'is_active' => true,
        ]);

        $stratVWAP = Strategy::create([
            'user_id' => $trader->id,
            'name' => 'VWAP Pullback',
            'color' => '#f59e0b',
            'description' => 'Reversion to VWAP line with tight risk and trend resumption.',
            'is_active' => true,
        ]);

        $stratScalp = Strategy::create([
            'user_id' => $trader->id,
            'name' => 'Option Scalping',
            'color' => '#06b6d4',
            'description' => 'Fast 10-20 point scalp in index options on sudden delta surge.',
            'is_active' => true,
        ]);

        // 5. Seed Realistic Sample Trades for Rahul
        $sampleTrades = [
            [
                'instrument' => 'NIFTY 50',
                'direction' => 'BUY',
                'entry_offset' => 14,
                'duration_min' => 45,
                'qty' => 100,
                'entry' => 22450.00,
                'exit' => 22530.00,
                'sl' => 22410.00,
                'target' => 22530.00,
                'charges' => 55.00,
                'strategy' => $stratBreakout,
                'exit_reason' => 'Target Hit',
                'lessons' => 'Patiently waited for the 15-min candle close above previous day high before taking the entry. Excellent reward:risk.',
                'remarks' => 'Heavy institutional buying seen in Banking & IT.',
            ],
            [
                'instrument' => 'BANK NIFTY',
                'direction' => 'BUY',
                'entry_offset' => 12,
                'duration_min' => 90,
                'qty' => 60,
                'entry' => 48100.00,
                'exit' => 48320.00,
                'sl' => 47990.00,
                'target' => 48350.00,
                'charges' => 65.00,
                'strategy' => $stratCPR,
                'exit_reason' => 'Trailing Stop Hit',
                'lessons' => 'Trailed stop behind 5-min pivot lows. Captured 220 points comfortably.',
                'remarks' => 'HDFC Bank led the rally after morning earnings call.',
            ],
            [
                'instrument' => 'RELIANCE',
                'direction' => 'BUY',
                'entry_offset' => 10,
                'duration_min' => 120,
                'qty' => 100,
                'entry' => 2840.00,
                'exit' => 2822.00,
                'sl' => 2820.00,
                'target' => 2880.00,
                'charges' => 48.00,
                'strategy' => $stratVWAP,
                'exit_reason' => 'Stop Loss Hit',
                'lessons' => 'Failed to notice crude oil spiking, which triggered market selloff. Cut loss without hesitation.',
                'remarks' => 'Followed risk management rule strictly. Loss kept small.',
            ],
            [
                'instrument' => 'NIFTY 50',
                'direction' => 'SELL',
                'entry_offset' => 8,
                'duration_min' => 30,
                'qty' => 150,
                'entry' => 22610.00,
                'exit' => 22540.00,
                'sl' => 22645.00,
                'target' => 22530.00,
                'charges' => 62.00,
                'strategy' => $stratORB,
                'exit_reason' => 'Target Hit',
                'lessons' => 'Fast breakdown after initial failure to sustain above 22,600 call writing wall.',
                'remarks' => 'Open interest data showed massive call additions at 22,600.',
            ],
            [
                'instrument' => 'TCS',
                'direction' => 'BUY',
                'entry_offset' => 6,
                'duration_min' => 180,
                'qty' => 50,
                'entry' => 3920.00,
                'exit' => 3975.00,
                'sl' => 3895.00,
                'target' => 3980.00,
                'charges' => 52.00,
                'strategy' => $stratBreakout,
                'exit_reason' => 'Manual Profit Taking',
                'lessons' => 'Booked profit near day high right before 3 PM market close.',
                'remarks' => 'IT index showed strong relative strength all day.',
            ],
            [
                'instrument' => 'BANK NIFTY',
                'direction' => 'SELL',
                'entry_offset' => 4,
                'duration_min' => 25,
                'qty' => 45,
                'entry' => 48450.00,
                'exit' => 48510.00,
                'sl' => 48510.00,
                'target' => 48300.00,
                'charges' => 45.00,
                'strategy' => $stratScalp,
                'exit_reason' => 'Stop Loss Hit',
                'lessons' => 'Anticipated breakdown rather than waiting for candle close. Premature entry cost 60 points.',
                'remarks' => 'Need to remember: Confirm first, click second!',
            ],
            [
                'instrument' => 'NIFTY 50',
                'direction' => 'BUY',
                'entry_offset' => 2,
                'duration_min' => 65,
                'qty' => 100,
                'entry' => 22500.00,
                'exit' => 22605.00,
                'sl' => 22455.00,
                'target' => 22600.00,
                'charges' => 58.00,
                'strategy' => $stratCPR,
                'exit_reason' => 'Target Hit',
                'lessons' => 'Clean CPR virgin pivot bounce. Risk was just 45 points for a 105 point gain (1:2.3 R:R).',
                'remarks' => 'Smooth trend without any pullback.',
            ],
            [
                'instrument' => 'BTC/USDT',
                'direction' => 'BUY',
                'entry_offset' => 1,
                'duration_min' => 240,
                'qty' => 0.5,
                'entry' => 64200.00,
                'exit' => 65800.00,
                'sl' => 63500.00,
                'target' => 66000.00,
                'charges' => 25.00,
                'strategy' => $stratBreakout,
                'exit_reason' => 'Manual Profit Taking',
                'lessons' => 'Strong weekend breakout over $64k resistance.',
                'remarks' => 'Crypto weekend scalp.',
            ],
        ];

        foreach ($sampleTrades as $st) {
            $entryTime = Carbon::now()->subDays($st['entry_offset'])->setTime(9, 45)->addMinutes(rand(10, 180));
            $exitTime = (clone $entryTime)->addMinutes($st['duration_min']);

            $qty = $st['qty'];
            $entry = $st['entry'];
            $exit = $st['exit'];
            $charges = $st['charges'];

            if ($st['direction'] === 'BUY') {
                $gross = ($exit - $entry) * $qty;
            } else {
                $gross = ($entry - $exit) * $qty;
            }
            $net = $gross - $charges;

            Trade::create([
                'user_id' => $trader->id,
                'strategy_id' => $st['strategy']->id,
                'strategy_name' => $st['strategy']->name,
                'instrument' => $st['instrument'],
                'direction' => $st['direction'],
                'entry_datetime' => $entryTime,
                'exit_datetime' => $exitTime,
                'quantity' => $qty,
                'entry_price' => $entry,
                'exit_price' => $exit,
                'stop_loss' => $st['sl'],
                'target' => $st['target'],
                'exit_reason' => $st['exit_reason'],
                'gross_pnl' => round($gross, 2),
                'charges' => round($charges, 2),
                'net_pnl' => round($net, 2),
                'screenshot' => null,
                'lessons' => $st['lessons'],
                'remarks' => $st['remarks'],
                'journal_datetime' => (clone $exitTime)->addMinutes(10),
                'trade_status' => 'CLOSED',
            ]);
        }
    }
}
