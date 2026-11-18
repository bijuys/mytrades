<?php

namespace Tests\Feature;

use App\Mail\WelcomeConfirmEmail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_can_be_rendered(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Welcome Back');
        $response->assertSee('login_password');
        $response->assertSee('login_eye_icon');
        $response->assertSee('Forgot password?');
    }

    public function test_signup_page_can_be_rendered_with_all_required_fields(): void
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
        $response->assertSee('First Name');
        $response->assertSee('Last Name');
        $response->assertSee('Phone Number (Indian)');
        $response->assertSee('Experience in Trading');
        $response->assertSee('Admin Approval Required');
        $response->assertSee('reg_password');
        $response->assertSee('reg_eye_icon');
    }

    public function test_user_can_signup_and_receives_welcome_confirmation_email(): void
    {
        Mail::fake();

        $response = $this->post('/register', [
            'first_name' => 'Siddharth',
            'last_name' => 'Menon',
            'email' => 'siddharth@example.com',
            'phone' => '9876543210',
            'trading_experience' => 3,
            'password' => 'secret1234',
            'password_confirmation' => 'secret1234',
            'agreed_to_terms' => '1',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHas('pending_notice');

        $this->assertDatabaseHas('users', [
            'email' => 'siddharth@example.com',
            'status' => 'unconfirmed',
            'role' => 'user',
            'trading_experience' => 3,
            'email_verified_at' => null,
        ]);

        $user = User::where('email', 'siddharth@example.com')->first();
        $this->assertNotNull($user->confirmation_token);
        $this->assertCount(6, $user->strategies); // Default starter strategies created

        Mail::assertSent(WelcomeConfirmEmail::class, function ($mail) use ($user) {
            return $mail->hasTo('siddharth@example.com') &&
                   str_contains($mail->confirmUrl, $user->confirmation_token);
        });
    }

    public function test_user_can_confirm_email_and_is_then_submitted_for_admin_approval(): void
    {
        $user = User::create([
            'first_name' => 'Siddharth',
            'last_name' => 'Menon',
            'name' => 'Siddharth Menon',
            'email' => 'siddharth@example.com',
            'password' => Hash::make('secret1234'),
            'phone' => '+919876543210',
            'trading_experience' => 3,
            'agreed_to_terms' => true,
            'role' => 'user',
            'status' => 'unconfirmed',
            'confirmation_token' => 'sample-test-token-abcdef123456',
            'email_verified_at' => null,
        ]);

        $response = $this->get('/confirm-email/sample-test-token-abcdef123456');

        $response->assertRedirect('/login');
        $response->assertSessionHas('pending_notice');

        $user->refresh();
        $this->assertEquals('pending', $user->status);
        $this->assertNotNull($user->email_verified_at);
        $this->assertNull($user->confirmation_token);
    }

    public function test_unconfirmed_user_cannot_login_and_gets_warning(): void
    {
        $user = User::create([
            'first_name' => 'Priya',
            'last_name' => 'Nair',
            'email' => 'priya@example.com',
            'password' => Hash::make('secret1234'),
            'phone' => '+919876543211',
            'trading_experience' => 2,
            'agreed_to_terms' => true,
            'role' => 'user',
            'status' => 'unconfirmed',
            'confirmation_token' => 'token-1234',
            'email_verified_at' => null,
        ]);

        $response = $this->post('/login', [
            'email' => 'priya@example.com',
            'password' => 'secret1234',
        ]);

        $this->assertGuest();
        $response->assertSessionHas('warning');
        $response->assertSessionHas('resend_email');
    }

    public function test_user_can_resend_confirmation_email(): void
    {
        Mail::fake();

        $user = User::create([
            'first_name' => 'Priya',
            'last_name' => 'Nair',
            'email' => 'priya@example.com',
            'password' => Hash::make('secret1234'),
            'phone' => '+919876543211',
            'trading_experience' => 2,
            'agreed_to_terms' => true,
            'role' => 'user',
            'status' => 'unconfirmed',
            'confirmation_token' => 'token-1234',
            'email_verified_at' => null,
        ]);

        $response = $this->post('/resend-confirmation', [
            'email' => 'priya@example.com',
        ]);

        $response->assertSessionHas('status');

        Mail::assertSent(WelcomeConfirmEmail::class, function ($mail) {
            return $mail->hasTo('priya@example.com');
        });
    }

    public function test_pending_user_cannot_login_and_gets_warning(): void
    {
        $user = User::create([
            'first_name' => 'Priya',
            'last_name' => 'Nair',
            'email' => 'priya@example.com',
            'password' => Hash::make('secret1234'),
            'phone' => '+919876543211',
            'trading_experience' => 2,
            'agreed_to_terms' => true,
            'role' => 'user',
            'status' => 'pending',
            'email_verified_at' => Carbon::now(),
        ]);

        $response = $this->post('/login', [
            'email' => 'priya@example.com',
            'password' => 'secret1234',
        ]);

        $this->assertGuest();
        $response->assertSessionHas('warning');
    }

    public function test_approved_user_can_login_successfully(): void
    {
        $user = User::create([
            'first_name' => 'Aarav',
            'last_name' => 'Kapoor',
            'email' => 'aarav@example.com',
            'password' => Hash::make('secret1234'),
            'phone' => '+919876543212',
            'trading_experience' => 5,
            'agreed_to_terms' => true,
            'role' => 'user',
            'status' => 'approved',
            'email_verified_at' => Carbon::now(),
            'approved_at' => Carbon::now(),
        ]);

        $response = $this->post('/login', [
            'email' => 'aarav@example.com',
            'password' => 'secret1234',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect('/trades');
    }

    public function test_forgot_password_flow_and_thanks_page(): void
    {
        $user = User::create([
            'first_name' => 'Karan',
            'last_name' => 'Johar',
            'email' => 'karan@example.com',
            'password' => Hash::make('oldpassword'),
            'phone' => '+919876543213',
            'trading_experience' => 1,
            'agreed_to_terms' => true,
            'role' => 'user',
            'status' => 'approved',
            'email_verified_at' => Carbon::now(),
        ]);

        // 1. Visit forgot password page
        $this->get('/forgot-password')->assertStatus(200);

        // 2. Submit email
        $response = $this->post('/forgot-password', [
            'email' => 'karan@example.com',
        ]);
        $response->assertSessionHas('status');

        $this->assertDatabaseHas('password_reset_tokens', [
            'email' => 'karan@example.com',
        ]);

        // 3. Reset password using valid token
        $rawToken = 'sample-test-token-1234567890';
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => 'karan@example.com'],
            ['token' => Hash::make($rawToken), 'created_at' => Carbon::now()]
        );

        $resetPage = $this->get('/reset-password/'.$rawToken.'?email=karan@example.com');
        $resetPage->assertStatus(200);
        $resetPage->assertSee('Set New Password');

        // 4. Update password
        $updateResponse = $this->post('/reset-password', [
            'token' => $rawToken,
            'email' => 'karan@example.com',
            'password' => 'brandnewpassword123',
            'password_confirmation' => 'brandnewpassword123',
        ]);

        $updateResponse->assertRedirect(route('password.thanks'));

        // 5. Thanks page renders
        $thanksPage = $this->get('/password/thanks');
        $thanksPage->assertStatus(200);
        $thanksPage->assertSee('Thank You!');

        // 6. User can now log in with new password
        $user->refresh();
        $this->assertTrue(Hash::check('brandnewpassword123', $user->password));
    }
}
