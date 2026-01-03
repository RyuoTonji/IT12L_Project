<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Void/Refund Requests Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #333;
        }

        .header p {
            margin: 5px 0;
            color: #666;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th {
            background-color: #f3f4f6;
            color: #374151;
            font-weight: bold;
            padding: 10px;
            text-align: left;
            border: 1px solid #d1d5db;
        }

        td {
            padding: 8px;
            border: 1px solid #d1d5db;
        }

        tr:nth-child(even) {
            background-color: #f9fafb;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }

        .status-badge {
            font-weight: bold;
        }

        .status-pending {
            color: #d97706;
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
            <h2 style="margin: 10px 0; font-size: 18px;">Void/Refund Requests Report</h2>
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
        <p style="margin: 3px 0;"><strong>Total Pending Requests:</strong> {{ $voidRequests->count() }}</p>
    </div>

    @if($voidRequests->isEmpty())
        <p style="text-align: center; color: #666; margin-top: 40px;">No pending void/refund requests.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Requester</th>
                    <th>Reason Tags</th>
                    <th>Additional Reason</th>
                    <th>Requested At</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($voidRequests as $request)
                    <tr>
                        <td>#{{ $request->order_id }}</td>
                        <td>{{ $request->requester->name }}</td>
                        <td>
                            @if($request->reason_tags)
                                {{ implode(', ', $request->reason_tags) }}
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $request->reason ?: '-' }}</td>
                        <td>{{ $request->created_at->format('M d, Y H:i') }}</td>
                        <td>
                            <span class="status-badge status-pending">
                                {{ ucfirst($request->status) }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- Footer -->
    <div style="margin-top: 30px; text-align: center; padding-top: 20px; border-top: 2px solid #333;">
        <p style="font-weight: bold; font-size: 16px;">--DATA COMPLETE---</p>
        <p style="font-weight: bold; font-size: 16px;">***END OF THE FILE***</p>
        <p style="font-size: 10px; margin-top: 5px;">&copy; {{ date('Y') }} POS BBQ System. All rights reserved.</p>
    </div>
</body>

</html>