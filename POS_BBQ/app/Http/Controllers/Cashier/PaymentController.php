<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Order;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $payments = Payment::with(['order'])
            ->when($user->branch_id, function ($query) use ($user) {
                return $query->where('branch_id', $user->branch_id);
            })
            ->latest()
            ->paginate(10);

        return view('cashier.payments.index', compact('payments'));
    }

    public function create(Request $request)
    {
        $orderId = $request->input('order');

        if (!$orderId) {
            return redirect()->route('orders.index')
                ->with('error', 'No order selected for payment');
        }

        $order = Order::with(['orderItems.menuItem', 'payments'])
            ->findOrFail($orderId);

        // Check if user can access this order
        $user = Auth::user();
        if ($user->branch_id && $order->branch_id !== $user->branch_id) {
            return redirect()->route('orders.index')
                ->with('error', 'Unauthorized access to this order.');
        }

        // Check if order is already paid
        if ($order->payment_status === 'paid') {
            return redirect()->route('orders.show', $order)
                ->with('error', 'This order has already been paid');
        }

        // Calculate remaining amount to be paid
        $paidAmount = $order->payments->sum('amount');
        $remainingAmount = $order->total_amount - $paidAmount;

        return view('cashier.payments.create', compact('order', 'paidAmount', 'remainingAmount'));
    }

    public function store(Request $request)
    {
        \Log::info('Payment Store Method hit', $request->all());

        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string', // Allow any string now, UI will restrict
            'payment_details' => 'nullable|array',
        ]);

        $order = Order::findOrFail($request->order_id);

        // Check if order is already paid
        if ($order->payment_status === 'paid') {
            return redirect()->route('orders.show', $order)
                ->with('error', 'This order has already been paid');
        }

        // Calculate remaining amount to be paid
        $paidAmount = $order->payments->sum('amount');
        $remainingAmount = $order->total_amount - $paidAmount;

        // Validate payment amount
        if ($request->amount > $remainingAmount) {
            return back()->with('error', 'Payment amount cannot exceed the remaining balance')->withInput();
        }

        // Start a database transaction
        DB::beginTransaction();

        try {
            // Create the payment
            $payment = new Payment();
            $payment->order_id = $request->order_id;
            $payment->branch_id = $order->branch_id; // Set branch from order
            $payment->amount = $request->amount;
            $payment->payment_method = $request->payment_method;

            // Store payment details if provided
            if ($request->filled('payment_details')) {
                $payment->payment_details = $request->payment_details;
            }

            $payment->save();

            // Update order payment status if fully paid
            // REFRESH payments relationship to ensure we have the latest data if needed, though manual calculation is safer
            $order->refresh(); // Refresh order to ensure we have fresh data
            $paidAmount = $order->payments->sum('amount'); // This might still use cached valid, so let's force valid

            \Log::info('Payment Processing', [
                'order_id' => $order->id,
                'total_amount' => $order->total_amount,
                'previous_payments_sum' => $paidAmount, // This is from DB
                'current_payment_amount' => $request->amount,
                'calculated_new_paid' => $paidAmount + $request->amount
            ]);

            $newPaidAmount = $order->payments()->sum('amount') + $request->amount; // Use query builder () to get fresh sum from DB directly? No, creating payment above is in transaction. 
            // Better: use the logic we had but be verbose
            $newPaidAmount = $order->payments->sum('amount') + $request->amount;

            if ($newPaidAmount >= $order->total_amount) {
                \Log::info('Marking as paid');
                $order->payment_status = 'paid';

                // If order is served and now paid, mark it as completed
                if ($order->status === 'served') {
                    $order->status = 'completed';
                }

                $saved = $order->save();
                \Log::info('Order saved result: ' . ($saved ? 'true' : 'false'));
            } else {
                \Log::info('Not marking as paid', ['newPaid' => $newPaidAmount, 'total' => $order->total_amount]);
            }

            // Automated Activity Logging
            Activity::create([
                'user_id' => Auth::id(),
                'action' => 'process_payment',
                'details' => "Processed payment for Order #{$order->id} - Amount: {$payment->amount} ({$payment->payment_method})",
                'status' => 'info',
                'related_id' => $payment->id,
                'related_model' => Payment::class,
            ]);

            DB::commit();

            return redirect()->route('orders.show', $order)
                ->with('success', 'Payment processed successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error processing payment: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Payment $payment)
    {
        $payment->load('order');
        return view('cashier.payments.show', compact('payment'));
    }

    public function destroy(Payment $payment)
    {
        if (Auth::user()->role !== 'admin' && Auth::user()->role !== 'manager') {
            return redirect()->route('payments.index')
                ->with('error', 'Unauthorized action.');
        }

        // Only allow refunding payments if the order is not completed
        if ($payment->order->status === 'completed') {
            return redirect()->route('payments.index')
                ->with('error', 'Cannot refund payment for a completed order');
        }

        // Start a database transaction
        DB::beginTransaction();

        try {
            // Mark the payment as refunded
            $payment->delete();

            // Update order payment status
            $order = $payment->order;
            $order->payment_status = 'refunded';
            $order->save();

            // Automated Activity Logging
            Activity::create([
                'user_id' => Auth::id(),
                'action' => 'refund_payment',
                'details' => "Refunded payment for Order #{$order->id} - Amount: {$payment->amount}",
                'status' => 'warning', // Warning for refund actions
                'related_id' => $payment->id,
                'related_model' => Payment::class,
            ]);

            DB::commit();

            return redirect()->route('payments.index')
                ->with('success', 'Payment refunded successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error refunding payment: ' . $e->getMessage());
        }
    }
}
