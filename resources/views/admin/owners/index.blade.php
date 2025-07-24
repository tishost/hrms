<!-- filepath: resources/views/admin/owners/index.blade.php -->
@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Owner Management</h3>
                    <a href="{{ route('admin.owners.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add New Owner
                    </a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Country</th>
                                    <th>Status</th>
                                    <th>Super Admin</th>
                                    <th>Properties</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($owners as $owner)
                                <tr>
                                    <td>{{ $owner->id }}</td>
                                    <td>{{ $owner->name }}</td>
                                    <td>{{ $owner->email }}</td>
                                    <td>{{ $owner->phone }}</td>
                                    <td>{{ $owner->country }}</td>
                                    <td>
                                        <span class="badge badge-{{ $owner->status === 'active' ? 'success' : ($owner->status === 'inactive' ? 'warning' : 'danger') }}">
                                            {{ ucfirst($owner->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($owner->is_super_admin)
                                            <span class="badge badge-primary">Super Admin</span>
                                        @else
                                            <span class="badge badge-secondary">Owner</span>
                                        @endif
                                    </td>
                                    <td>{{ $owner->properties->count() }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-info" onclick="editOwner({{ $owner->id }})">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            @if(!$owner->is_super_admin)
                                            <form action="{{ route('admin.owners.destroy', $owner->id) }}" method="POST" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this owner?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Owner Modal -->
<div class="modal fade" id="editOwnerModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Owner</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="editOwnerForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Name</label>
                                <input type="text" class="form-control" name="name" id="edit_name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" class="form-control" name="email" id="edit_email" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Phone</label>
                                <input type="text" class="form-control" name="phone" id="edit_phone" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Country</label>
                                <select class="form-control" name="country" id="edit_country" required>
                                    @foreach($countries as $code => $name)
                                        <option value="{{ $name }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Gender</label>
                                <select class="form-control" name="gender" id="edit_gender">
                                    <option value="">Select Gender</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Address</label>
                                <textarea class="form-control" name="address" id="edit_address" rows="3" required></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Owner</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function editOwner(ownerId) {
    $.get(`/admin/owners/${ownerId}/edit`, function(data) {
        $('#edit_name').val(data.owner.name);
        $('#edit_email').val(data.owner.email);
        $('#edit_phone').val(data.owner.phone);
        $('#edit_country').val(data.owner.country);
        $('#edit_gender').val(data.owner.gender);
        $('#edit_address').val(data.owner.address);

        $('#editOwnerForm').attr('action', `/admin/owners/${ownerId}`);
        $('#editOwnerModal').modal('show');
    });
}

$('#editOwnerForm').on('submit', function(e) {
    e.preventDefault();

    $.ajax({
        url: $(this).attr('action'),
        method: 'PUT',
        data: $(this).serialize(),
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if(response.success) {
                $('#editOwnerModal').modal('hide');
                location.reload();
            }
        },
        error: function(xhr) {
            alert('Error updating owner');
        }
    });
});
</script>
@endpush
