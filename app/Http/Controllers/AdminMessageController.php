<?php

namespace App\Http\Controllers;

use App\Mail\DirectUserMessageMail;
use App\Models\User;
use App\Models\UserMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class AdminMessageController extends Controller
{
    /**
     * Display messaging interface & history.
     */
    public function index(Request $request): View
    {
        $selectedUserId = $request->query('user_id');

        $query = UserMessage::with(['user', 'admin']);

        if ($selectedUserId) {
            $query->where('user_id', $selectedUserId);
        }

        $messages = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        $allUsers = User::orderBy('first_name')->get();

        $selectedUser = $selectedUserId ? User::find($selectedUserId) : null;

        return view('admin.messages.index', compact(
            'messages',
            'allUsers',
            'selectedUserId',
            'selectedUser'
        ));
    }

    /**
     * Send direct message to a user.
     */
    public function send(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'min:3'],
            'send_email' => ['nullable'],
        ]);

        $recipient = User::findOrFail($validated['user_id']);
        $admin = Auth::user();
        $sendEmail = $request->has('send_email') ? $request->boolean('send_email') : true;

        $msgRecord = UserMessage::create([
            'user_id' => $recipient->id,
            'admin_id' => $admin->id,
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'sent_email' => $sendEmail,
        ]);

        if ($sendEmail) {
            try {
                Mail::to($recipient->email)->send(
                    new DirectUserMessageMail($recipient, $admin, $validated['subject'], $validated['message'])
                );
            } catch (\Throwable $e) {
                Log::warning("Direct message email failed for {$recipient->email}: ".$e->getMessage());
            }
        }

        return redirect()->route('admin.messages.index', ['user_id' => $recipient->id])
            ->with('success', "Message sent to {$recipient->full_name} ({$recipient->email}) successfully!");
    }

    /**
     * Delete a communication record.
     */
    public function destroy(UserMessage $message): RedirectResponse
    {
        $message->delete();

        return back()->with('success', 'Message log entry removed.');
    }
}
