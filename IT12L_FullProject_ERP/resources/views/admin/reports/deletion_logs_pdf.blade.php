<!DOCTYPE html>
<html>

<head>
    <title>Deletion Logs</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .company-name {
            font-size: 18px;
            font-weight: bold;
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

        .footer {
            margin-top: 30px;
            font-size: 12px;
        }

        .end-note {
            text-align: center;
            font-weight: bold;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="company-name">{{ $companyInfo['name'] }}</div>
        <div>{{ $companyInfo['address'] }}</div>
        <h3>Deletion Logs Report</h3>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date & Time</th>
                <th>Table</th>
                <th>Record ID</th>
                <th>Deleted By</th>
                <th>Reason</th>
                <th>Data Snapshot (Preview)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($logs as $log)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($log->created_at)->format('M d, Y h:i A') }}</td>
                    <td>{{ $log->table_name }}</td>
                    <td>{{ $log->record_id }}</td>
                    <td>{{ $log->deleted_by_name ?? 'System/Unknown' }}</td>
                    <td>{{ $log->reason }}</td>
                    <td>
                        {{ Str::limit($log->data, 50) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p><strong>Extracted by:</strong> {{ $exportedBy }}</p>
        <p><strong>Date Extracted:</strong> {{ \Carbon\Carbon::parse($exportDate)->format('M d, Y h:i A') }}</p>
    </div>

    <div class="end-note">
        <p>---DATA COMPLETE---</p>
        <p>***END OF THE FILE***</p>
    </div>
</body>

</html>