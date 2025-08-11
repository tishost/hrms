@extends('admin.layouts.app')

@section('title', 'Charges Setup')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-money-bill-wave me-2"></i>
                        Charges Setup
                    </h4>
                    <a href="{{ route('admin.charges.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>
                        Add New Charge
                    </a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="30%">Charge Label</th>
                                    <th width="20%">Amount</th>
                                    <th width="15%">Status</th>
                                    <th width="15%">Created</th>
                                    <th width="15%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($charges as $index => $charge)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <strong>{{ $charge->label }}</strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-success fs-6">
                                                {{ $charge->formatted_amount }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($charge->is_active)
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check-circle me-1"></i>
                                                    Active
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">
                                                    <i class="fas fa-times-circle me-1"></i>
                                                    Inactive
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                {{ $charge->formatted_created_at }}
                                            </small>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.charges.edit', $charge) }}" 
                                                   class="btn btn-sm btn-outline-primary" 
                                                   title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                
                                                <form action="{{ route('admin.charges.toggle-status', $charge) }}" 
                                                      method="POST" 
                                                      class="d-inline">
                                                    @csrf
                                                    <button type="submit" 
                                                            class="btn btn-sm btn-outline-{{ $charge->is_active ? 'warning' : 'success' }}"
                                                            title="{{ $charge->is_active ? 'Deactivate' : 'Activate' }}"
                                                            onclick="return confirm('Are you sure you want to {{ $charge->is_active ? 'deactivate' : 'activate' }} this charge?')">
                                                        <i class="fas fa-{{ $charge->is_active ? 'pause' : 'play' }}"></i>
                                                    </button>
                                                </form>
                                                
                                                <form action="{{ route('admin.charges.destroy', $charge) }}" 
                                                      method="POST" 
                                                      class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-sm btn-outline-danger"
                                                            title="Delete"
                                                            onclick="return confirm('Are you sure you want to delete this charge? This action cannot be undone.')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-inbox fa-3x mb-3"></i>
                                                <h5>No Charges Found</h5>
                                                <p>Start by adding your first charge.</p>
                                                <a href="{{ route('admin.charges.create') }}" class="btn btn-primary">
                                                    <i class="fas fa-plus me-1"></i>
                                                    Add First Charge
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($charges->count() > 0)
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="text-muted">
                                <small>Total: {{ $charges->count() }} charge(s)</small>
                            </div>
                            <div class="text-muted">
                                <small>Last updated: {{ now()->format('M d, Y H:i') }}</small>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);

    // Confirm delete action
    function confirmDelete(chargeName) {
        return confirm('Are you sure you want to delete "' + chargeName + '"? This action cannot be undone.');
    }
</script>
@endpush
