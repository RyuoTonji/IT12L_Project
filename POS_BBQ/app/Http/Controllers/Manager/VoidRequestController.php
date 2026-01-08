<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\VoidRequest;
use App\Models\Order;
use App\Models\Activity;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VoidRequestController extends Controller
{
    public function index(Request $request)
    {
        $relationships = [
            'order.user',
            'order.branch',
            'order.table',
            'order.orderItems.menuItem',
            'order.payments',
            'order.voidRequests.approver',
            'order.voidRequests.requester',
            'requester'
        ];

        $search = $request->input('search');

        $queryCallback = function ($query) use ($search) {
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('order_id', 'like', "%{$search}%")
                        ->orWhereHas('order', function ($q) use ($search) {
                            $q->where('customer_name', 'like', "%{$search}%")
                                ->orWhereHas('branch', function ($q) use ($search) {
                                    $q->where('name', 'like', "%{$search}%");
                                });
                        });
                });
            }
        };

        $pendingQuery = VoidRequest::with($relationships)
            ->where('status', 'pending');

        $queryCallback($pendingQuery);

        $voidRequests = $pendingQuery->latest()
            ->paginate(10, ['*'], 'pending_page');

        $historyQuery = VoidRequest::with(array_merge($relationships, ['approver']))
            ->whereIn('status', ['approved', 'rejected']);

        $queryCallback($historyQuery);

        $voidRequestHistory = $historyQuery->latest('updated_at')
            ->paginate(10, ['*'], 'history_page');

        return view('manager.void_requests.index', compact('voidRequests', 'voidRequestHistory', 'search'));
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

            return redirect()->route('manager.void-requests.index')->with('success', 'Void request approved. Order cancelled.');

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


        return redirect()->route('manager.void-requests.index')->with('success', 'Void request rejected.');
    }

    public function exportPdf()
    {
        $voidRequests = VoidRequest::with(['order.user', 'requester', 'approver'])
            ->where('status', 'pending')
            ->latest()
            ->get();

        // Get metadata
        $exporter = Auth::user();
        $branch = $exporter->branch; // Assuming user has branch relationship or property
        $exportDate = now()->format('F d, Y');
        $exportTime = now()->format('h:i A');

        $pdf = Pdf::loadView('exports.void_requests_pdf', compact(
            'voidRequests',
            'exporter',
            'branch',
            'exportDate',
            'exportTime'
        ));
        return $pdf->download('void_requests_' . date('Y-m-d') . '.pdf');
    }
}
