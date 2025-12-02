<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\VoidRequest;
use App\Models\Order;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VoidRequestController extends Controller
{
    public function index()
    {
        $voidRequests = VoidRequest::with(['order', 'requester'])
            ->where('status', 'pending')
            ->latest()
            ->paginate(10);

        return view('manager.void_requests.index', compact('voidRequests'));
    }

    public function approve(Request $request, VoidRequest $voidRequest)
    {
        if ($voidRequest->status !== 'pending') {
            return back()->with('error', 'This request has already been processed.');
        }

        DB::beginTransaction();

        try {
            // Update request status
            $voidRequest->update([
                'status' => 'approved',
                'approver_id' => Auth::id(),
            ]);

            // Cancel the order
            $order = $voidRequest->order;
            $oldStatus = $order->status;
            $order->status = 'cancelled';
            if ($order->payment_status === 'paid') {
                $order->payment_status = 'refunded';
                // Ideally, we should also record a negative payment or refund transaction here
                // For now, we update the status which ShiftReportController uses to calculate refunds
            }
            $order->save();

            // Free up table if applicable
            if ($order->table_id) {
                $table = $order->table;
                $table->status = 'available';
                $table->save();
            }

            // Log activity
            Activity::create([
                'user_id' => Auth::id(),
                'action' => 'approve_void',
                'details' => "Approved void request for Order #{$order->id}. Reason: {$voidRequest->reason}",
                'status' => 'warning',
                'related_id' => $order->id,
                'related_model' => Order::class,
            ]);

            DB::commit();

            return back()->with('success', 'Void request approved. Order cancelled.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error approving request: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, VoidRequest $voidRequest)
    {
        if ($voidRequest->status !== 'pending') {
            return back()->with('error', 'This request has already been processed.');
        }

        $voidRequest->update([
            'status' => 'rejected',
            'approver_id' => Auth::id(),
        ]);

        return back()->with('success', 'Void request rejected.');
    }
}
