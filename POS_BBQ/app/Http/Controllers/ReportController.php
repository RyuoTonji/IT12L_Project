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

        if ($request->has('action') && $request->action != '') {
            $query->where('action', $request->action);
        }

        if ($request->has('user_id') && $request->user_id != '') {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('date') && $request->date != '') {
            $query->whereDate('created_at', $request->date);
        }

        $activities = $query->latest()->paginate(20);

        return view('reports.index', compact('activities'));
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
