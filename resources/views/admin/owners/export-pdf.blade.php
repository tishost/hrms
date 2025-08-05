<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            color: #2c3e50;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            color: #7f8c8d;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #495057;
        }
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .status-active {
            color: #28a745;
            font-weight: bold;
        }
        .status-pending {
            color: #ffc107;
            font-weight: bold;
        }
        .status-expired {
            color: #dc3545;
            font-weight: bold;
        }
        .status-no-subscription {
            color: #6c757d;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <p>Generated on: {{ $generated_at }}</p>
        <p>Total Records: {{ $owners->count() }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Owner ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Country</th>
                <th>Gender</th>
                <th>Subscription Status</th>
                <th>Plan</th>
                <th>Expiry Date</th>
                <th>Registration Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($owners as $owner)
            <tr>
                <td>{{ $owner->owner_uid }}</td>
                <td>{{ $owner->user->name ?? $owner->name }}</td>
                <td>{{ $owner->user->email ?? $owner->email }}</td>
                <td>{{ $owner->phone }}</td>
                <td>{{ $owner->country ?? 'N/A' }}</td>
                <td>{{ ucfirst($owner->gender ?? 'N/A') }}</td>
                <td>
                    @if($owner->subscription)
                        @if($owner->subscription->status === 'active')
                            <span class="status-active">Active</span>
                        @elseif($owner->subscription->status === 'pending')
                            <span class="status-pending">Pending</span>
                        @elseif($owner->subscription->status === 'expired')
                            <span class="status-expired">Expired</span>
                        @else
                            {{ ucfirst($owner->subscription->status) }}
                        @endif
                    @else
                        <span class="status-no-subscription">No Subscription</span>
                    @endif
                </td>
                <td>
                    @if($owner->subscription && $owner->subscription->plan)
                        {{ $owner->subscription->plan->name }}
                    @else
                        N/A
                    @endif
                </td>
                <td>
                    @if($owner->subscription && $owner->subscription->end_date)
                        {{ $owner->subscription->end_date->format('Y-m-d') }}
                    @else
                        N/A
                    @endif
                </td>
                <td>{{ $owner->created_at->format('Y-m-d') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>This report was generated automatically by the HRMS Admin System</p>
        <p>Page {{ $PAGE_NUM }} of {{ $PAGE_COUNT }}</p>
    </div>
</body>
</html> 