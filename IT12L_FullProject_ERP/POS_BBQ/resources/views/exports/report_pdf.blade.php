<!DOCTYPE html>
<html>

<head>
    <title>Shift Report</title>
    <style>
        body {
            font-family: sans-serif;
        }

        .header {
            margin-bottom: 20px;
        }

        .stats {
            margin-bottom: 20px;
        }

        .content {
            margin-top: 20px;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }

        .label {
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Shift Report</h1>
        <p><span class="label">Staff:</span> {{ $report->user->name }}</p>
        <p><span class="label">Date:</span> {{ $report->shift_date->format('Y-m-d') }}</p>
        <p><span class="label">Generated:</span> {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>

    <div class="stats">
        <h3>Statistics</h3>
        @if($report->report_type === 'sales')
            <p><span class="label">Total Orders:</span> {{ $report->total_orders }}</p>
            <p><span class="label">Total Sales:</span> {{ number_format($report->total_sales, 2) }}</p>
            <p><span class="label">Total Refunds:</span> {{ number_format($report->total_refunds, 2) }}</p>
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

    <div class="content">
        <h3>Report Content</h3>
        <p>{{ $report->content }}</p>
    </div>

    @if($report->admin_reply)
        <div class="content">
            <h3>Admin Reply</h3>
            <p>{{ $report->admin_reply }}</p>
        </div>
    @endif
</body>

</html>