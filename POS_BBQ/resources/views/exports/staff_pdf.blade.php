<!DOCTYPE html>
<html>

<head>
    <title>Staff Performance Report</title>
    <style>
        body {
            font-family: sans-serif;
        }

        table {
            width: 100%;
            border-collapse: collapse;
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
            <h2 style="margin: 10px 0; font-size: 18px;">Staff Performance Report</h2>
            <p style="margin: 5px 0; font-size: 12px;">{{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} -
                {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}</p>
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

    <!-- Staff Performance Table -->
    <table>
        <thead>
            <tr>
                <th>Staff Member</th>
                <th class="text-right">Orders</th>
                <th class="text-right">Total Sales</th>
                <th class="text-right">Avg. Order Value</th>
            </tr>
        </thead>
        <tbody>
            @forelse($staffPerformance as $staff)
                <tr>
                    <td>{{ $staff->name }}</td>
                    <td class="text-right">{{ $staff->total_orders }}</td>
                    <td class="text-right">₱{{ number_format($staff->total_sales, 2) }}</td>
                    <td class="text-right">
                        ₱{{ $staff->total_orders > 0 ? number_format($staff->total_sales / $staff->total_orders, 2) : '0.00' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center;">No staff performance data found</td>
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