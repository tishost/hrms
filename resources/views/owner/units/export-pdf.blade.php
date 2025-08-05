<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Units Report</title>
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
        .status-rented {
            background-color: #28a745;
            color: white;
        }
        .status-free {
            background-color: #ffc107;
            color: #212529;
        }
        .unit-name {
            font-weight: bold;
            color: #667eea;
        }
        .property-name {
            font-weight: bold;
            color: #2c3e50;
        }
        .rent-amount {
            font-weight: bold;
            color: #28a745;
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
        <h1>🏬 Units Report</h1>
        <p>Generated on {{ date('F j, Y \a\t g:i A') }}</p>
    </div>

    <div class="summary">
        <h2>📊 Summary</h2>
        <div class="summary-grid">
            <div class="summary-item">
                <h3>{{ $units->count() }}</h3>
                <p>Total Units</p>
            </div>
            <div class="summary-item">
                <h3>{{ $units->where('tenant')->count() }}</h3>
                <p>Rented Units</p>
            </div>
            <div class="summary-item">
                <h3>{{ $units->where('tenant', null)->count() }}</h3>
                <p>Free Units</p>
            </div>
            <div class="summary-item">
                <h3>৳{{ number_format($units->sum('rent')) }}</h3>
                <p>Total Base Rent</p>
            </div>
            <div class="summary-item">
                <h3>{{ $units->unique('property_id')->count() }}</h3>
                <p>Properties</p>
            </div>
            <div class="summary-item">
                <h3>৳{{ number_format($units->sum(function($unit) { return $unit->rent + ($unit->charges ? $unit->charges->sum('amount') : 0); })) }}</h3>
                <p>Total with Charges</p>
            </div>
        </div>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Property</th>
                    <th>Unit</th>
                    <th>Base Rent (৳)</th>
                    <th>Charges (৳)</th>
                    <th>Total (৳)</th>
                    <th>Status</th>
                    <th>Tenant</th>
                </tr>
            </thead>
            <tbody>
                @foreach($units as $unit)
                <tr>
                    <td>
                        <div class="property-name">{{ $unit->property->name ?? '-' }}</div>
                        @if($unit->property && $unit->property->address)
                            <small style="color: #6c757d;">{{ $unit->property->address }}</small>
                        @endif
                    </td>
                    <td>
                        <div class="unit-name">{{ $unit->name }}</div>
                    </td>
                    <td>
                        <div class="rent-amount">৳{{ number_format($unit->rent) }}</div>
                    </td>
                    <td>
                        @if($unit->charges && $unit->charges->count() > 0)
                            <div style="color: #6c757d;">৳{{ number_format($unit->charges->sum('amount')) }}</div>
                            <small style="color: #6c757d;">
                                {{ $unit->charges->pluck('label')->implode(', ') }}
                            </small>
                        @else
                            <div style="color: #6c757d;">৳0</div>
                        @endif
                    </td>
                    <td>
                        <div class="rent-amount">৳{{ number_format($unit->rent + ($unit->charges ? $unit->charges->sum('amount') : 0)) }}</div>
                    </td>
                    <td>
                        @if($unit->tenant)
                            <span class="status-badge status-rented">Rented</span>
                        @else
                            <span class="status-badge status-free">Free</span>
                        @endif
                    </td>
                    <td>
                        @if($unit->tenant)
                            <div style="font-weight: bold;">{{ $unit->tenant->first_name }} {{ $unit->tenant->last_name }}</div>
                            @if($unit->tenant->phone)
                                <small style="color: #6c757d;">{{ $unit->tenant->phone }}</small>
                            @endif
                        @else
                            <div style="color: #6c757d;">-</div>
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