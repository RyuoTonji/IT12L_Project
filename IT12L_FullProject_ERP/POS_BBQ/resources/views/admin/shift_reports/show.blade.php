@extends('layouts.admin')

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-semibold">Shift Report Details</h1>
                <div>
                    <a href="{{ route('export.report', $shiftReport) }}"
                        class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 mr-2">Export PDF</a>
                    <a href="{{ route('admin.shift-reports.index') }}"
                        class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">Back to List</a>
                </div>
            </div>

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <h2 class="text-lg font-medium mb-4">Report Information</h2>
                    <div class="bg-white p-4 rounded-lg shadow border">
                        <div class="mb-3">
                            <p class="text-sm text-gray-600">Staff Member</p>
                            <p class="font-medium">{{ $shiftReport->user->name }}</p>
                        </div>
                        <div class="mb-3">
                            <p class="text-sm text-gray-600">Role</p>
                            <p class="font-medium">{{ ucfirst($shiftReport->user->role) }}</p>
                        </div>
                        <div class="mb-3">
                            <p class="text-sm text-gray-600">Shift Date</p>
                            <p class="font-medium">{{ $shiftReport->shift_date->format('M d, Y') }}</p>
                        </div>
                        <div class="mb-3">
                            <p class="text-sm text-gray-600">Report Type</p>
                            <p class="font-medium">{{ ucfirst($shiftReport->report_type) }}</p>
                        </div>
                        <div class="mb-3">
                            <p class="text-sm text-gray-600">Status</p>
                            <span
                                class="px-2 py-1 rounded text-xs font-semibold {{ $shiftReport->status == 'reviewed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ ucfirst($shiftReport->status) }}
                            </span>
                        </div>
                    </div>
                </div>

                <div>
                    <h2 class="text-lg font-medium mb-4">Shift Statistics</h2>
                    <div class="bg-white p-4 rounded-lg shadow border space-y-3">
                        @if($shiftReport->report_type === 'sales')
                            <div class="flex justify-between">
                                <span class="text-gray-600">Total Orders:</span>
                                <span class="font-bold">{{ $shiftReport->total_orders }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Total Sales:</span>
                                <span class="font-bold text-green-600">₱{{ number_format($shiftReport->total_sales, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Total Refunds:</span>
                                <span class="font-bold text-red-600">₱{{ number_format($shiftReport->total_refunds, 2) }}</span>
                            </div>
                        @else
                            <div class="flex justify-between">
                                <span class="text-gray-600">Stock In:</span>
                                <span class="font-bold text-green-600">{{ number_format($shiftReport->stock_in, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Stock Out:</span>
                                <span class="font-bold text-orange-600">{{ number_format($shiftReport->stock_out, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Remaining Stock:</span>
                                <span
                                    class="font-bold text-blue-600">{{ number_format($shiftReport->remaining_stock, 2) }}</span>
                            </div>
                            @if($shiftReport->spoilage)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Spoilage:</span>
                                    <span class="font-bold text-red-600">{{ number_format($shiftReport->spoilage, 2) }}</span>
                                </div>
                            @endif
                            @if($shiftReport->returns)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Returns:</span>
                                    <span class="font-bold text-yellow-600">{{ number_format($shiftReport->returns, 2) }}</span>
                                </div>
                                @if($shiftReport->return_reason)
                                    <div class="pt-2 border-t">
                                        <p class="text-sm text-gray-600 mb-1">Return Reason:</p>
                                        <p class="text-sm">{{ $shiftReport->return_reason }}</p>
                                    </div>
                                @endif
                            @endif
                        @endif
                    </div>
                </div>
            </div>

            <div class="mb-6">
                <h2 class="text-lg font-medium mb-4">Report Content</h2>
                <div class="bg-gray-50 p-4 rounded-lg border">
                    <p class="whitespace-pre-wrap">{{ $shiftReport->content }}</p>
                </div>
            </div>

            @if($shiftReport->admin_reply)
                <div class="mb-6">
                    <h2 class="text-lg font-medium mb-4">Admin Reply</h2>
                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                        <p class="whitespace-pre-wrap">{{ $shiftReport->admin_reply }}</p>
                    </div>
                </div>
            @endif

            @if($shiftReport->status == 'submitted')
                <div>
                    <h2 class="text-lg font-medium mb-4">Send Reply</h2>
                    <form action="{{ route('admin.shift-reports.reply', $shiftReport) }}" method="POST">
                        @csrf
                        <textarea name="admin_reply" rows="4" class="w-full border rounded p-2 mb-4"
                            placeholder="Write your reply to the staff member..." required></textarea>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Send
                            Reply</button>
                    </form>
                </div>
            @endif
        </div>
    </div>
@endsection