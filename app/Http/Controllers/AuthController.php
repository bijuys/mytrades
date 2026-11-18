<?php

namespace App\Http\Controllers;

use App\Mail\ResetPasswordMail;
use App\Mail\WelcomeConfirmEmail;
use App\Models\Strategy;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Show the Login page.
     */
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return Auth::user()->isAdmin()
                ? redirect()->route('admin.dashboard')
                : redirect()->route('trades.index');
        }

        return view('auth.login');
    }

    /**
     * Handle Login submission.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        // Check if user exists
        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return back()->withInput($request->only('email', 'remember'))
                ->withErrors(['email' => 'The provided credentials do not match our records.']);
        }

        // Check approval status (Super Admin bypasses check)
        if (! $user->isAdmin()) {
            if ($user->status === 'unconfirmed' || is_null($user->email_verified_at)) {
                return back()->withInput($request->only('email'))
                    ->with('warning', 'Please confirm your email address before your account can be submitted for Super Admin approval. Check your inbox for the confirmation link.')
                    ->with('resend_email', $user->email);
            }

            if ($user->isPending()) {
                return back()->withInput($request->only('email'))
                    ->with('warning', 'Your email is confirmed and your registration is currently awaiting Super Admin approval. Please check back shortly or contact your administrator.');
            }

            if ($user->isRejected()) {
                $reason = $user->rejection_reason ? " Reason: {$user->rejection_reason}" : '';

                return back()->withInput($request->only('email'))
                    ->with('error', 'Your registration was reviewed and declined by the administrator.'.$reason);
            }

            if ($user->status === 'suspended') {
                return back()->withInput($request->only('email'))
                    ->with('error', 'Your account has been suspended. Please contact support.');
            }
        }

        Auth::login($user, $remember);
        $request->session()->regenerate();

        if ($user->isAdmin()) {
            return redirect()->intended(route('admin.dashboard'))
                ->with('success', "Welcome back, Admin {$user->first_name}!");
        }

        return redirect()->intended(route('trades.index'))
            ->with('success', "Welcome back, {$user->first_name}! Ready to journal your trades?");
    }

    /**
     * Show the Signup page.
     */
    public function showRegister(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('trades.index');
        }

        return view('auth.register');
    }

    /**
     * Handle Signup submission.
     */
    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:50'],
            'last_name' => ['required', 'string', 'max:50'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => [
                'required',
                'string',
                'regex:/^(\+91[\-\s]?)?[6789]\d{9}$/', // Indian phone validation: 10 digits starting with 6-9, optional +91
            ],
            'trading_experience' => ['required', 'integer', 'min:0', 'max:60'],
            'agreed_to_terms' => ['accepted'],
        ], [
            'phone.regex' => 'Please enter a valid 10-digit Indian mobile number (e.g. 9876543210 or +91 9876543210).',
            'agreed_to_terms.accepted' => 'You must agree to the Terms of Service to create an account.',
        ]);

        // Clean Indian phone number formatting
        $cleanPhone = preg_replace('/[^\d+]/', '', $validated['phone']);

        $confirmationToken = Str::random(64);

        $user = User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'name' => trim($validated['first_name'].' '.$validated['last_name']),
            'email' => strtolower($validated['email']),
            'password' => Hash::make($validated['password']),
            'phone' => $cleanPhone,
            'trading_experience' => $validated['trading_experience'],
            'agreed_to_terms' => true,
            'role' => 'user',
            'status' => 'unconfirmed', // Must confirm email before Admin review
            'confirmation_token' => $confirmationToken,
            'email_verified_at' => null,
        ]);

        // Seed default starter strategies for this new user
        $defaultStrategies = [
            ['name' => 'Breakout & Retest', 'color' => '#3b82f6', 'description' => 'Break of key support/resistance level followed by a volume-backed retest.'],
            ['name' => 'CPR & Pivot Points', 'color' => '#10b981', 'description' => 'Trades taken at Central Pivot Range boundaries and daily support/resistance pivots.'],
            ['name' => 'Opening Range Breakout (ORB)', 'color' => '#8b5cf6', 'description' => 'First 15-minute or 5-minute high/low breakout with high momentum.'],
            ['name' => 'VWAP Pullback', 'color' => '#f59e0b', 'description' => 'Trend continuation bounce or rejection near the Volume-Weighted Average Price.'],
            ['name' => 'Support & Resistance Reversal', 'color' => '#ec4899', 'description' => 'Reversal candlesticks (pin bar, engulfing) at major higher-timeframe support/resistance.'],
            ['name' => 'Option Scalping', 'color' => '#06b6d4', 'description' => 'Quick momentum options trades targeting 10-25 points in index contracts.'],
        ];

        foreach ($defaultStrategies as $strat) {
            Strategy::create([
                'user_id' => $user->id,
                'name' => $strat['name'],
                'color' => $strat['color'],
                'description' => $strat['description'],
                'is_active' => true,
            ]);
        }

        $confirmUrl = route('email.confirm', ['token' => $confirmationToken]);

        // Dispatch warm welcome & confirmation email
        try {
            Mail::to($user->email)->send(new WelcomeConfirmEmail($user, $confirmUrl));
        } catch (\Throwable $e) {
            Log::warning('Welcome email dispatch note: '.$e->getMessage());
        }

        return redirect()->route('login')->with('pending_notice', [
            'title' => 'Welcome to MyTrades! Please Confirm Your Email',
            'message' => "Thank you for signing up, {$user->first_name}! We have sent a welcome email to {$user->email}. Please click the 'Confirm Email' button inside to confirm your signup. Once confirmed, your registration will be submitted for Super Admin approval.",
            'email' => $user->email,
            'unconfirmed' => true,
        ])->with('dev_confirm_url', $confirmUrl);
    }

    /**
     * Show Forgot Password view.
     */
    public function showForgotPassword(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Send Password Reset Link.
     */
    public function sendResetLink(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $email = strtolower($request->email);
        $user = User::where('email', $email)->first();

        if (! $user) {
            // Keep security vague or notify user politely
            return back()->with('status', 'If an account exists with that email address, we have sent a password reset link.');
        }

        // Generate reset token
        $token = Str::random(64);

        // Store or update in password_reset_tokens table
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'token' => Hash::make($token),
                'created_at' => Carbon::now(),
            ]
        );

        $resetUrl = route('password.reset', ['token' => $token, 'email' => $email]);

        // Send Email
        try {
            Mail::to($user->email)->send(new ResetPasswordMail($user, $resetUrl, $token));
        } catch (\Throwable $e) {
            // If mail server fails or in dev mode, log it
            Log::warning('Password reset email dispatch note: '.$e->getMessage());
        }

        return back()->with('status', 'We have emailed your password reset link!')
            ->with('dev_reset_url', $resetUrl); // Convenient for local testing
    }

    /**
     * Show Set New Password Form.
     */
    public function showResetPassword(Request $request, string $token): View|RedirectResponse
    {
        $email = $request->query('email');

        if (! $email) {
            return redirect()->route('password.request')->withErrors(['email' => 'Invalid password reset request link.']);
        }

        return view('auth.reset-password', [
            'token' => $token,
            'email' => $email,
        ]);
    }

    /**
     * Handle Password Reset submission.
     */
    public function resetPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $email = strtolower($request->email);

        $record = DB::table('password_reset_tokens')->where('email', $email)->first();

        if (! $record) {
            return back()->withErrors(['email' => 'Invalid or expired password reset token. Please request a new link.']);
        }

        // Check if token expired (> 60 minutes)
        if (Carbon::parse($record->created_at)->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $email)->delete();

            return back()->withErrors(['email' => 'This password reset link has expired. Please request a new one.']);
        }

        // Check token hash
        if (! Hash::check($request->token, $record->token)) {
            return back()->withErrors(['email' => 'Invalid password reset token.']);
        }

        $user = User::where('email', $email)->first();
        if (! $user) {
            return back()->withErrors(['email' => 'Unable to find a user with this email address.']);
        }

        // Update user password
        $user->password = Hash::make($request->password);
        $user->save();

        // Delete used token
        DB::table('password_reset_tokens')->where('email', $email)->delete();

        // Redirect to Thanks page
        return redirect()->route('password.thanks');
    }

    /**
     * Show Password Reset Thanks page.
     */
    public function showResetThanks(): View
    {
        return view('auth.thanks');
    }

    /**
     * Confirm user email address from the welcome email link.
     */
    public function confirmEmail(string $token): RedirectResponse
    {
        $user = User::where('confirmation_token', $token)->first();

        if (! $user) {
            return redirect()->route('login')
                ->with('warning', 'This email confirmation link is invalid or has already been used. If your account has been approved by the Super Admin, you can log in below.');
        }

        $user->update([
            'email_verified_at' => Carbon::now(),
            'confirmation_token' => null,
            'status' => 'pending', // Transition from 'unconfirmed' to 'pending' admin review!
        ]);

        return redirect()->route('login')->with('pending_notice', [
            'title' => 'Email Confirmed Successfully! 🎉',
            'message' => "Thank you, {$user->first_name}! Your email address ({$user->email}) is now verified. Your application has been submitted to the Super Admin for review and approval. You will be able to log in as soon as an administrator approves your account.",
            'email' => $user->email,
            'confirmed' => true,
        ]);
    }

    /**
     * Show form to resend confirmation email.
     */
    public function showResendConfirmation(): View
    {
        return view('auth.resend-confirmation');
    }

    /**
     * Resend Welcome & Confirmation Email.
     */
    public function resendConfirmation(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $email = strtolower($request->email);
        $user = User::where('email', $email)->first();

        if (! $user) {
            return back()->with('status', 'If an unconfirmed registration exists with that email address, we have resent the confirmation link.');
        }

        if ($user->isEmailVerified() && $user->status !== 'unconfirmed') {
            return redirect()->route('login')
                ->with('info', 'Your email is already confirmed! If you are awaiting approval, our administrators will review your profile shortly.');
        }

        // Generate a new token if not present
        if (empty($user->confirmation_token)) {
            $user->confirmation_token = Str::random(64);
            $user->status = 'unconfirmed';
            $user->save();
        }

        $confirmUrl = route('email.confirm', ['token' => $user->confirmation_token]);

        try {
            Mail::to($user->email)->send(new WelcomeConfirmEmail($user, $confirmUrl));
        } catch (\Throwable $e) {
            Log::warning('Resend confirmation email failed: '.$e->getMessage());
        }

        return back()->with('status', "We have resent the welcome and confirmation email to {$user->email}! Please check your inbox.")
            ->with('dev_confirm_url', $confirmUrl);
    }

    /**
     * Log the user out.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have been successfully logged out. See you next trading session!');
    }
}
