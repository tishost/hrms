<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Tenants Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
        }
        .header h1 {
            color: #667eea;
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #667eea;
            color: white;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .status-active {
            color: #28a745;
            font-weight: bold;
        }
        .status-inactive {
            color: #ffc107;
            font-weight: bold;
        }
        .status-checkout {
            color: #dc3545;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Tenants Report</h1>
        <p>Generated on: {{ $export_date }}</p>
        <p>Total Tenants: {{ $tenants->count() }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Mobile No</th>
                <th>Owner Name</th>
                <th>District</th>
                <th>Status</th>
                <th>Email</th>
                <th>NID Number</th>
                <th>Address</th>
                <th>Check In Date</th>
                <th>Security Deposit</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tenants as $tenant)
            <tr>
                <td>{{ $tenant->full_name }}</td>
                <td>{{ $tenant->mobile }}</td>
                <td>{{ $tenant->owner->name ?? 'N/A' }}</td>
                <td>{{ $tenant->district ?? 'N/A' }}</td>
                <td class="status-{{ $tenant->status }}">
                    {{ ucfirst($tenant->status) }}
                </td>
                <td>{{ $tenant->email ?? 'N/A' }}</td>
                <td>{{ $tenant->nid_number ?? 'N/A' }}</td>
                <td>{{ Str::limit($tenant->address ?? 'N/A', 50) }}</td>
                <td>{{ $tenant->check_in_date ? $tenant->check_in_date->format('Y-m-d') : 'N/A' }}</td>
                <td>{{ $tenant->security_deposit ? '৳' . number_format($tenant->security_deposit) : 'N/A' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>This report was generated automatically by the HRMS System</p>
        <p>© {{ date('Y') }} HRMS. All rights reserved.</p>
    </div>
</body>
</html>
