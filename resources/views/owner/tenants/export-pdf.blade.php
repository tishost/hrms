<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tenants Report</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
        }
        .header p {
            margin: 5px 0 0 0;
            font-size: 14px;
            opacity: 0.9;
        }
        .summary {
            background: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .summary h2 {
            color: #2c3e50;
            margin-bottom: 15px;
            font-size: 18px;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        .summary-item {
            text-align: center;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #667eea;
        }
        .summary-item h3 {
            margin: 0 0 5px 0;
            color: #667eea;
            font-size: 24px;
        }
        .summary-item p {
            margin: 0;
            color: #6c757d;
            font-size: 14px;
        }
        .table-container {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }
        thead {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
        }
        th {
            padding: 12px 8px;
            text-align: left;
            font-weight: bold;
            border-bottom: 2px solid #ddd;
        }
        td {
            padding: 10px 8px;
            border-bottom: 1px solid #eee;
        }
        tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        tbody tr:hover {
            background-color: #e9ecef;
        }
        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-active {
            background-color: #28a745;
            color: white;
        }
        .status-inactive {
            background-color: #dc3545;
            color: white;
        }
        .tenant-name {
            font-weight: bold;
            color: #667eea;
        }
        .property-name {
            font-weight: bold;
            color: #2c3e50;
        }
        .contact-info {
            color: #6c757d;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #6c757d;
            font-size: 12px;
        }
        .page-break {
            page-break-before: always;
        }
        @media print {
            body {
                background-color: white;
            }
            .header {
                background: #667eea !important;
                -webkit-print-color-adjust: exact;
            }
            thead {
                background: #2c3e50 !important;
                -webkit-print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>👥 Tenants Report</h1>
        <p>Generated on {{ date('F j, Y \a\t g:i A') }}</p>
    </div>

    <div class="summary">
        <h2>📊 Summary</h2>
        <div class="summary-grid">
            <div class="summary-item">
                <h3>{{ $tenants->count() }}</h3>
                <p>Total Tenants</p>
            </div>
            <div class="summary-item">
                <h3>{{ $tenants->where('status', 'active')->count() }}</h3>
                <p>Active Tenants</p>
            </div>
            <div class="summary-item">
                <h3>{{ $tenants->where('status', 'inactive')->count() }}</h3>
                <p>Inactive Tenants</p>
            </div>
            <div class="summary-item">
                <h3>{{ $tenants->where('gender', 'male')->count() }}</h3>
                <p>Male Tenants</p>
            </div>
            <div class="summary-item">
                <h3>{{ $tenants->where('gender', 'female')->count() }}</h3>
                <p>Female Tenants</p>
            </div>
            <div class="summary-item">
                <h3>{{ $tenants->unique('unit.property_id')->count() }}</h3>
                <p>Properties Occupied</p>
            </div>
        </div>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Contact</th>
                    <th>Property</th>
                    <th>Occupation</th>
                    <th>Status</th>
                    <th>Check-in Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tenants as $tenant)
                <tr>
                    <td>
                        <div class="tenant-name">{{ $tenant->first_name }} {{ $tenant->last_name }}</div>
                        <small class="contact-info">{{ $tenant->gender ?? 'N/A' }} • {{ $tenant->total_family_member ?? 0 }} members</small>
                    </td>
                    <td>
                        <div class="contact-info">
                            <div>{{ $tenant->mobile ?? 'N/A' }}</div>
                            @if($tenant->email)
                                <small>{{ $tenant->email }}</small>
                            @endif
                        </div>
                    </td>
                    <td>
                        @if($tenant->unit && $tenant->unit->property)
                            <div class="property-name">{{ $tenant->unit->property->name }}</div>
                            <small class="contact-info">{{ $tenant->unit->name ?? 'N/A' }}</small>
                        @else
                            <div style="color: #6c757d;">Not Assigned</div>
                        @endif
                    </td>
                    <td>
                        <div style="font-weight: bold;">{{ $tenant->occupation ?? 'N/A' }}</div>
                        @if($tenant->company_name)
                            <small class="contact-info">{{ $tenant->company_name }}</small>
                        @endif
                    </td>
                    <td>
                        @if($tenant->status === 'active')
                            <span class="status-badge status-active">Active</span>
                        @else
                            <span class="status-badge status-inactive">Inactive</span>
                        @endif
                    </td>
                    <td>
                        @if($tenant->check_in_date)
                            <div style="font-weight: bold;">{{ \Carbon\Carbon::parse($tenant->check_in_date)->format('M j, Y') }}</div>
                        @else
                            <div style="color: #6c757d;">N/A</div>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>This report was generated by the HRMS System</p>
        <p>© {{ date('Y') }} All rights reserved</p>
    </div>
</body>
</html> 