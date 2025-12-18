<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Activity::with('user');

        // Tab Filtering (Role-based)
        $activeTab = $request->get('role', 'cashier');
        $validTabs = ['cashier', 'inventory', 'manager'];

        if (!in_array($activeTab, $validTabs)) {
            $activeTab = 'cashier';
        }

        // Apply role filter based on tab
        $query->whereHas('user', function ($q) use ($activeTab) {
            $q->where('role', $activeTab);
        });

        if ($request->has('action') && $request->action != '') {
            $query->where('action', $request->action);
        }

        if ($request->has('user_id') && $request->user_id != '') {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('date') && $request->date != '') {
            $query->whereDate('created_at', $request->date);
        }

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('action', 'like', '%' . $search . '%')
                    ->orWhere('details', 'like', '%' . $search . '%');
            });
        }

        $activities = $query->latest()->paginate(20)->appends($request->all());
        $users = \App\Models\User::orderBy('name')->get();

        return view('reports.index', compact('activities', 'users', 'activeTab'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'action' => 'required|string',
            'details' => 'required|string',
        ]);

        Activity::create([
            'user_id' => Auth::id(),
            'action' => $request->action,
            'details' => $request->details,
            'status' => 'info',
        ]);

        return back()->with('success', 'Activity logged successfully.');
    }
}
