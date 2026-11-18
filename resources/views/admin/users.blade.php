@extends('layouts.app')

@section('title', 'Manage Users - Super Admin Console - MyTrades')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-400 mb-1">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-indigo-500">Admin</a>
                <span>/</span>
                <span class="text-indigo-600 dark:text-indigo-400">Users</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                User Management & Approvals
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                Review user registrations, grant access, manage member privileges and monitor trading activity.
            </p>
        </div>
    </div>

    <!-- Status Tabs -->
    <div class="flex flex-wrap items-center gap-2 border-b border-slate-200 dark:border-slate-800 pb-3 text-xs font-bold">
        <a href="{{ route('admin.users') }}" class="px-3.5 py-2 rounded-xl transition-colors {{ !request('status') && !request('role') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            All Users ({{ $counts['all'] }})
        </a>
        <a href="{{ route('admin.users', ['status' => 'pending']) }}" class="px-3.5 py-2 rounded-xl transition-colors {{ request('status') === 'pending' ? 'bg-amber-600 text-white shadow-sm' : 'text-amber-700 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-950/60' }}">
            <i class="fa-solid fa-clock mr-1"></i> Pending Approval ({{ $counts['pending'] }})
        </a>
        <a href="{{ route('admin.users', ['status' => 'unconfirmed']) }}" class="px-3.5 py-2 rounded-xl transition-colors {{ request('status') === 'unconfirmed' ? 'bg-slate-700 text-white shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <i class="fa-regular fa-envelope mr-1"></i> Unconfirmed Email ({{ $counts['unconfirmed'] }})
        </a>
        <a href="{{ route('admin.users', ['status' => 'approved']) }}" class="px-3.5 py-2 rounded-xl transition-colors {{ request('status') === 'approved' ? 'bg-emerald-600 text-white shadow-sm' : 'text-emerald-700 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-950/60' }}">
            <i class="fa-solid fa-check mr-1"></i> Approved ({{ $counts['approved'] }})
        </a>
        <a href="{{ route('admin.users', ['status' => 'rejected']) }}" class="px-3.5 py-2 rounded-xl transition-colors {{ request('status') === 'rejected' ? 'bg-rose-600 text-white shadow-sm' : 'text-rose-700 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/60' }}">
            Rejected ({{ $counts['rejected'] }})
        </a>
        <a href="{{ route('admin.users', ['status' => 'suspended']) }}" class="px-3.5 py-2 rounded-xl transition-colors {{ request('status') === 'suspended' ? 'bg-slate-700 text-white shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            Suspended ({{ $counts['suspended'] }})
        </a>
        <a href="{{ route('admin.users', ['role' => 'admin']) }}" class="px-3.5 py-2 rounded-xl transition-colors {{ request('role') === 'admin' ? 'bg-purple-600 text-white shadow-sm' : 'text-purple-700 dark:text-purple-400 hover:bg-purple-50 dark:hover:bg-purple-950/60' }}">
            <i class="fa-solid fa-shield-halved mr-1"></i> Admins ({{ $counts['admin'] }})
        </a>
    </div>

    <!-- Search Toolbar -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 shadow-sm border border-slate-200/80 dark:border-slate-800">
        <form method="GET" action="{{ route('admin.users') }}" class="flex items-center gap-3">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            @if(request('role'))
                <input type="hidden" name="role" value="{{ request('role') }}">
            @endif

            <div class="flex-1 relative">
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}" 
                       placeholder="Search by name, email, or Indian phone number..." 
                       class="w-full pl-10 pr-4 py-2.5 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3 text-slate-400 text-sm"></i>
            </div>
            <button type="submit" class="px-5 py-2.5 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 rounded-xl shadow-sm">
                Search
            </button>
            @if(request('search'))
                <a href="{{ route('admin.users', request()->only(['status', 'role'])) }}" class="p-2.5 rounded-xl text-slate-400 hover:text-slate-600 border border-slate-200 dark:border-slate-700">
                    <i class="fa-solid fa-xmark"></i>
                </a>
            @endif
        </form>
    </div>

    <!-- Users Table Card -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-sm border border-slate-200/80 dark:border-slate-800 overflow-hidden">
        @if($users->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="border-b border-slate-200/80 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-800/50 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                            <th class="py-4 px-5">User Profile</th>
                            <th class="py-4 px-4">Contact (India)</th>
                            <th class="py-4 px-4">Experience</th>
                            <th class="py-4 px-4">Approval Status</th>
                            <th class="py-4 px-4">Role</th>
                            <th class="py-4 px-4 text-center">Trades</th>
                            <th class="py-4 px-4">Registered</th>
                            <th class="py-4 px-5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-xs">
                        @foreach($users as $user)
                            <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition-colors">
                                
                                <!-- User Profile -->
                                <td class="py-4 px-5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full {{ $user->isAdmin() ? 'bg-purple-100 text-purple-700 dark:bg-purple-900/60 dark:text-purple-300' : 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300' }} font-bold flex items-center justify-center flex-shrink-0 text-xs">
                                            {{ strtoupper(substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="font-extrabold text-sm text-slate-900 dark:text-white">
                                                {{ $user->full_name }}
                                            </div>
                                            <div class="text-slate-400 font-mono text-[11px]">
                                                {{ $user->email }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Indian Phone -->
                                <td class="py-4 px-4 whitespace-nowrap">
                                    <div class="font-mono text-slate-700 dark:text-slate-300">
                                        {{ $user->phone }}
                                    </div>
                                    <span class="text-[10px] text-slate-400">Verified format</span>
                                </td>

                                <!-- Trading Experience -->
                                <td class="py-4 px-4 whitespace-nowrap">
                                    <span class="font-bold text-slate-800 dark:text-slate-200">
                                        {{ $user->trading_experience }} Years
                                    </span>
                                </td>

                                <!-- Approval Status Badge -->
                                <td class="py-4 px-4 whitespace-nowrap">
                                    @if($user->status === 'approved')
                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 dark:bg-emerald-950/70 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 flex items-center gap-1.5 w-fit">
                                            <i class="fa-solid fa-circle-check text-[10px]"></i> Approved
                                        </span>
                                    @elseif($user->isUnconfirmed())
                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-300 dark:border-slate-700 flex items-center gap-1.5 w-fit" title="User has not clicked the email confirmation link yet">
                                            <i class="fa-regular fa-envelope text-[10px]"></i> Unconfirmed
                                        </span>
                                    @elseif($user->status === 'pending')
                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 dark:bg-amber-950/70 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800 flex items-center gap-1.5 w-fit animate-pulse" title="Email verified, ready for admin review">
                                            <i class="fa-solid fa-clock text-[10px]"></i> Awaiting Approval
                                        </span>
                                    @elseif($user->status === 'rejected')
                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-50 dark:bg-rose-950/70 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800 flex items-center gap-1.5 w-fit">
                                            <i class="fa-solid fa-ban text-[10px]"></i> Rejected
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700 flex items-center gap-1.5 w-fit">
                                            <i class="fa-solid fa-pause text-[10px]"></i> Suspended
                                        </span>
                                    @endif
                                </td>

                                <!-- Role -->
                                <td class="py-4 px-4 whitespace-nowrap">
                                    @if($user->role === 'admin')
                                        <span class="px-2 py-0.5 rounded bg-purple-100 dark:bg-purple-900/60 text-purple-700 dark:text-purple-300 font-extrabold text-[10px] uppercase">
                                            Super Admin
                                        </span>
                                    @else
                                        <span class="text-slate-500 font-semibold text-xs">
                                            Trader
                                        </span>
                                    @endif
                                </td>

                                <!-- Trades Count -->
                                <td class="py-4 px-4 text-center whitespace-nowrap">
                                    <a href="{{ route('admin.trades', ['user_id' => $user->id]) }}" class="font-mono font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
                                        {{ $user->trades_count }}
                                    </a>
                                </td>

                                <!-- Registered At -->
                                <td class="py-4 px-4 whitespace-nowrap text-slate-400 text-[11px]">
                                    <div>{{ $user->created_at?->format('d M Y') }}</div>
                                    <div>{{ $user->created_at?->diffForHumans() }}</div>
                                </td>

                                <!-- Actions -->
                                <td class="py-4 px-5 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1.5">
                                        
                                        <!-- Contact / Message User -->
                                        <a href="{{ route('admin.messages.index', ['user_id' => $user->id]) }}" class="p-1.5 rounded-lg text-indigo-600 hover:bg-indigo-50 dark:hover:bg-slate-800" title="Contact {{ $user->first_name }} Directly">
                                            <i class="fa-solid fa-envelope text-xs"></i>
                                        </a>

                                        <!-- Approve Action -->
                                        @if($user->status !== 'approved')
                                            <form method="POST" action="{{ route('admin.users.approve', $user) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="p-1.5 rounded-lg text-emerald-600 hover:bg-emerald-50 dark:hover:bg-slate-800" title="Approve Registration">
                                                    <i class="fa-solid fa-check text-sm"></i>
                                                </button>
                                            </form>
                                        @endif

                                        <!-- Reject Action -->
                                        @if($user->status === 'pending')
                                            <button type="button" onclick="openRejectModal({{ $user->id }}, '{{ addslashes($user->full_name) }}')" class="p-1.5 rounded-lg text-rose-600 hover:bg-rose-50 dark:hover:bg-slate-800" title="Reject Registration">
                                                <i class="fa-solid fa-xmark text-sm"></i>
                                            </button>
                                        @endif

                                        <!-- Suspend / Reactivate Toggle -->
                                        @if($user->id !== auth()->id())
                                            <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-amber-600 hover:bg-amber-50 dark:hover:bg-slate-800" title="{{ $user->status === 'suspended' ? 'Re-activate account' : 'Suspend account' }}">
                                                    <i class="fa-solid {{ $user->status === 'suspended' ? 'fa-play' : 'fa-pause' }} text-xs"></i>
                                                </button>
                                            </form>
                                        @endif

                                        <!-- Admin Role Toggle -->
                                        @if($user->id !== auth()->id())
                                            <form method="POST" action="{{ route('admin.users.toggle-role', $user) }}" onsubmit="return confirm('Change admin role for {{ $user->full_name }}?');" class="inline">
                                                @csrf
                                                <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-purple-600 hover:bg-purple-50 dark:hover:bg-slate-800" title="Toggle Admin Role">
                                                    <i class="fa-solid fa-shield-halved text-xs"></i>
                                                </button>
                                            </form>
                                        @endif

                                        <!-- Delete User -->
                                        @if($user->id !== auth()->id())
                                            <form method="POST" action="{{ route('admin.users.delete', $user) }}" onsubmit="return confirm('Permanently delete user {{ $user->full_name }} and all their trades? This cannot be undone.');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-slate-800" title="Delete Account">
                                                    <i class="fa-solid fa-trash text-xs"></i>
                                                </button>
                                            </form>
                                        @endif

                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($users->hasPages())
                <div class="p-5 border-t border-slate-200 dark:border-slate-800">
                    {{ $users->links() }}
                </div>
            @endif
        @else
            <div class="py-16 text-center text-slate-400">
                <i class="fa-solid fa-users-slash text-4xl mb-3"></i>
                <div class="font-bold text-slate-700 dark:text-slate-300">No Users Match the Selected Filters</div>
                <div class="text-xs mt-1">Try resetting the status filter or search query.</div>
            </div>
        @endif
    </div>

</div>

<!-- Reject Reason Modal -->
<div id="rejectModal" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 max-w-md w-full border border-slate-200 dark:border-slate-800 shadow-2xl">
        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">Reject User Registration</h3>
        <p class="text-xs text-slate-500 mb-4">Provide a reason for declining <span id="rejectUserName" class="font-bold text-slate-800 dark:text-slate-200"></span>.</p>
        
        <form id="rejectForm" method="POST" action="">
            @csrf
            <div class="mb-4">
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Reason for Rejection</label>
                <textarea name="rejection_reason" rows="3" required placeholder="e.g. Incomplete phone number verification, not an active trader, etc." class="w-full px-4 py-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm"></textarea>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeRejectModal()" class="px-4 py-2.5 text-xs font-bold text-slate-500 hover:bg-slate-100 rounded-xl">Cancel</button>
                <button type="submit" class="px-5 py-2.5 text-xs font-bold text-white bg-rose-600 hover:bg-rose-500 rounded-xl">Confirm Rejection</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openRejectModal(userId, userName) {
        document.getElementById('rejectUserName').textContent = userName;
        document.getElementById('rejectForm').action = '/admin/users/' + userId + '/reject';
        document.getElementById('rejectModal').classList.remove('hidden');
    }

    function closeRejectModal() {
        document.getElementById('rejectModal').classList.add('hidden');
    }
</script>
@endpush
@endsection
