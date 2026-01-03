<!DOCTYPE html>
<html>

<head>
    <title>Shift Reports - All</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        .page-break {
            page-break-after: always;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .label {
            font-weight: bold;
        }

        .report-item {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #333;
        }

        .filter-summary {
            background-color: #f0f0f0;
            padding: 10px;
            margin-bottom: 15px;
            border-left: 4px solid #007bff;
        }

        .status-text {
            font-weight: bold;
        }

        .color-warning {
            color: #d97706;
        }

        .color-success {
            color: #16a34a;
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
            <h2 style="margin: 10px 0; font-size: 18px;">All Shift Reports</h2>
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
        <p style="margin: 3px 0;"><strong>Total Reports:</strong> {{ $reports->count() }}</p>
    </div>

    <!-- Filter Summary -->
    @if($filterSummary)
        <div class="filter-summary">
            <strong>Applied Filters:</strong> {{ $filterSummary }}
        </div>
    @endif

    <!-- Summary Table -->
    <h3>Summary</h3>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Staff</th>
                <th>Role</th>
                @if($activeTab === 'inventory')
                    <th>Stock In</th>
                    <th>Stock Out</th>
                    <th>Remaining</th>
                @else
                    <th>Orders</th>
                    <th>Sales</th>
                    <th>Refunds</th>
                @endif
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reports as $report)
                <tr>
                    <td>{{ $report->shift_date->format('M d, Y') }}</td>
                    <td>{{ $report->user->name }}</td>
                    <td>{{ ucfirst($report->user->role) }}</td>
                    @if($activeTab === 'inventory')
                        <td>{{ number_format($report->stock_in, 2) }}</td>
                        <td>{{ number_format($report->stock_out, 2) }}</td>
                        <td>{{ number_format($report->remaining_stock, 2) }}</td>
                    @else
                        <td>{{ $report->total_orders }}</td>
                        <td>₱{{ number_format($report->total_sales, 2) }}</td>
                        <td>₱{{ number_format($report->total_refunds, 2) }}</td>
                    @endif
                    <td>
                        <span class="status-text {{ $report->status === 'reviewed' ? 'color-success' : 'color-warning' }}">
                            {{ ucfirst($report->status) }}
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Detailed Reports -->
    <div style="page-break-before: always;"></div>
    <h3>Detailed Reports</h3>

    @foreach($reports as $index => $report)
        <div class="report-item">
            <h4>Report #{{ $index + 1 }}</h4>
            <p><span class="label">Staff:</span> {{ $report->user->name }}</p>
            <p><span class="label">Date:</span> {{ $report->shift_date->format('Y-m-d') }}</p>

            <div style="margin-top: 10px;">
                <strong>Statistics:</strong>
                @if($report->report_type === 'sales')
                    <p><span class="label">Total Orders:</span> {{ $report->total_orders }}</p>
                    <p><span class="label">Total Sales:</span> ₱{{ number_format($report->total_sales, 2) }}</p>
                    <p><span class="label">Total Refunds:</span> ₱{{ number_format($report->total_refunds, 2) }}</p>
                @else
                    <p><span class="label">Stock In:</span> {{ number_format($report->stock_in, 2) }}</p>
                    <p><span class="label">Stock Out:</span> {{ number_format($report->stock_out, 2) }}</p>
                    <p><span class="label">Remaining Stock:</span> {{ number_format($report->remaining_stock, 2) }}</p>
                    @if($report->spoilage)
                        <p><span class="label">Spoilage:</span> {{ number_format($report->spoilage, 2) }}</p>
                    @endif
                    @if($report->returns)
                        <p><span class="label">Returns:</span> {{ number_format($report->returns, 2) }}</p>
                        @if($report->return_reason)
                            <p><span class="label">Return Reason:</span> {{ $report->return_reason }}</p>
                        @endif
                    @endif
                @endif
            </div>

            <div style="margin-top: 10px;">
                <strong>Report Content:</strong>
                <p style="white-space: pre-wrap;">{{ $report->content }}</p>
            </div>

            @if($report->admin_reply)
                <div style="margin-top: 10px;">
                    <strong>Admin Reply:</strong>
                    <p style="white-space: pre-wrap;">{{ $report->admin_reply }}</p>
                </div>
            @endif
        </div>

        @if(!$loop->last && ($index + 1) % 3 == 0)
            <div style="page-break-after: always;"></div>
        @endif
    @endforeach

    <!-- Footer -->
    <div style="margin-top: 30px; text-align: center; padding-top: 20px; border-top: 2px solid #333;">
        <p style="font-weight: bold; font-size: 16px;">--DATA COMPLETE---</p>
        <p style="font-weight: bold; font-size: 16px;">***END OF THE FILE***</p>
    </div>
</body>

</html>