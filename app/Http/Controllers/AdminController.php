<?php

namespace App\Http\Controllers;

use App\Models\Trade;
use App\Models\User;
use App\Services\ImageService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function __construct(
        protected ImageService $imageService
    ) {}

    /**
     * Admin Console Dashboard
     */
    public function dashboard(): View
    {
        $totalUsers = User::count();
        $pendingUsersCount = User::where('status', 'pending')->whereNotNull('email_verified_at')->count();
        $unconfirmedUsersCount = User::where('status', 'unconfirmed')->orWhereNull('email_verified_at')->where('role', '!=', 'admin')->count();
        $approvedUsersCount = User::where('status', 'approved')->count();
        $totalTrades = Trade::count();
        $totalPlatformPnl = Trade::sum('net_pnl');

        // Recent email-confirmed registrations needing Super Admin review
        $pendingUsers = User::where('status', 'pending')
            ->whereNotNull('email_verified_at')
            ->orderBy('created_at', 'desc')
            ->take(8)
            ->get();

        // Recent trades logged across all users
        $recentTrades = Trade::with(['user', 'strategy'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'pendingUsersCount',
            'unconfirmedUsersCount',
            'approvedUsersCount',
            'totalTrades',
            'totalPlatformPnl',
            'pendingUsers',
            'recentTrades'
        ));
    }

    /**
     * Manage all users.
     */
    public function users(Request $request): View
    {
        $query = User::withCount('trades');

        // Filter by Status
        if ($status = $request->input('status')) {
            if ($status === 'pending') {
                $query->where('status', 'pending')->whereNotNull('email_verified_at');
            } elseif ($status === 'unconfirmed') {
                $query->where(function ($q) {
                    $q->where('status', 'unconfirmed')->orWhereNull('email_verified_at');
                })->where('role', '!=', 'admin');
            } elseif ($status !== 'all') {
                $query->where('status', $status);
            }
        }

        // Filter by Role
        if ($role = $request->input('role')) {
            $query->where('role', $role);
        }

        // Search by name, email, or phone
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        $counts = [
            'all' => User::count(),
            'pending' => User::where('status', 'pending')->whereNotNull('email_verified_at')->count(),
            'unconfirmed' => User::where(function ($q) {
                $q->where('status', 'unconfirmed')->orWhereNull('email_verified_at');
            })->where('role', '!=', 'admin')->count(),
            'approved' => User::where('status', 'approved')->count(),
            'rejected' => User::where('status', 'rejected')->count(),
            'suspended' => User::where('status', 'suspended')->count(),
            'admin' => User::where('role', 'admin')->count(),
        ];

        return view('admin.users', compact('users', 'counts'));
    }

    /**
     * Approve user registration.
     */
    public function approveUser(User $user): RedirectResponse
    {
        $user->update([
            'status' => 'approved',
            'email_verified_at' => $user->email_verified_at ?? Carbon::now(),
            'confirmation_token' => null,
            'approved_at' => Carbon::now(),
            'approved_by' => Auth::id(),
            'rejection_reason' => null,
        ]);

        return back()->with('success', "User {$user->full_name} ({$user->email}) has been APPROVED successfully. They can now log in!");
    }

    /**
     * Reject user registration.
     */
    public function rejectUser(Request $request, User $user): RedirectResponse
    {
        $reason = $request->input('rejection_reason', 'Account registration criteria not met.');

        $user->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
        ]);

        return back()->with('success', "User {$user->full_name} registration has been marked as REJECTED.");
    }

    /**
     * Toggle suspend / active status.
     */
    public function toggleUserStatus(User $user): RedirectResponse
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot suspend your own admin account.');
        }

        $newStatus = ($user->status === 'suspended') ? 'approved' : 'suspended';
        $user->update(['status' => $newStatus]);

        $message = $newStatus === 'suspended' ? 'account has been suspended.' : 'account has been re-activated.';

        return back()->with('success', "User {$user->full_name}'s {$message}");
    }

    /**
     * Toggle admin role.
     */
    public function toggleAdminRole(User $user): RedirectResponse
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot change your own admin role.');
        }

        $newRole = ($user->role === 'admin') ? 'user' : 'admin';
        $user->update(['role' => $newRole]);

        return back()->with('success', "Role for {$user->full_name} updated to {$newRole}.");
    }

    /**
     * Delete user.
     */
    public function deleteUser(User $user): RedirectResponse
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot delete your own admin account.');
        }

        $name = $user->full_name;

        // Clean up screenshots
        foreach ($user->trades as $trade) {
            if ($trade->screenshot) {
                $this->imageService->deleteScreenshot($trade->screenshot);
            }
        }

        $user->delete();

        return back()->with('success', "User '{$name}' and all associated records have been permanently deleted.");
    }

    /**
     * View all trades across all users.
     */
    public function trades(Request $request): View
    {
        $query = Trade::with(['user', 'strategy']);

        // Filter by user
        if ($userId = $request->input('user_id')) {
            $query->where('user_id', $userId);
        }

        // Search by instrument, user name, or strategy
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('instrument', 'like', "%{$search}%")
                    ->orWhere('strategy_name', 'like', "%{$search}%")
                    ->orWhere('exit_reason', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by result
        if ($result = $request->input('result')) {
            if ($result === 'win') {
                $query->where('net_pnl', '>', 0);
            } elseif ($result === 'loss') {
                $query->where('net_pnl', '<', 0);
            }
        }

        // Date filter
        if ($from = $request->input('from_date')) {
            $query->whereDate('entry_datetime', '>=', $from);
        }
        if ($to = $request->input('to_date')) {
            $query->whereDate('entry_datetime', '<=', $to);
        }

        $allMatchingTrades = (clone $query)->get();
        $totalCount = $allMatchingTrades->count();
        $totalPlatformNetPnl = $allMatchingTrades->sum('net_pnl');
        $totalCharges = $allMatchingTrades->sum('charges');

        $trades = $query->orderBy('entry_datetime', 'desc')->paginate(25)->withQueryString();

        $allUsers = User::orderBy('first_name')->get();

        return view('admin.trades', compact('trades', 'allUsers', 'totalCount', 'totalPlatformNetPnl', 'totalCharges'));
    }

    /**
     * Delete a trade as Admin.
     */
    public function deleteTrade(Trade $trade): RedirectResponse
    {
        if ($trade->screenshot) {
            $this->imageService->deleteScreenshot($trade->screenshot);
        }

        $trade->delete();

        return back()->with('success', 'Trade record removed from database.');
    }
}
