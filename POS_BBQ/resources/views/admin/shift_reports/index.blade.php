@extends('layouts.admin')

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-semibold">Shift Reports</h1>
                <div class="flex items-center space-x-2">
                    <form action="{{ route('admin.shift-reports.index') }}" method="GET"
                        class="flex items-center space-x-2">
                        <label for="date" class="text-sm text-gray-600">Date:</label>
                        <input type="date" name="date" id="date" value="{{ request('date') }}"
                            class="border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                            onchange="this.form.submit()">

                        @if(request('date'))
                            <a href="{{ route('admin.shift-reports.index') }}"
                                class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md text-sm hover:bg-gray-300 transition flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Clear Filter
                            </a>
                        @endif
                    </form>
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

            @if($reports->isEmpty())
                <p class="text-gray-500">No shift reports submitted yet.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Staff
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Orders</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sales
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Refunds</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($reports as $report)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $report->shift_date->format('M d, Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $report->user->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $report->total_orders }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">₱{{ number_format($report->total_sales, 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">₱{{ number_format($report->total_refunds, 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="px-2 py-1 rounded text-xs font-semibold {{ $report->status == 'reviewed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            {{ ucfirst($report->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('admin.shift-reports.show', $report) }}"
                                            class="text-blue-600 hover:text-blue-900 flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $reports->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection