<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Export the user's profile data as PDF.
     */
    public function exportData(Request $request)
    {
        $user = $request->user();

        // Get user's orders if they created any
        $orders = \App\Models\Order::where('user_id', $user->id)
            ->with('orderItems.menuItem')
            ->latest()
            ->get();

        // Get user's activity logs
        $activities = \App\Models\Activity::where('user_id', $user->id)
            ->latest()
            ->limit(100)
            ->get();

        // Get user's shift reports if any
        $shiftReports = \App\Models\ShiftReport::where('user_id', $user->id)
            ->latest()
            ->get();

        $exportDate = now()->format('F d, Y');
        $exportTime = now()->format('h:i A');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.profile_data_pdf', compact(
            'user',
            'orders',
            'activities',
            'shiftReports',
            'exportDate',
            'exportTime'
        ));

        return $pdf->download('my_profile_data_' . now()->format('Y-m-d') . '.pdf');
    }
}
