<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>My Profile Data</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
        }

        .header p {
            margin: 5px 0;
            color: #666;
        }

        .section {
            margin-bottom: 20px;
        }

        .section h2 {
            background-color: #f0f0f0;
            padding: 8px;
            margin: 0 0 10px 0;
            font-size: 14px;
            border-left: 4px solid #3b82f6;
        }

        .info-row {
            margin-bottom: 5px;
        }

        .info-row strong {
            display: inline-block;
            width: 120px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 6px 8px;
            text-align: left;
        }

        th {
            background-color: #f5f5f5;
            font-weight: bold;
        }

        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            font-size: 10px;
            color: #666;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>My Profile Data Export</h1>
        <p>Exported on {{ $exportDate }} at {{ $exportTime }}</p>
    </div>

    <div class="section">
        <h2>Profile Information</h2>
        <div class="info-row"><strong>Name:</strong> {{ $user->name }}</div>
        <div class="info-row"><strong>Email:</strong> {{ $user->email }}</div>
        <div class="info-row"><strong>Role:</strong> {{ ucfirst($user->role) }}</div>
        <div class="info-row"><strong>Account Created:</strong> {{ $user->created_at->format('F d, Y') }}</div>
        @if($user->branch)
            <div class="info-row"><strong>Branch:</strong> {{ $user->branch->name }}</div>
        @endif
    </div>

    @if($orders->count() > 0)
        <div class="section">
            <h2>Transaction History ({{ $orders->count() }} orders)</h2>
            <table>
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders->take(50) as $order)
                        <tr>
                            <td>#{{ $order->id }}</td>
                            <td>{{ $order->created_at->format('M d, Y H:i') }}</td>
                            <td>{{ ucfirst($order->status) }}</td>
                            <td>₱{{ number_format($order->total_amount, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @if($orders->count() > 50)
                <p style="font-style: italic; color: #666;">Showing first 50 of {{ $orders->count() }} orders.</p>
            @endif
        </div>
    @endif

    @if($shiftReports->count() > 0)
        <div class="section">
            <h2>Shift Reports ({{ $shiftReports->count() }} reports)</h2>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Orders</th>
                        <th>Sales</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($shiftReports->take(25) as $report)
                        <tr>
                            <td>{{ $report->shift_date->format('M d, Y') }}</td>
                            <td>{{ $report->total_orders }}</td>
                            <td>₱{{ number_format($report->total_sales, 2) }}</td>
                            <td>{{ ucfirst($report->status) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    @if($activities->count() > 0)
        <div class="section">
            <h2>Recent Activity Log</h2>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Action</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($activities->take(25) as $activity)
                        <tr>
                            <td>{{ $activity->created_at->format('M d, Y H:i') }}</td>
                            <td>{{ $activity->action }}</td>
                            <td>{{ Str::limit($activity->details, 50) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <div class="footer">
        <p>This document contains personal data from your account at POS BBQ System.</p>
        <p>Generated automatically upon user request.</p>
    </div>
</body>

</html>