<div class="toast align-items-center text-white bg-{{ $type }} border-0 mb-2" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
        <div class="toast-body">
            {{ $type === 'success' ? '✅' : ($type === 'danger' ? '⚠️' : 'ℹ️') }} {{ $message }}
        </div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
</div>