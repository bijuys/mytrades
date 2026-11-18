<?php

namespace Tests\Feature;

use App\Models\Strategy;
use App\Models\Trade;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TradeJournalTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Strategy $strategy;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::create([
            'first_name' => 'Dev',
            'last_name' => 'Trader',
            'name' => 'Dev Trader',
            'email' => 'dev@example.com',
            'password' => Hash::make('password123'),
            'phone' => '+919876500000',
            'trading_experience' => 3,
            'agreed_to_terms' => true,
            'role' => 'user',
            'status' => 'approved',
            'approved_at' => Carbon::now(),
        ]);

        $this->strategy = Strategy::create([
            'user_id' => $this->user->id,
            'name' => 'Breakout & Retest',
            'color' => '#3b82f6',
            'description' => 'Test strategy',
            'is_active' => true,
        ]);
    }

    public function test_user_can_view_trades_journal_page(): void
    {
        $response = $this->actingAs($this->user)->get('/trades');
        $response->assertStatus(200);
        $response->assertSee('Trading Journal');
        $response->assertSee('Log New Trade');
    }

    public function test_user_can_view_create_trade_form(): void
    {
        $response = $this->actingAs($this->user)->get('/trades/create');
        $response->assertStatus(200);
        $response->assertSee('Log Journal Entry');
        $response->assertSee('Execution Numbers');
        $response->assertSee('Automatically converted to WebP format');
        $response->assertSee('rows="5"', false);
        $response->assertSee('Charges & Brokerage (₹)', false);
    }

    public function test_user_can_store_trade_with_auto_pnl_and_webp_screenshot(): void
    {
        Storage::fake('public');

        // Create a fake test PNG image
        $image = UploadedFile::fake()->image('chart.png', 800, 600);

        $response = $this->actingAs($this->user)->post('/trades', [
            'instrument' => 'BANKNIFTY',
            'direction' => 'BUY',
            'entry_datetime' => '2026-11-18 09:30:00',
            'exit_datetime' => '2026-11-18 10:15:00',
            'quantity' => 30,
            'entry_price' => 48200.00,
            'exit_price' => 48400.00, // 200 pts gain * 30 qty = 6000 gross
            'stop_loss' => 48100.00,
            'target' => 48450.00,
            'charges' => 60.00, // Net = 6000 - 60 = 5940
            'strategy_id' => $this->strategy->id,
            'exit_reason' => 'Target Hit',
            'lessons' => 'Followed the trend cleanly.',
            'remarks' => 'Morning breakout trade.',
            'screenshot' => $image,
        ]);

        $this->assertDatabaseHas('trades', [
            'user_id' => $this->user->id,
            'instrument' => 'BANKNIFTY',
            'direction' => 'BUY',
            'quantity' => 30,
            'gross_pnl' => 6000.00,
            'charges' => 60.00,
            'net_pnl' => 5940.00,
            'exit_reason' => 'Target Hit',
        ]);

        $trade = Trade::where('instrument', 'BANKNIFTY')->first();
        $this->assertNotNull($trade);
        $this->assertNotNull($trade->screenshot);
        $this->assertStringEndsWith('.webp', $trade->screenshot); // Converted to WebP!

        $response->assertRedirect(route('trades.show', $trade));
    }

    public function test_user_can_view_single_trade_details(): void
    {
        $trade = Trade::create([
            'user_id' => $this->user->id,
            'strategy_id' => $this->strategy->id,
            'strategy_name' => $this->strategy->name,
            'instrument' => 'NIFTY 50',
            'direction' => 'BUY',
            'entry_datetime' => Carbon::now()->subHours(2),
            'exit_datetime' => Carbon::now()->subHour(),
            'quantity' => 50,
            'entry_price' => 22000.00,
            'exit_price' => 22100.00,
            'gross_pnl' => 5000.00,
            'charges' => 50.00,
            'net_pnl' => 4950.00,
            'exit_reason' => 'Target Hit',
            'journal_datetime' => Carbon::now(),
        ]);

        $response = $this->actingAs($this->user)->get(route('trades.show', $trade));
        $response->assertStatus(200);
        $response->assertSee('NIFTY 50');
        $response->assertSee('+₹4,950.00');
    }

    public function test_user_can_edit_and_update_trade(): void
    {
        $trade = Trade::create([
            'user_id' => $this->user->id,
            'strategy_id' => $this->strategy->id,
            'instrument' => 'RELIANCE',
            'direction' => 'BUY',
            'entry_datetime' => Carbon::now()->subDay(),
            'quantity' => 10,
            'entry_price' => 2800.00,
            'exit_price' => 2820.00,
            'gross_pnl' => 200.00,
            'charges' => 20.00,
            'net_pnl' => 180.00,
            'journal_datetime' => Carbon::now(),
        ]);

        $response = $this->actingAs($this->user)->put(route('trades.update', $trade), [
            'instrument' => 'RELIANCE',
            'direction' => 'BUY',
            'entry_datetime' => '2026-11-18 09:30:00',
            'quantity' => 10,
            'entry_price' => 2800.00,
            'exit_price' => 2850.00, // Updated exit price
            'charges' => 25.00,
            'strategy_id' => $this->strategy->id,
            'remarks' => 'Updated after checking trade log.',
        ]);

        $response->assertRedirect(route('trades.show', $trade));

        $trade->refresh();
        $this->assertEquals(2850.00, (float) $trade->exit_price);
        $this->assertEquals(500.00, (float) $trade->gross_pnl);
        $this->assertEquals(475.00, (float) $trade->net_pnl);
    }

    public function test_user_can_delete_trade(): void
    {
        $trade = Trade::create([
            'user_id' => $this->user->id,
            'instrument' => 'INFY',
            'direction' => 'BUY',
            'entry_datetime' => Carbon::now(),
            'quantity' => 20,
            'entry_price' => 1500.00,
            'exit_price' => 1510.00,
            'gross_pnl' => 200.00,
            'charges' => 15.00,
            'net_pnl' => 185.00,
            'journal_datetime' => Carbon::now(),
        ]);

        $response = $this->actingAs($this->user)->delete(route('trades.destroy', $trade));
        $response->assertRedirect(route('trades.index'));

        $this->assertDatabaseMissing('trades', ['id' => $trade->id]);
    }

    public function test_user_can_view_performance_analytics_summary(): void
    {
        $response = $this->actingAs($this->user)->get(route('analytics.index'));
        $response->assertStatus(200);
        $response->assertSee('Performance Summary');
        $response->assertSee('Cumulative Equity Curve');
        $response->assertSee('Profit Factor');
    }

    public function test_user_can_manage_custom_strategies(): void
    {
        $response = $this->actingAs($this->user)->post(route('strategies.store'), [
            'name' => 'Custom VWAP Bounce',
            'color' => '#10b981',
            'description' => 'Tested on 5m chart',
        ]);

        $response->assertRedirect(route('strategies.index'));

        $this->assertDatabaseHas('strategies', [
            'user_id' => $this->user->id,
            'name' => 'Custom VWAP Bounce',
        ]);
    }

    public function test_trade_screenshot_url_attribute(): void
    {
        $trade = Trade::create([
            'user_id' => $this->user->id,
            'strategy_id' => $this->strategy->id,
            'instrument' => 'NIFTY',
            'direction' => 'BUY',
            'entry_datetime' => Carbon::now(),
            'quantity' => 25,
            'entry_price' => 22000.00,
            'screenshot' => 'trades/sample.webp',
        ]);

        config(['filesystems.default' => 'public']);
        $this->assertStringContainsString('trades/sample.webp', $trade->screenshot_url);

        $trade->screenshot = 'https://example.com/chart.png';
        $this->assertEquals('https://example.com/chart.png', $trade->screenshot_url);
    }
}
