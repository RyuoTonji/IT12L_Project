<!DOCTYPE html>
<html>

<head>
    <title>Daily Consolidated Report</title>
    <style>
        body {
            font-family: sans-serif;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        h3 {
            margin-top: 25px;
            margin-bottom: 10px;
            font-size: 16px;
        }

        .summary-box {
            display: inline-block;
            padding: 10px 15px;
            margin: 5px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <div style="position: relative; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px;">
        <div style="text-align: center;">
            <h1 style="margin: 0; font-size: 24px;">BBQ Lagao & Beef Pares</h1>
            @if($branch)
                <p style="margin: 5px 0; font-size: 14px;">{{ $branch->name }} - {{ $branch->address }}</p>
            @endif
            <h2 style="margin: 10px 0; font-size: 18px;">Daily Consolidated Report</h2>
            <p style="margin: 5px 0; font-size: 12px;">{{ \Carbon\Carbon::parse($date)->format('F d, Y') }}</p>
        </div>
        @php
            $logoPath = public_path('logo_black.png');
            if (file_exists($logoPath)) {
                $logoData = base64_encode(file_get_contents($logoPath));
                $logoSrc = 'data:image/png;base64,' . $logoData;
            } else {
                $logoSrc = '';
            }
        @endphp
        @if($logoSrc)
            <img src="{{ $logoSrc }}" style="position: absolute; right: 0; top: -20px; height: 80px; width: auto;"
                alt="Company Logo">
        @endif
    </div>

    <!-- Export Metadata -->
    <div style="margin-bottom: 15px; font-size: 12px;">
        <p style="margin: 3px 0;"><strong>Exported by:</strong> {{ $exporter->name }}</p>
        <p style="margin: 3px 0;"><strong>Export Date:</strong> {{ $exportDate }}</p>
        <p style="margin: 3px 0;"><strong>Export Time:</strong> {{ $exportTime }}</p>
    </div>

    <!-- Summary -->
    <div style="margin-bottom: 20px; text-align: center;">
        <div class="summary-box">
            <strong>Total Sales:</strong> ₱{{ number_format($totalSales, 2) }}
        </div>
        <div class="summary-box">
            <strong>Total Refunds:</strong> ₱{{ number_format(abs($totalRefunds), 2) }}
        </div>
        <div class="summary-box">
            <strong>Shift Reports:</strong> {{ $shiftReports->count() }}
        </div>
    </div>

    <!-- Shift Reports -->
    <h3>Staff Shift Reports</h3>
    @forelse($shiftReports as $report)
        <div style="border: 1px solid #ddd; padding: 10px; margin-bottom: 10px; background-color: #f9f9f9;">
            <p style="margin: 3px 0;"><strong>{{ $report->user->name }}</strong> ({{ ucfirst($report->user->role) }}) -
                {{ ucfirst($report->status) }}</p>
            <p style="margin: 3px 0; font-size: 12px;">
                Orders: {{ $report->total_orders }} |
                Sales: ₱{{ number_format($report->total_sales, 2) }} |
                Refunds: ₱{{ number_format($report->total_refunds, 2) }}
            </p>
            <p style="margin: 5px 0; font-size: 12px;">{{ $report->content }}</p>
        </div>
    @empty
        <p style="font-size: 12px; color: #666;">No shift reports submitted for this date.</p>
    @endforelse

    <!-- Void Requests -->
    <h3>Void Requests</h3>
    <table>
        <thead>
            <tr>
                <th>Time</th>
                <th>Order #</th>
                <th>Requester</th>
                <th>Reason</th>
                <th>Status</th>
                <th>Approver</th>
            </tr>
        </thead>
        <tbody>
            @forelse($voidRequests as $void)
                <tr>
                    <td>{{ $void->created_at->format('H:i') }}</td>
                    <td>{{ $void->order_id }}</td>
                    <td>{{ $void->requester->name }}</td>
                    <td>{{ $void->reason }}</td>
                    <td>{{ ucfirst($void->status) }}</td>
                    <td>{{ $void->approver->name ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">No void requests for this date.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Inventory Activities -->
    <h3>Inventory Activities</h3>
    <table>
        <thead>
            <tr>
                <th>Time</th>
                <th>User</th>
                <th>Action</th>
                <th>Details</th>
            </tr>
        </thead>
        <tbody>
            @forelse($inventoryActivities as $activity)
                <tr>
                    <td>{{ $activity->created_at->format('H:i') }}</td>
                    <td>{{ $activity->user->name }}</td>
                    <td>{{ $activity->action }}</td>
                    <td>{{ $activity->details }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">No inventory activity for this date.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Footer -->
    <div style="margin-top: 30px; text-align: center; padding-top: 20px; border-top: 2px solid #333;">
        <p style="font-weight: bold; font-size: 16px;">--DATA COMPLETE---</p>
        <p style="font-weight: bold; font-size: 16px;">***END OF THE FILE***</p>
    </div>
</body>

</html>