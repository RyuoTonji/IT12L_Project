<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    /**
     * Display a listing of feedback submissions
     */
    public function index(Request $request)
    {
        $query = Feedback::with(['user', 'order'])
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by type
        if ($request->has('type') && $request->type !== 'all') {
            $query->where('feedback_type', $request->type);
        }

        // Search by customer name, email, or order ID
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%")
                  ->orWhere('order_id', $search);
            });
        }

        $feedbacks = $query->paginate(20);
        $newCount = Feedback::where('status', Feedback::STATUS_NEW)->count();

        return view('admin.feedback.index', compact('feedbacks', 'newCount'));
    }

    /**
     * Display the specified feedback
     */
    public function show($id)
    {
        $feedback = Feedback::with(['user', 'order'])->findOrFail($id);

        // Mark as read if it's new
        if ($feedback->status === Feedback::STATUS_NEW) {
            $feedback->update(['status' => Feedback::STATUS_READ]);
        }

        return view('admin.feedback.show', compact('feedback'));
    }

    /**
     * Update the status of a feedback
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:new,read,resolved',
        ]);

        $feedback = Feedback::findOrFail($id);
        $feedback->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Feedback status updated successfully!');
    }

    /**
     * Remove the specified feedback from storage
     */
    public function destroy($id)
    {
        $feedback = Feedback::findOrFail($id);
        $feedback->delete();

        return redirect()->route('admin.feedback.index')
            ->with('success', 'Feedback deleted successfully!');
    }
}
