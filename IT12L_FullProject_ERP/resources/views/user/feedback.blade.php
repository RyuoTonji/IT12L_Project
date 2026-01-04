@extends('layouts.app')

@section('title', 'Send Feedback - BBQ Lagao & Beef Pares')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white py-3">
                    <h4 class="mb-0"><i class="fas fa-comment-dots me-2"></i>Send Feedback or Complaint</h4>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('feedback.send') }}" method="POST">
                        @csrf

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="feedback_type" class="form-label fw-bold">Type of Communication</label>
                                <select class="form-select @error('feedback_type') is-invalid @enderror" id="feedback_type" name="feedback_type" required>
                                    <option value="" selected disabled>Select type...</option>
                                    <option value="feedback" {{ old('feedback_type') == 'feedback' ? 'selected' : '' }}>Feedback</option>
                                    <option value="complaint" {{ old('feedback_type') == 'complaint' ? 'selected' : '' }}>Complaint</option>
                                    <option value="suggestion" {{ old('feedback_type') == 'suggestion' ? 'selected' : '' }}>Suggestion</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="customer_type" class="form-label fw-bold">Customer Type</label>
                                <select class="form-select @error('customer_type') is-invalid @enderror" id="customer_type" name="customer_type" required>
                                    <option value="" selected disabled>Select type...</option>
                                    <option value="dine-in" {{ old('customer_type') == 'dine-in' ? 'selected' : '' }}>Dine-in</option>
                                    <option value="pick-up" {{ old('customer_type') == 'pick-up' ? 'selected' : '' }}>Pick-up</option>
                                    <option value="take-out" {{ old('customer_type') == 'take-out' ? 'selected' : '' }}>Take out</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="customer_name" class="form-label fw-bold">Your Name</label>
                                <input type="text" class="form-control @error('customer_name') is-invalid @enderror" id="customer_name" name="customer_name" value="{{ old('customer_name', auth()->user()->name) }}" placeholder="Enter your name" required>
                                @error('customer_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="order_id" class="form-label fw-bold">Order Number <span class="text-muted small">(Optional)</span></label>
                                <input type="text" class="form-control @error('order_id') is-invalid @enderror" id="order_id" name="order_id" value="{{ old('order_id') }}" placeholder="Enter your order ID">
                                <div class="form-text">For verification purposes. Must be your order.</div>
                                @error('order_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="message" class="form-label fw-bold">Your Message</label>
                            <textarea class="form-control @error('message') is-invalid @enderror" id="message" name="message" rows="6" placeholder="Please type your feedback, complaint, or suggestion here..." required>{{ old('message') }}</textarea>
                            <div class="form-text">Minimum 10 characters required.</div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-paper-plane me-2"></i>Submit Feedback
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="text-center mt-3">
                <a href="{{ route('home') }}" class="text-decoration-none text-muted">
                    <i class="fas fa-arrow-left me-1"></i>Back to Home
                </a>
            </div>
        </div>
    </div>
</div>
@endsection