<?php

namespace App\Services;

use App\Models\PayrollRecord;
use App\Models\User;
use App\Models\ShiftReport;
use App\Models\GrillerAttendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PayrollService
{
    /**
     * Generate payroll for a user for a given period.
     *
     * @param int $userId
     * @param string $periodStart
     * @param string $periodEnd
     * @return PayrollRecord|false
     */
    public function generatePayroll($userId, $periodStart, $periodEnd)
    {
        $user = User::find($userId);
        if (!$user || !$user->hourly_rate) {
            return false;
        }

        $start = Carbon::parse($periodStart);
        $end = Carbon::parse($periodEnd);

        // Calculate total hours based on role
        $totalHours = $this->calculateTotalHours($user, $start, $end);

        if ($totalHours <= 0) {
            return false;
        }

        // Calculate pay
        $grossPay = $totalHours * $user->hourly_rate;
        $deductions = 0; // Can be customized later
        $netPay = $grossPay - $deductions;

        // Create payroll record
        $payroll = PayrollRecord::create([
            'user_id' => $userId,
            'period_start' => $start,
            'period_end' => $end,
            'total_hours' => round($totalHours, 2),
            'hourly_rate' => $user->hourly_rate,
            'gross_pay' => round($grossPay, 2),
            'deductions' => round($deductions, 2),
            'net_pay' => round($netPay, 2),
            'status' => 'pending',
        ]);

        return $payroll;
    }

    /**
     * Calculate total hours worked for a user in a period.
     *
     * @param User $user
     * @param Carbon $start
     * @param Carbon $end
     * @return float
     */
    protected function calculateTotalHours(User $user, Carbon $start, Carbon $end)
    {
        $totalHours = 0;

        if ($user->role === 'griller') {
            // For grillers, use griller_attendance table
            $totalHours = GrillerAttendance::where('user_id', $user->id)
                ->whereBetween('date', [$start, $end])
                ->whereNotNull('total_hours')
                ->sum('total_hours');
        } else {
            // For other roles, use shift_reports table
            $totalHours = ShiftReport::where('user_id', $user->id)
                ->whereBetween('shift_date', [$start, $end])
                ->whereNotNull('total_hours')
                ->sum('total_hours');
        }

        return (float) $totalHours;
    }

    /**
     * Approve a payroll record.
     *
     * @param int $payrollId
     * @param int $approverId
     * @return bool
     */
    public function approvePayroll($payrollId, $approverId)
    {
        $payroll = PayrollRecord::find($payrollId);
        if (!$payroll) {
            return false;
        }

        $payroll->update([
            'status' => 'approved',
            'approved_by' => $approverId,
            'approved_at' => now(),
        ]);

        return true;
    }

    /**
     * Mark payroll as paid.
     *
     * @param int $payrollId
     * @return bool
     */
    public function markAsPaid($payrollId)
    {
        $payroll = PayrollRecord::find($payrollId);
        if (!$payroll || $payroll->status !== 'approved') {
            return false;
        }

        $payroll->update(['status' => 'paid']);
        return true;
    }
}
