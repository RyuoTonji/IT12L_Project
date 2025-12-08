@extends('layouts.admin')

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-semibold">Staff Management</h1>
                <a href="{{ route('staff.create') }}"
                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                    Add Staff
                </a>
            </div>



            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th
                                class="py-2 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                ID</th>
                            <th
                                class="py-2 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Name</th>
                            <th
                                class="py-2 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Email</th>
                            <th
                                class="py-2 px-4 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Role</th>
                            <th
                                class="py-2 px-4 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status</th>
                            <th
                                class="py-2 px-4 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($staff as $user)
                            @php
                                $computedStatus = $user->getComputedStatus();
                                $statusLabel = $user->getStatusLabel();
                            @endphp
                            <tr>
                                <td class="py-2 px-4">{{ $user->id }}</td>
                                <td class="py-2 px-4">{{ $user->name }}</td>
                                <td class="py-2 px-4">{{ $user->email }}</td>
                                <td class="py-2 px-4 text-center">
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded
                                                                                                                    {{ $user->role == 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td class="py-2 px-4 text-center">
                                    @if($computedStatus === 'inactive')
                                        {{-- Display inactive as badge (cannot be changed via dropdown) --}}
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded bg-yellow-100 text-yellow-800 items-center"
                                            title="{{ $statusLabel }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            {{ $statusLabel }}
                                        </span>
                                    @else
                                        {{-- Inline Edit for Active/Disabled --}}
                                        <div class="inline-flex items-center gap-2">
                                            <span id="status-label-{{ $user->id }}" class="flex items-center">
                                                @if($user->status == 'active')
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-green-500"
                                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M5 13l4 4L19 7" />
                                                    </svg>
                                                    Active
                                                @else
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-red-500"
                                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                    Disabled
                                                @endif
                                            </span>
                                            <button onclick="showStatusEdit({{ $user->id }})"
                                                class="text-gray-500 hover:text-blue-600">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                        </div>
                                        <div id="status-edit-{{ $user->id }}" class="hidden inline-flex items-center gap-2">
                                            <select id="status-select-{{ $user->id }}"
                                                class="rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 text-sm">
                                                <option value="active" {{ $user->status == 'active' ? 'selected' : '' }}>Active
                                                </option>
                                                <option value="disabled" {{ $user->status == 'disabled' ? 'selected' : '' }}>Disabled
                                                </option>
                                            </select>
                                            <button onclick="saveStatus({{ $user->id }})"
                                                class="text-green-600 hover:text-green-800 flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M5 13l4 4L19 7" />
                                                </svg>
                                                <span>Save</span>
                                            </button>
                                            <button onclick="cancelStatusEdit({{ $user->id }})"
                                                class="text-red-600 hover:text-red-800 flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                                <span>Cancel</span>
                                            </button>
                                        </div>
                                    @endif
                                </td>
                                <td class="py-2 px-4 text-center">
                                    <a href="{{ route('staff.show', $user) }}"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 text-sm text-blue-600 hover:bg-blue-100 rounded mr-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <span>View</span>
                                    </a>

                                    @if($user->id !== auth()->id())
                                        <form id="deleteStaffForm{{ $user->id }}" action="{{ route('staff.destroy', $user) }}"
                                            method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button"
                                                class="inline-flex items-center gap-1 px-3 py-1.5 text-sm text-red-600 hover:bg-red-100 rounded"
                                                onclick="confirmArchiveStaff({{ $user->id }})">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                                                </svg>
                                                <span>Archive</span>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-4 px-4 text-center text-gray-500">No staff members found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function showStatusEdit(userId) {
            document.getElementById(`status-label-${userId}`).parentElement.classList.add('hidden');
            document.getElementById(`status-edit-${userId}`).classList.remove('hidden');
        }

        function cancelStatusEdit(userId) {
            document.getElementById(`status-label-${userId}`).parentElement.classList.remove('hidden');
            document.getElementById(`status-edit-${userId}`).classList.add('hidden');
        }

        function saveStatus(userId) {
            const status = document.getElementById(`status-select-${userId}`).value;

            fetch(`/admin/staff/${userId}/update-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ status: status })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update label with icon
                        const iconHtml = status == 'active'
                            ? '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>Active'
                            : '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>Disabled';

                        document.getElementById(`status-label-${userId}`).innerHTML = iconHtml;
                        cancelStatusEdit(userId);
                    }
                })
                .catch(error => {
                    console.error('Error updating status:', error);
                    alert('Failed to update status');
                });
        }

        function confirmArchiveStaff(userId) {
            AlertModal.showConfirm(
                'Are you sure you want to archive this staff member?',
                function () {
                    document.getElementById('deleteStaffForm' + userId).submit();
                },
                null,
                'Archive Confirmation'
            );
        }
    </script>
@endsection