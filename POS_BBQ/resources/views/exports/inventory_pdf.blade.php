<!DOCTYPE html>
<html>

<head>
    <title>Inventory Report</title>
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

        .status-text {
            font-weight: bold;
        }

        .color-success {
            color: #16a34a;
        }

        .color-danger {
            color: #dc2626;
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
            <h2 style="margin: 10px 0; font-size: 18px;">Inventory Report</h2>
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

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Category</th>
                <th>Supplier</th>
                <th>Quantity</th>
                <th>Unit</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($inventoryItems as $item)
                <tr>
                    <td>{{ $item->id }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->category ?? '-' }}</td>
                    <td>{{ $item->supplier ?? '-' }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ $item->unit }}</td>
                    <td>
                        <span class="status-text {{ $item->quantity <= $item->reorder_level ? 'color-danger' : 'color-success' }}">
                            {{ $item->quantity <= $item->reorder_level ? 'Low Stock' : 'In Stock' }}
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Footer -->
    <div style="margin-top: 30px; text-align: center; padding-top: 20px; border-top: 2px solid #333;">
        <p style="font-weight: bold; font-size: 16px;">--DATA COMPLETE---</p>
        <p style="font-weight: bold; font-size: 16px;">***END OF THE FILE***</p>
    </div>
</body>

</html>