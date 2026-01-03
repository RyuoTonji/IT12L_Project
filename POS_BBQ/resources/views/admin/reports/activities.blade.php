@extends('layouts.admin')

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
        <div class="p-6">
            <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                <h1 class="text-2xl font-semibold flex items-center">
                    <svg class="w-8 h-8 mr-2 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Recent Activities
                </h1>
                <div class="flex flex-col md:flex-row items-center gap-4 w-full md:w-auto">
                    <form action="{{ route('admin.reports.activities') }}" method="GET"
                        class="flex flex-col md:flex-row items-center gap-2 w-full md:w-auto">
                        <input type="hidden" name="role" value="{{ $activeTab }}">

                        <!-- Search Input -->
                        <div class="relative rounded-md shadow-sm w-full md:w-48">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..."
                                class="w-full border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 text-sm pl-8">
                            <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                        </div>

                        <!-- Date Filter -->
                        <div class="w-full md:w-auto">
                            <input type="date" name="date" value="{{ request('date') }}"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                                onchange="this.form.submit()">
                        </div>

                        <!-- User Filter -->
                        <div class="w-full md:w-auto">
                            <select name="user_id" onchange="this.form.submit()"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                <option value="">All Users</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                    <a href="{{ route('admin.reports') }}"
                        class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700 flex items-center inline-flex whitespace-nowrap">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to Reports
                    </a>
                </div>
            </div>

            <!-- Tabs Navigation -->
            <div class="border-b border-gray-200 mb-6">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    @foreach(['cashier' => 'Cashier', 'inventory' => 'Inventory', 'manager' => 'Manager'] as $key => $label)
                        <a href="{{ route('admin.reports.activities', ['role' => $key]) }}"
                            class="{{ $activeTab === $key ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} 
                                whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200">
                            {{ $label }}
                        </a>
                    @endforeach
                </nav>
            </div>

            @if($activities->isEmpty())
                <p class="text-gray-500 text-center py-8">No recent activities found.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Action</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Details</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($activities as $activity)
                                <tr class="hover:bg-gray-50 cursor-pointer transition-colors"
                                    onclick="openActivityModal('{{ $activity->created_at->format('Y-m-d H:i') }}', '{{ $activity->user->name ?? 'System' }}', '{{ addslashes($activity->action) }}', '{{ addslashes($activity->details) }}', '{{ $activity->status }}')">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $activity->created_at->format('Y-m-d H:i') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $activity->user->name ?? 'System' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $activity->action }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        <div class="max-w-xs truncate">{{ $activity->details }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @php
                                            $statusClasses = match ($activity->status) {
                                                'success' => 'text-green-600',
                                                'warning' => 'text-yellow-600',
                                                'error', 'danger' => 'text-red-600',
                                                'info' => 'text-blue-600',
                                                default => 'text-gray-600',
                                            };
                                        @endphp
                                        <span class="inline-flex text-xs font-semibold leading-5 {{ $statusClasses }}">
                                            {{ ucfirst($activity->status ?? 'N/A') }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $activities->links('vendor.pagination.custom') }}
                </div>
            @endif
        </div>
    </div>

    <!-- Details Modal -->
    <div id="activityModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-lg shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Activity Details</h3>
                    <button onclick="closeActivityModal()" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                    </button>
                </div>
                <div class="space-y-4">
                    <div>
                        <span class="block text-sm font-medium text-gray-700">Date & Time</span>
                        <p class="mt-1 text-sm text-gray-900" id="modal-date"></p>
                    </div>
                    <div>
                        <span class="block text-sm font-medium text-gray-700">User</span>
                        <p class="mt-1 text-sm text-gray-900" id="modal-user"></p>
                    </div>
                    <div>
                        <span class="block text-sm font-medium text-gray-700">Action</span>
                        <p class="mt-1 text-sm text-gray-900" id="modal-action"></p>
                    </div>
                    <div>
                        <span class="block text-sm font-medium text-gray-700">Status</span>
                        <p class="mt-1 text-sm text-gray-900" id="modal-status"></p>
                    </div>
                    <div>
                        <span class="block text-sm font-medium text-gray-700">Details</span>
                        <div class="mt-1 p-3 bg-gray-50 rounded text-sm text-gray-900 break-words" id="modal-details"></div>
                    </div>
                </div>
                <div class="mt-6 text-right">
                    <button onclick="closeActivityModal()"
                        class="px-4 py-2 bg-gray-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-300">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openActivityModal(date, user, action, details, status) {
            document.getElementById('modal-date').textContent = date;
            document.getElementById('modal-user').textContent = user;
            document.getElementById('modal-action').textContent = action;
            document.getElementById('modal-details').textContent = details; // Ensure this is safe text
            document.getElementById('modal-status').textContent = status.charAt(0).toUpperCase() + status.slice(1);
            document.getElementById('activityModal').classList.remove('hidden');
        }

        function closeActivityModal() {
            document.getElementById('activityModal').classList.add('hidden');
        }

        // Close modal when clicking outside
        window.onclick = function (event) {
            const modal = document.getElementById('activityModal');
            if (event.target == modal) {
                closeActivityModal();
            }
        }
    </script>
    </div>
    </div>
@endsection