<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;

class ManagerController extends Controller
{
    public function index()
    {
        // Fetch recent activities for the dashboard
        $recentActivities = Activity::with('user')->latest()->take(10)->get();
        return view('manager.dashboard', compact('recentActivities'));
    }

    public function reports()
    {
        $activities = Activity::with('user')->latest()->paginate(20);
        return view('manager.reports', compact('activities'));
    }
}
