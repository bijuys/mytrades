<?php

namespace Tests\Feature;

use App\Models\Trade;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $regularUser;

    protected User $pendingUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'first_name' => 'Vikram',
            'last_name' => 'Aditya',
            'name' => 'Vikram Aditya',
            'email' => 'admin@mytrades.com',
            'password' => Hash::make('password123'),
            'phone' => '+919876543210',
            'trading_experience' => 10,
            'agreed_to_terms' => true,
            'role' => 'admin',
            'status' => 'approved',
            'approved_at' => Carbon::now(),
        ]);

        $this->regularUser = User::create([
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
            'approved_at' => Carbon::now(),
        ]);

        $this->pendingUser = User::create([
            'first_name' => 'Amit',
            'last_name' => 'Patel',
            'name' => 'Amit Patel',
            'email' => 'amit@example.com',
            'password' => Hash::make('password123'),
            'phone' => '+919988776655',
            'trading_experience' => 2,
            'agreed_to_terms' => true,
            'role' => 'user',
            'status' => 'pending',
        ]);
    }

    public function test_non_admin_cannot_access_admin_dashboard(): void
    {
        $response = $this->actingAs($this->regularUser)->get('/admin');
        $response->assertStatus(403);
    }

    public function test_admin_can_access_dashboard_and_see_pending_users(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin');
        $response->assertStatus(200);
        $response->assertSee('Administration Overview');
        $response->assertSee('Pending User Approvals');
        $response->assertSee('Amit Patel');
    }

    public function test_admin_can_view_user_management(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/users');
        $response->assertStatus(200);
        $response->assertSee('User Management');
        $response->assertSee('Amit Patel');
        $response->assertSee('Rahul Verma');
    }

    public function test_admin_can_approve_pending_user_and_user_can_then_login(): void
    {
        // 1. Pending user cannot login initially
        $this->post('/login', [
            'email' => 'amit@example.com',
            'password' => 'password123',
        ]);
        $this->assertGuest();

        // 2. Admin approves user
        $response = $this->actingAs($this->admin)->post(route('admin.users.approve', $this->pendingUser));
        $response->assertSessionHas('success');

        $this->pendingUser->refresh();
        $this->assertEquals('approved', $this->pendingUser->status);
        $this->assertNotNull($this->pendingUser->approved_at);

        // 3. Admin logs out, user logs in
        auth()->logout();

        $loginResponse = $this->post('/login', [
            'email' => 'amit@example.com',
            'password' => 'password123',
        ]);
        $this->assertAuthenticatedAs($this->pendingUser);
    }

    public function test_admin_can_reject_user_with_reason(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.users.reject', $this->pendingUser), [
            'rejection_reason' => 'Invalid trading credentials provided.',
        ]);

        $this->pendingUser->refresh();
        $this->assertEquals('rejected', $this->pendingUser->status);
        $this->assertEquals('Invalid trading credentials provided.', $this->pendingUser->rejection_reason);
    }

    public function test_admin_can_audit_all_trades_and_delete_trade(): void
    {
        $trade = Trade::create([
            'user_id' => $this->regularUser->id,
            'instrument' => 'NIFTY',
            'direction' => 'BUY',
            'entry_datetime' => Carbon::now(),
            'quantity' => 50,
            'entry_price' => 22000.00,
            'exit_price' => 22100.00,
            'gross_pnl' => 5000.00,
            'charges' => 50.00,
            'net_pnl' => 4950.00,
            'journal_datetime' => Carbon::now(),
        ]);

        // View platform trades
        $response = $this->actingAs($this->admin)->get('/admin/trades');
        $response->assertStatus(200);
        $response->assertSee('Platform Trades Audit');
        $response->assertSee('NIFTY');
        $response->assertSee('Rahul Verma');

        // Delete trade
        $deleteResponse = $this->actingAs($this->admin)->delete(route('admin.trades.delete', $trade));
        $this->assertDatabaseMissing('trades', ['id' => $trade->id]);
    }
}
