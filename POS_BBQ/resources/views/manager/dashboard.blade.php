@extends('layouts.manager')

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
        <div class="p-6 text-gray-900">
            <h3 class="text-lg font-medium mb-4">Overview</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-blue-100 p-4 rounded-lg">
                    <h4 class="font-bold text-blue-800">Recent Activities</h4>
                    <p class="text-2xl">{{ $recentActivities->count() }}</p>
                </div>
                <div class="bg-green-100 p-4 rounded-lg">
                    <h4 class="font-bold text-green-800">Day Sales</h4>
                    <p class="text-2xl">₱{{ number_format($todaySales, 2) }}</p>
                </div>
                <div class="bg-yellow-100 p-4 rounded-lg">
                    <h4 class="font-bold text-yellow-800">Today's Orders</h4>
                    <p class="text-2xl">{{ $todayOrders }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium">Recent Activity Logs</h3>
                <a href="{{ route('manager.reports') }}" class="text-blue-600 hover:text-blue-900 flex items-center inline-flex">
                    View All
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Action</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Details</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($recentActivities as $activity)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $activity->created_at->format('Y-m-d H:i') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $activity->user->name ?? 'System' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $activity->action }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $activity->details }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection