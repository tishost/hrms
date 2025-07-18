<!-- filepath: resources/views/admin/owners/index.blade.php -->
@extends('layouts.admin')

@section('content')
<div class="container">
    <h2>Owner List</h2>
    <a href="{{ route('owners.create') }}" class="btn btn-primary mb-3">Add New Owner</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Country</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($owners as $owner)
            <tr>
                <td>{{ $owner->name }}</td>
                <td>{{ $owner->email }}</td>
                <td>{{ $owner->phone }}</td>
                <td>{{ $owner->country }}</td>
                <td>
                   <button class="btn btn-sm btn-primary edit-owner-btn" 
                    data-id="{{ $owner->id }}" 
                    data-toggle="modal" 
                    data-target="#editOwnerModal">
                     Edit
                   </button>

                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection


<div class="modal fade" id="editOwnerModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form id="edit-owner-form">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Edit Owner</h5>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>

        <div class="modal-body">
          <input type="hidden" name="owner_id" id="owner_id">
          <div class="form-group">
            <label>Name</label>
            <input type="text" name="name" id="owner_name" class="form-control">
          </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" id="owner_email" class="form-control"> 
            </div>
            <div class="form-group">    
                <label>Phone</label>
                <input type="text" name="phone" id="owner_phone" class="form-control">  
            </div>
            <div class="form-group">
                <label>Country</label>
                <select name="country" id="owner_country" class="form-control">
            </div>
        <!-- Options will be injected via AJAX -->
    </select>
</div>

        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Udate</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">
           <span aria-hidden="true">&times;</span>
  
          Close</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div id="updateToast" class="toast" style="position: fixed; bottom: 20px; right: 20px;" data-delay="3000">
  <div class="toast-header">
    <strong class="mr-auto text-success">Update Successful</strong>
    <small>Now</small>
    <button type="button" class="ml-2 mb-1 close" data-dismiss="toast">&times;</button>
  </div>
  <div class="toast-body">
    Owner details have been updated.
  </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function () {
    $(document).on('click', '.edit-owner-btn', function () {
        let ownerId = $(this).data('id');
        $.get(`/admin/owners/${ownerId}/edit`, function (data) {
                const owner = data.owner;
                $('#owner_id').val(owner.id);
                $('#owner_name').val(owner.name);
                $('#owner_email').val(owner.email);
                $('#owner_phone').val(owner.phone);
                let countryOptions = '';
                data.countries.forEach(function (country) {
                    const selected = (owner.country === country) ? 'selected' : '';
                    countryOptions += `<option value="${country}" ${selected}>${country}</option>`;
                });
                $('#owner_country').html(countryOptions);

                $('#editOwnerModal').modal('show');
            });
        });
});
$(document).on('click', '.close', function () {
    $('#editOwnerModal').modal('hide');
});

$('#editOwnerModal').on('hidden.bs.modal', function () {
    $('body').removeClass('modal-open');
    $('.modal-backdrop').remove();
});

let toastPending = false;

$('#edit-owner-form').submit(function (e) {
    e.preventDefault();

    const ownerId = $('#owner_id').val();
    const formData = $(this).serialize();
    toastPending = true; // mark that toast should trigger

    $.ajax({
        url: `/admin/owners/${ownerId}`,
        type: 'POST',
        data: formData,
        success: function (response) {
            if (response.success) {
                $('#editOwnerModal').modal('hide');
            }
        },
        error: function () {
            toastPending = false;
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'error',
                title: 'Failed to update owner',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        }
    });
});

$('#editOwnerModal').on('hidden.bs.modal', function () {
    if (toastPending) {
        Swal.fire({
            toast: true,
            position: 'top-center',
            icon: 'success',
            title: 'Owner updated successfully!',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
        toastPending = false; // reset the flag
    }
});

</script>
@endpush
