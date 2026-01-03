<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Mail\FeedbackMail;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class FeedbackController extends Controller
{
    public function index()
    {
        return view('user.feedback');
    }

    public function send(Request $request)
    {
        $validated = $request->validate([
            'feedback_type' => 'required|in:feedback,complaint,suggestion',
            'customer_type' => 'required|in:dine-in,pick-up,take-out',
            'customer_name' => 'required|string|max:255',
            'order_id' => 'nullable|exists:orders,id',
            'message' => 'required|string|min:10',
        ]);

        // Verify that the order belongs to the authenticated user if provided
        if (!empty($validated['order_id'])) {
            $order = Order::where('id', $validated['order_id'])
                ->where('user_id', auth()->id())
                ->first();

            if (!$order) {
                return redirect()->back()
                    ->withErrors(['order_id' => 'Order not found or does not belong to you.'])
                    ->withInput();
            }
        }

        // Prepare feedback data
        $feedbackData = [
            'user_id' => auth()->id(),
            'order_id' => $validated['order_id'],
            'customer_name' => $validated['customer_name'],
            'customer_email' => auth()->user()->email,
            'feedback_type' => $validated['feedback_type'],
            'customer_type' => $validated['customer_type'],
            'message' => $validated['message'],
            'status' => 'new',
        ];

        // Save to database
        $feedback = \App\Models\Feedback::create($feedbackData);

        // Send email to admin
        try {
            $recipient = config('mail.from.address');
            Mail::to($recipient)->send(new FeedbackMail($feedbackData));
        } catch (\Exception $e) {
            Log::error('Failed to send feedback email', [
                'feedback_id' => $feedback->id,
                'error' => $e->getMessage()
            ]);
        }

        return redirect()->back()->with('success', 'Your ' . $validated['feedback_type'] . ' has been sent successfully! Thank you for your communication.');
    }

    public function history()
    {
        $feedbacks = \App\Models\Feedback::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('user.feedback_history', compact('feedbacks'));
    }
}
