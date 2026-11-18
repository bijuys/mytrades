<?php

namespace App\Http\Controllers;

use App\Mail\NewsletterMail;
use App\Models\Newsletter;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class AdminNewsletterController extends Controller
{
    /**
     * Show Newsletter management & composer screen.
     */
    public function index(): View
    {
        $newsletters = Newsletter::with('admin')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $approvedUsersCount = User::where('status', 'approved')->count();
        $totalUsersCount = User::count();

        return view('admin.newsletter.index', compact(
            'newsletters',
            'approvedUsersCount',
            'totalUsersCount'
        ));
    }

    /**
     * Broadcast a newsletter to the selected audience.
     */
    public function send(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string', 'min:10'],
            'target_audience' => ['required', 'in:approved,all'],
        ]);

        $query = User::query();

        if ($validated['target_audience'] === 'approved') {
            $query->where('status', 'approved');
        }

        $recipients = $query->get();

        if ($recipients->isEmpty()) {
            return back()->withInput()->with('error', 'No recipients found for the selected audience.');
        }

        $sentCount = 0;
        foreach ($recipients as $recipient) {
            try {
                Mail::to($recipient->email)->send(
                    new NewsletterMail($recipient, $validated['subject'], $validated['content'])
                );
                $sentCount++;
            } catch (\Throwable $e) {
                Log::warning("Newsletter delivery failed for {$recipient->email}: ".$e->getMessage());
            }
        }

        Newsletter::create([
            'admin_id' => Auth::id(),
            'subject' => $validated['subject'],
            'content' => $validated['content'],
            'target_audience' => $validated['target_audience'],
            'recipients_count' => $sentCount,
            'sent_at' => Carbon::now(),
        ]);

        return redirect()->route('admin.newsletter.index')
            ->with('success', "Newsletter '{$validated['subject']}' was successfully dispatched to {$sentCount} recipient(s)!");
    }

    /**
     * Send a preview test newsletter to the current admin's email.
     */
    public function sendTest(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string', 'min:5'],
        ]);

        $admin = Auth::user();

        try {
            Mail::to($admin->email)->send(
                new NewsletterMail($admin, '[TEST PREVIEW] '.$validated['subject'], $validated['content'])
            );

            return back()->withInput()
                ->with('success', "Test preview email successfully sent to your admin email ({$admin->email})!");
        } catch (\Throwable $e) {
            Log::warning('Newsletter test email failed: '.$e->getMessage());

            return back()->withInput()
                ->with('error', 'Could not send test email: '.$e->getMessage());
        }
    }
}
