<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PayrollRecord;
use App\Models\User;
use App\Services\PayrollService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PayrollController extends Controller
{
    protected $payrollService;

    public function __construct(PayrollService $payrollService)
    {
        $this->payrollService = $payrollService;
    }

    public function index(Request $request)
    {
        $query = PayrollRecord::with(['user', 'approver']);

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        $payrolls = $query->latest()->paginate(20);
        $users = User::whereNotNull('hourly_rate')->get();

        return view('admin.payroll.index', compact('payrolls', 'users'));
    }

    public function create()
    {
        $users = User::whereNotNull('hourly_rate')->get();
        return view('admin.payroll.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
        ]);

        $payroll = $this->payrollService->generatePayroll(
            $request->user_id,
            $request->period_start,
            $request->period_end
        );

        if (!$payroll) {
            return back()->with('error', 'Unable to generate payroll. User may not have hourly rate set or no hours worked in this period.');
        }

        return redirect()->route('admin.payroll.index')->with('success', 'Payroll generated successfully.');
    }

    public function approve(PayrollRecord $payroll)
    {
        $this->payrollService->approvePayroll($payroll->id, Auth::id());
        return back()->with('success', 'Payroll approved successfully.');
    }

    public function markPaid(PayrollRecord $payroll)
    {
        if ($this->payrollService->markAsPaid($payroll->id)) {
            return back()->with('success', 'Payroll marked as paid.');
        }

        return back()->with('error', 'Payroll must be approved before marking as paid.');
    }

    public function destroy(PayrollRecord $payroll)
    {
        if ($payroll->status === 'paid') {
            return back()->with('error', 'Cannot delete paid payroll records.');
        }

        $payroll->delete();
        return back()->with('success', 'Payroll record deleted successfully.');
    }
}
