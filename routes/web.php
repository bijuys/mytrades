<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminMessageController;
use App\Http\Controllers\AdminNewsletterController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StrategyController;
use App\Http\Controllers\TradeJournalController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Landing route
Route::get('/', function () {
    if (auth()->check()) {
        return auth()->user()->isAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('trades.index');
    }

    return redirect()->route('login');
});

// Front End - Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');

    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

    // Email Confirmation & Resend
    Route::get('/confirm-email/{token}', [AuthController::class, 'confirmEmail'])->name('email.confirm');
    Route::get('/resend-confirmation', [AuthController::class, 'showResendConfirmation'])->name('verification.resend.show');
    Route::post('/resend-confirmation', [AuthController::class, 'resendConfirmation'])->name('verification.resend');
});

Route::get('/password/thanks', [AuthController::class, 'showResetThanks'])->name('password.thanks');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Profile & Account Management
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Section 2: User Area (Requires Auth & Super Admin Approval)
Route::middleware(['auth', 'approved'])->group(function () {
    // Journal Entries (View past entries, log new, modify, delete)
    Route::resource('trades', TradeJournalController::class);

    // Performance Summary in many ways
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');

    // Strategy Management (User set dropdown values)
    Route::resource('strategies', StrategyController::class)->only(['index', 'store', 'update', 'destroy']);
});

// Section 3: Admin Area (Requires Super Admin Privileges)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard', [AdminController::class, 'dashboard']);

    // User Management & Approval
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::post('/users/{user}/approve', [AdminController::class, 'approveUser'])->name('users.approve');
    Route::post('/users/{user}/reject', [AdminController::class, 'rejectUser'])->name('users.reject');
    Route::post('/users/{user}/toggle-status', [AdminController::class, 'toggleUserStatus'])->name('users.toggle-status');
    Route::post('/users/{user}/toggle-role', [AdminController::class, 'toggleAdminRole'])->name('users.toggle-role');
    Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('users.delete');

    // Platform-wide Trade Journal Audit
    Route::get('/trades', [AdminController::class, 'trades'])->name('trades');
    Route::delete('/trades/{trade}', [AdminController::class, 'deleteTrade'])->name('trades.delete');

    // Newsletter Broadcasting Facility
    Route::get('/newsletter', [AdminNewsletterController::class, 'index'])->name('newsletter.index');
    Route::post('/newsletter/send', [AdminNewsletterController::class, 'send'])->name('newsletter.send');
    Route::post('/newsletter/test', [AdminNewsletterController::class, 'sendTest'])->name('newsletter.test');

    // Direct User Messaging Interface
    Route::get('/messages', [AdminMessageController::class, 'index'])->name('messages.index');
    Route::post('/messages', [AdminMessageController::class, 'send'])->name('messages.send');
    Route::delete('/messages/{message}', [AdminMessageController::class, 'destroy'])->name('messages.destroy');
});
