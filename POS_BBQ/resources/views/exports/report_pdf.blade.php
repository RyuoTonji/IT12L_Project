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
    <!-- Header -->
    <div style="position: relative; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px;">
        <div style="text-align: center;">
            <h1 style="margin: 0; font-size: 24px;">BBQ Lagao & Beef Pares</h1>
            @if($branch)
                <p style="margin: 5px 0; font-size: 14px;">{{ $branch->name }} - {{ $branch->address }}</p>
            @endif
            <h2 style="margin: 10px 0; font-size: 18px;">Shift Report</h2>
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

    <div class="header">
        <p><span class="label">Staff:</span> {{ $report->user->name }}</p>
        <p><span class="label">Date:</span> {{ $report->shift_date->format('Y-m-d') }}</p>
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

    <!-- Footer -->
    <div style="margin-top: 30px; text-align: center; padding-top: 20px; border-top: 2px solid #333;">
        <p style="font-weight: bold; font-size: 16px;">--DATA COMPLETE---</p>
        <p style="font-weight: bold; font-size: 16px;">***END OF THE FILE***</p>
    </div>
</body>

</html>