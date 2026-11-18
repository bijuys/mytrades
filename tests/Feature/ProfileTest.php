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

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::create([
            'first_name' => 'Rohan',
            'last_name' => 'Sharma',
            'name' => 'Rohan Sharma',
            'email' => 'rohan@example.com',
            'password' => Hash::make('password123'),
            'phone' => '+919876543210',
            'trading_experience' => 3,
            'agreed_to_terms' => true,
            'role' => 'user',
            'status' => 'approved',
            'approved_at' => Carbon::now(),
        ]);
    }

    public function test_profile_page_is_displayed_for_authenticated_user(): void
    {
        $response = $this->actingAs($this->user)->get('/profile');

        $response->assertStatus(200);
        $response->assertSee('Account Profile');
        $response->assertSee('Rohan');
        $response->assertSee('Sharma');
        $response->assertSee('rohan@example.com');
        $response->assertSee('RS'); // Initials fallback
        $response->assertSee('Close My Account Permanently');
    }

    public function test_user_initials_fallback_attribute(): void
    {
        $this->assertEquals('RS', $this->user->initials);

        $userSingleName = User::create([
            'first_name' => 'Vikram',
            'last_name' => '',
            'name' => 'Vikram',
            'email' => 'vikram@example.com',
            'password' => Hash::make('secret123'),
            'phone' => '+919876500001',
            'trading_experience' => 2,
            'agreed_to_terms' => true,
            'role' => 'user',
            'status' => 'approved',
        ]);

        $this->assertEquals('V', $userSingleName->initials);
    }

    public function test_profile_information_can_be_updated(): void
    {
        $response = $this->actingAs($this->user)->patch('/profile', [
            'first_name' => 'Rohan',
            'last_name' => 'Verma',
            'email' => 'rohan.new@example.com',
            'phone' => '9876543211',
            'trading_experience' => 5,
        ]);

        $response->assertRedirect('/profile');
        $response->assertSessionHas('success');

        $this->user->refresh();

        $this->assertEquals('Rohan Verma', $this->user->name);
        $this->assertEquals('Rohan', $this->user->first_name);
        $this->assertEquals('Verma', $this->user->last_name);
        $this->assertEquals('rohan.new@example.com', $this->user->email);
        $this->assertEquals('9876543211', $this->user->phone);
        $this->assertEquals(5, $this->user->trading_experience);
    }

    public function test_profile_picture_avatar_can_be_uploaded_and_removed(): void
    {
        Storage::fake('public');
        config(['filesystems.default' => 'public']);

        $image = UploadedFile::fake()->image('my_avatar.png', 300, 300);

        // Upload avatar
        $response = $this->actingAs($this->user)->patch('/profile', [
            'first_name' => 'Rohan',
            'last_name' => 'Sharma',
            'email' => 'rohan@example.com',
            'phone' => '9876543210',
            'trading_experience' => 3,
            'avatar' => $image,
        ]);

        $response->assertRedirect('/profile');
        $this->user->refresh();

        $this->assertNotNull($this->user->avatar);
        $this->assertStringEndsWith('.webp', $this->user->avatar);
        Storage::disk('public')->assertExists($this->user->avatar);
        $this->assertNotNull($this->user->avatar_url);

        $savedAvatarPath = $this->user->avatar;

        // Remove avatar
        $removeResponse = $this->actingAs($this->user)->patch('/profile', [
            'first_name' => 'Rohan',
            'last_name' => 'Sharma',
            'email' => 'rohan@example.com',
            'phone' => '9876543210',
            'trading_experience' => 3,
            'remove_avatar' => 1,
        ]);

        $removeResponse->assertRedirect('/profile');
        $this->user->refresh();

        $this->assertNull($this->user->avatar);
        Storage::disk('public')->assertMissing($savedAvatarPath);
    }

    public function test_password_can_be_updated(): void
    {
        $response = $this->actingAs($this->user)->put('/profile/password', [
            'current_password' => 'password123',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertRedirect('/profile');
        $response->assertSessionHas('success');

        $this->assertTrue(Hash::check('newpassword123', $this->user->fresh()->password));
    }

    public function test_correct_current_password_must_be_provided_to_update_password(): void
    {
        $response = $this->actingAs($this->user)->put('/profile/password', [
            'current_password' => 'wrongpassword',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertSessionHasErrors('current_password');
        $this->assertFalse(Hash::check('newpassword123', $this->user->fresh()->password));
    }

    public function test_user_can_close_account(): void
    {
        Storage::fake('public');
        config(['filesystems.default' => 'public']);

        $strategy = Strategy::create([
            'user_id' => $this->user->id,
            'name' => 'Test Strategy',
            'color' => '#3b82f6',
        ]);

        $trade = Trade::create([
            'user_id' => $this->user->id,
            'strategy_id' => $strategy->id,
            'instrument' => 'NIFTY',
            'direction' => 'BUY',
            'entry_datetime' => Carbon::now(),
            'quantity' => 25,
            'entry_price' => 22000.00,
        ]);

        $response = $this->actingAs($this->user)->delete('/profile', [
            'password' => 'password123',
        ]);

        $response->assertRedirect('/login');
        $this->assertGuest();

        $this->assertDatabaseMissing('users', ['id' => $this->user->id]);
        $this->assertDatabaseMissing('trades', ['id' => $trade->id]);
        $this->assertDatabaseMissing('strategies', ['id' => $strategy->id]);
    }

    public function test_correct_password_must_be_provided_to_close_account(): void
    {
        $response = $this->actingAs($this->user)->delete('/profile', [
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertDatabaseHas('users', ['id' => $this->user->id]);
        $this->assertAuthenticatedAs($this->user);
    }
}
