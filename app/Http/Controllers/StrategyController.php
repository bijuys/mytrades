<?php

namespace App\Http\Controllers;

use App\Models\Strategy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StrategyController extends Controller
{
    /**
     * List user's strategies with usage metrics.
     */
    public function index(): View
    {
        $user = Auth::user();
        $strategies = Strategy::where('user_id', $user->id)
            ->withCount('trades')
            ->orderBy('name')
            ->get();

        return view('strategies.index', compact('strategies'));
    }

    /**
     * Store a new strategy. Supports HTML form submission and JSON/AJAX.
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'color' => ['nullable', 'string', 'max:25'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $user = Auth::user();

        $strategy = Strategy::create([
            'user_id' => $user->id,
            'name' => trim($validated['name']),
            'color' => $validated['color'] ?? '#4f46e5',
            'description' => $validated['description'] ?? null,
            'is_active' => true,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'strategy' => $strategy,
                'message' => 'Strategy added successfully!',
            ]);
        }

        return redirect()->route('strategies.index')
            ->with('success', "Strategy '{$strategy->name}' added successfully!");
    }

    /**
     * Update an existing strategy.
     */
    public function update(Request $request, Strategy $strategy): RedirectResponse
    {
        if ($strategy->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'color' => ['nullable', 'string', 'max:25'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $strategy->update([
            'name' => trim($validated['name']),
            'color' => $validated['color'] ?? $strategy->color,
            'description' => $validated['description'],
        ]);

        return redirect()->route('strategies.index')
            ->with('success', "Strategy '{$strategy->name}' updated!");
    }

    /**
     * Delete a strategy.
     */
    public function destroy(Strategy $strategy): RedirectResponse
    {
        if ($strategy->user_id !== Auth::id()) {
            abort(403);
        }

        $strategyName = $strategy->name;
        $strategy->delete();

        return redirect()->route('strategies.index')
            ->with('success', "Strategy '{$strategyName}' deleted.");
    }
}
