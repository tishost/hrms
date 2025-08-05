<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Properties Report</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #667eea;
            padding-bottom: 20px;
        }
        
        .header h1 {
            color: #2c3e50;
            margin: 0 0 10px 0;
            font-size: 24px;
        }
        
        .header p {
            margin: 5px 0;
            color: #666;
            font-size: 14px;
        }
        
        .summary {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #667eea;
        }
        
        .summary h3 {
            margin: 0 0 10px 0;
            color: #2c3e50;
            font-size: 16px;
        }
        
        .summary p {
            margin: 5px 0;
            color: #666;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 10px;
        }
        
        th {
            background: #2c3e50;
            color: white;
            padding: 10px 8px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #34495e;
        }
        
        td {
            padding: 8px;
            border: 1px solid #ddd;
            vertical-align: top;
        }
        
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .badge {
            padding: 3px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
            color: white;
        }
        
        .badge-success {
            background-color: #28a745;
        }
        
        .badge-warning {
            background-color: #ffc107;
            color: #333;
        }
        
        .badge-info {
            background-color: #17a2b8;
        }
        
        .badge-primary {
            background-color: #007bff;
        }
        
        .property-name {
            font-weight: bold;
            color: #2c3e50;
        }
        
        .property-address {
            color: #666;
            font-size: 9px;
            margin-top: 2px;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        @media print {
            body {
                margin: 0;
                padding: 15px;
            }
            
            .header {
                margin-bottom: 20px;
            }
            
            table {
                font-size: 9px;
            }
            
            th, td {
                padding: 6px 4px;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🏢 Properties Report</h1>
        <p>Generated on: {{ date('F d, Y \a\t g:i A') }}</p>
        <p>Total Properties: {{ $properties->count() }}</p>
    </div>
    
    <div class="summary">
        <h3>📊 Report Summary</h3>
        <p><strong>Total Properties:</strong> {{ $properties->count() }}</p>
        <p><strong>Active Properties:</strong> {{ $properties->where('status', 'active')->count() }}</p>
        <p><strong>Inactive Properties:</strong> {{ $properties->where('status', 'inactive')->count() }}</p>
        <p><strong>Pending Properties:</strong> {{ $properties->where('status', 'pending')->count() }}</p>
        <p><strong>Total Units:</strong> {{ $properties->sum(function($property) { return $property->units->count(); }) }}</p>
    </div>
    
    @if($properties->count() > 0)
    <table>
        <thead>
            <tr>
                <th>Building Name</th>
                <th>Type</th>
                <th>Address</th>
                <th>Country</th>
                <th>Total Units</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($properties as $property)
            <tr>
                <td>
                    <div class="property-name">{{ $property->name }}</div>
                    @if($property->city)
                        <div class="property-address">{{ $property->city }}, {{ $property->state }}</div>
                    @endif
                </td>
                <td>
                    <span class="badge badge-info">{{ ucfirst($property->property_type ?? 'residential') }}</span>
                </td>
                <td>
                    <div class="property-address">
                        {{ $property->address }}
                        @if($property->zip_code)
                            <br>{{ $property->zip_code }}
                        @endif
                    </div>
                </td>
                <td>{{ $property->country }}</td>
                <td>
                    <span class="badge badge-primary">{{ $property->units->count() }}</span>
                </td>
                <td>
                    <span class="badge badge-{{ $property->status == 'active' ? 'success' : ($property->status == 'inactive' ? 'warning' : 'info') }}">
                        {{ ucfirst($property->status ?? 'pending') }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div style="text-align: center; padding: 40px; color: #666;">
        <h3>No Properties Found</h3>
        <p>No properties match the current filters.</p>
    </div>
    @endif
    
    <div class="footer">
        <p>This report was generated automatically by the HRMS Property Management System</p>
        <p>© {{ date('Y') }} HRMS - All rights reserved</p>
    </div>
</body>
</html> 