@extends('layouts.owner')

@section('title', 'Tenant List')

@section('head')
    <style>
        .responsive-table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }
        .responsive-table th, .responsive-table td {
            padding: 12px 16px;
            text-align: left;
        }
        .responsive-table th {
            background: #e8edff;
            color: #3f37c9;
            font-weight: 600;
            border-bottom: 2px solid #6c63ff;
        }
        .responsive-table tr:not(:last-child) {
            border-bottom: 1px solid #f0f0f0;
        }
        .responsive-table tbody tr:hover {
            background: #f6f8ff;
        }
        @media (max-width: 768px) {
            .responsive-table thead {
                display: none;
            }
            .responsive-table, .responsive-table tbody, .responsive-table tr, .responsive-table td {
                display: block;
                width: 100%;
            }
            .responsive-table tr {
                margin-bottom: 16px;
                border-radius: 8px;
                box-shadow: 0 1px 4px rgba(0,0,0,0.03);
                background: #fff;
            }
            .responsive-table td {
                padding: 10px 16px;
                text-align: right;
                position: relative;
            }
            .responsive-table td::before {
                content: attr(data-label);
                position: absolute;
                left: 16px;
                top: 10px;
                font-weight: 600;
                color: #3f37c9;
                text-align: left;
            }
        }
        .live-search-input {
            width: 260px;
            max-width: 100%;
            padding: 8px 14px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-bottom: 18px;
            font-size: 16px;
            float: right;
        }
        @media (max-width: 768px) {
            .live-search-input {
                float: none;
                width: 100%;
                margin-bottom: 14px;
            }
        }
        .btn-view {
            background: #4895ef;
            color: #fff;
            border: none;
            border-radius: 4px;
            padding: 6px 16px;
            font-size: 15px;
            margin-right: 6px;
            transition: background 0.2s;
            text-decoration: none;
            display: inline-block;
        }
        .btn-view:hover {
            background: #4361ee;
            color: #fff;
        }
        .btn-edit {
            background: #f8961e;
            color: #fff;
            border: none;
            border-radius: 4px;
            padding: 6px 16px;
            font-size: 15px;
            transition: background 0.2s;
            text-decoration: none;
            display: inline-block;
        }
        .btn-edit:hover {
            background: #f3722c;
            color: #fff;
        }
        .add-tenant-btn {
            background: #4cc9f0;
            color: #fff;
            border: none;
            border-radius: 5px;
            padding: 9px 22px;
            font-size: 16px;
            font-weight: 600;
            box-shadow: 0 2px 6px rgba(76,201,240,0.08);
            transition: background 0.2s, box-shadow 0.2s;
            text-decoration: none;
            display: inline-block;
            margin-bottom: 18px;
        }
        .add-tenant-btn:hover {
            background: #4895ef;
            color: #fff;
            box-shadow: 0 4px 12px rgba(72,149,239,0.12);
        }
        .table-header-row {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            margin-bottom: 8px;
        }
        @media (max-width: 768px) {
            .table-header-row {
                flex-direction: column;
                align-items: stretch;
                gap: 10px;
            }
            .add-tenant-btn {
                width: 100%;
                text-align: center;
            }
        }
        .btn-assign {
            background: #43aa8b;
            color: #fff;
            border: none;
            border-radius: 4px;
            padding: 6px 16px;
            font-size: 15px;
            margin-left: 6px;
            transition: background 0.2s;
            text-decoration: none;
            display: inline-block;
        }
        .btn-assign:hover {
            background: #277c5d;
            color: #fff;
        }
    </style>
@endsection

@section('content')
<div class="container mt-4">
    <h2 class="mb-3">Tenant List</h2>
    <div class="table-header-row">
        <a href="{{ route('owner.tenants.create') }}" class="add-tenant-btn">+ Add Tenant</a>
    </div>
    <input type="text" id="liveSearch" class="live-search-input" placeholder="Search tenants...">
    <table class="responsive-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Mobile</th>
                <th>Email</th>
                <th>Family Member</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="tenantTableBody">
            @foreach ($tenants as $tenant)
                <tr>
                    <td data-label="Name">{{ $tenant->first_name ?? '-' }}</td>
                    <td data-label="Mobile">{{ $tenant->mobile ?? '-' }}</td>
                    <td data-label="Email">{{ $tenant->email ?? '-' }}</td>
                    <td data-label="Family Member">{{ $tenant->total_family_member ?? '-' }}</td>
                    <td data-label="Actions">
                        <a href="#" class="btn-view">View</a>
                        <a href="#" class="btn-edit">Edit</a>
                        @if (empty($tenant->unit_id))
                            <a href="{{ route('owner.rents.create', $tenant->id) }}" class="btn-assign">Tenant Assign</a>
                        @else
                            @if($tenant->status === 'active')
                                <a href="{{ route('owner.checkouts.create', $tenant->id) }}" class="btn-assign" style="background: #f72585;">Check-out</a>
                            @else
                                <button class="btn-assign" style="background: #ccc; cursor: not-allowed;" disabled>Checked Out</button>
                            @endif
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @if(count($tenants) === 0)
        <div class="text-center mt-4 text-muted">No tenants found</div>
    @endif
</div>
@endsection

@section('scripts')
<script>
document.getElementById('liveSearch').addEventListener('keyup', function() {
    let value = this.value.toLowerCase();
    let rows = document.querySelectorAll('#tenantTableBody tr');
    rows.forEach(function(row) {
        let text = row.textContent.toLowerCase();
        row.style.display = text.includes(value) ? '' : 'none';
    });
});
</script>
@endsection
