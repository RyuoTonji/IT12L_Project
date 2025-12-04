<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\GrillerAttendance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class GrillerAttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = GrillerAttendance::with(['user', 'recorder']);

        if ($request->has('date') && $request->date) {
            $query->whereDate('date', $request->date);
        }

        $attendances = $query->latest('date')->paginate(20);
        $grillers = User::where('role', 'griller')->get();

        return view('manager.griller_attendance.index', compact('attendances', 'grillers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'clock_in' => 'required|date_format:H:i',
            'clock_out' => 'nullable|date_format:H:i|after:clock_in',
            'notes' => 'nullable|string',
        ]);

        $date = Carbon::parse($request->date);
        $clockIn = Carbon::parse($request->date . ' ' . $request->clock_in);
        $clockOut = $request->clock_out ? Carbon::parse($request->date . ' ' . $request->clock_out) : null;

        $totalHours = null;
        if ($clockOut) {
            $totalHours = $clockIn->diffInHours($clockOut, true);
        }

        GrillerAttendance::create([
            'user_id' => $request->user_id,
            'date' => $date,
            'clock_in' => $clockIn,
            'clock_out' => $clockOut,
            'total_hours' => $totalHours ? round($totalHours, 2) : null,
            'recorded_by' => Auth::id(),
            'notes' => $request->notes,
        ]);

        return back()->with('success', 'Griller attendance recorded successfully.');
    }

    public function update(Request $request, GrillerAttendance $attendance)
    {
        $request->validate([
            'clock_in' => 'required|date_format:H:i',
            'clock_out' => 'nullable|date_format:H:i|after:clock_in',
            'notes' => 'nullable|string',
        ]);

        $clockIn = Carbon::parse($attendance->date->format('Y-m-d') . ' ' . $request->clock_in);
        $clockOut = $request->clock_out ? Carbon::parse($attendance->date->format('Y-m-d') . ' ' . $request->clock_out) : null;

        $totalHours = null;
        if ($clockOut) {
            $totalHours = $clockIn->diffInHours($clockOut, true);
        }

        $attendance->update([
            'clock_in' => $clockIn,
            'clock_out' => $clockOut,
            'total_hours' => $totalHours ? round($totalHours, 2) : null,
            'notes' => $request->notes,
        ]);

        return back()->with('success', 'Attendance updated successfully.');
    }

    public function destroy(GrillerAttendance $attendance)
    {
        $attendance->delete();
        return back()->with('success', 'Attendance record deleted successfully.');
    }
}
