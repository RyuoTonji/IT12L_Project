@extends('layouts.admin')

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-semibold flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 mr-3 text-gray-800" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Daily Consolidated Report
                </h1>
                <div class="flex items-center space-x-2">
                    <form action="{{ route('admin.reports.daily') }}" method="GET" class="flex items-center space-x-2">
                        <label for="date" class="text-sm text-gray-600">Date:</label>
                        <input type="date" name="date" id="date" value="{{ $date }}"
                            class="px-3 py-2 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                            onchange="this.form.submit()">
                    </form>
                    <button onclick="showExportConfirmation()"
                        class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 flex items-center inline-flex">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2zm2-10l4 4m-4 0l4-4m-4 4V7" />
                        </svg>
                        Export PDF
                    </button>
                    <a href="{{ route('admin.reports') }}"
                        class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 flex items-center inline-flex">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to Reports
                    </a>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                <div class="bg-green-100 p-4 rounded-lg shadow">
                    <h2 class="text-lg font-bold text-green-800">Total Sales</h2>
                    <p class="text-2xl font-bold text-green-800 text-right mt-2">₱{{ number_format($totalSales, 2) }}</p>
                </div>
                <div class="bg-red-100 p-4 rounded-lg shadow">
                    <h2 class="text-lg font-bold text-red-800">Total Refunds (Est.)</h2>
                    <p class="text-2xl font-bold text-red-800 text-right mt-2">₱{{ number_format(abs($totalRefunds), 2) }}
                    </p>
                </div>
                <div class="bg-blue-100 p-4 rounded-lg shadow">
                    <h2 class="text-lg font-bold text-blue-800">Shift Reports</h2>
                    <p class="text-2xl font-bold text-blue-800 text-right mt-2">{{ $shiftReports->count() }}</p>
                </div>
            </div>

            <!-- Shift Reports Section -->
            <div class="mb-8">
                <h2 class="text-xl font-medium mb-4 border-b pb-2">Staff Shift Reports</h2>
                @if($shiftReports->isEmpty())
                    <p class="text-gray-500 italic">No shift reports submitted for this date.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 bg-white border rounded-lg">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Staff</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Role</th>
                                    <th
                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Orders</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Sales</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Refunds</th>
                                    <th
                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($shiftReports as $report)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $report->user->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ ucfirst($report->user->role) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-700">
                                            {{ $report->total_orders }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium text-gray-900">
                                            ₱{{ number_format($report->total_sales, 2) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-700">
                                            ₱{{ number_format($report->total_refunds, 2) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <span
                                                class="text-xs font-semibold {{ $report->status == 'reviewed' ? 'text-green-600' : 'text-yellow-600' }}">
                                                {{ ucfirst($report->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                            <a href="{{ route('admin.shift-reports.show', $report) }}"
                                                class="text-blue-600 hover:text-blue-900 font-medium flex items-center justify-end">
                                                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                                    <polyline points="14 2 14 8 20 8"></polyline>
                                                    <circle cx="11.5" cy="15.5" r="2.5"></circle>
                                                    <path d="M16 20l-2-2"></path>
                                                </svg>
                                                View & Reply
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <!-- Void Requests Section -->
            <div class="mb-8">
                <h2 class="text-xl font-medium mb-4 border-b pb-2">Void Requests</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Time</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Order #</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Requester</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Reason</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Approver</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($voidRequests as $void)
                                <tr>
                                    <td class="px-4 py-2 text-sm">{{ $void->created_at->format('H:i') }}</td>
                                    <td class="px-4 py-2 text-sm">{{ $void->order_id }}</td>
                                    <td class="px-4 py-2 text-sm">{{ $void->requester->name }}</td>
                                    <td class="px-4 py-2 text-sm">
                                        @if($void->reason_tags)
                                            @foreach($void->reason_tags as $tag)
                                                <span
                                                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 mr-1">
                                                    {{ $tag }}
                                                </span>
                                            @endforeach
                                        @endif
                                        <span class="text-gray-600">{{ $void->reason }}</span>
                                    </td>
                                    <td class="px-4 py-2 text-sm">
                                        <span
                                            class="inline-flex text-xs leading-5 font-semibold items-center {{ $void->status == 'approved' ? 'text-green-600' : ($void->status == 'rejected' ? 'text-red-600' : 'text-yellow-600') }}">
                                            {{ ucfirst($void->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2 text-sm">{{ $void->approver->name ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-2 text-sm text-center text-gray-500">No void requests for
                                        this date.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Inventory Activities Section -->
            <div class="mb-8">
                <h2 class="text-xl font-medium mb-4 border-b pb-2">Inventory Activities</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Time</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Details</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($inventoryActivities as $activity)
                                <tr>
                                    <td class="px-4 py-2 text-sm">{{ $activity->created_at->format('H:i') }}</td>
                                    <td class="px-4 py-2 text-sm">{{ $activity->user->name }}</td>
                                    <td class="px-4 py-2 text-sm">{{ $activity->action }}</td>
                                    <td class="px-4 py-2 text-sm">{{ $activity->details }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-2 text-sm text-center text-gray-500">No inventory activity
                                        for this date.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
@endsection

<script>
    function showExportConfirmation() {
        const date = '{{ $date }}';
        AlertModal.showConfirm(
            'Are you sure you want to export this daily report as PDF?',
            function () {
                window.location.href = `/export/daily?date=${date}`;
            },
            null,
            'Export Confirmation'
        );
    }
</script>