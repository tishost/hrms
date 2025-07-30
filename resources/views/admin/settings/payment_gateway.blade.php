@extends('layouts.admin')

@section('title', 'Payment Gateway Settings')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-credit-card"></i> Payment Gateway Settings
                    </h3>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Payment Methods Overview -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="text-primary">Available Payment Methods</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Method</th>
                                            <th>Status</th>
                                            <th>Transaction Fee</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($paymentMethods as $method)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-{{ $method->code === 'bkash' ? 'mobile-alt' : ($method->code === 'nagad' ? 'wallet' : ($method->code === 'rocket' ? 'rocket' : 'university')) }} text-primary mr-2"></i>
                                                        <strong>{{ $method->name }}</strong>
                                                    </div>
                                                </td>
                                                <td>
                                                    @if($method->is_active)
                                                        <span class="badge badge-success">Active</span>
                                                    @else
                                                        <span class="badge badge-secondary">Inactive</span>
                                                    @endif
                                                </td>
                                                <td>{{ $method->formatted_fee }}</td>
                                                <td>
                                                    <button class="btn btn-sm btn-primary" onclick="editMethod({{ $method->id }})">
                                                        <i class="fas fa-edit"></i> Configure
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- bKash Configuration -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-mobile-alt text-primary"></i> bKash TokenizedCheckout Configuration
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <!-- Credentials Info -->
                                    <div class="alert alert-info">
                                        <h6><i class="fas fa-info-circle"></i> bKash API Credentials</h6>
                                        <p class="mb-2">For bKash TokenizedCheckout, you need the following credentials:</p>
                                        <ul class="mb-0">
                                            <li><strong>Merchant ID:</strong> Your bKash merchant ID (used as username)</li>
                                            <li><strong>Merchant Password:</strong> Your bKash merchant password</li>
                                            <li><strong>API Key:</strong> Your bKash application key</li>
                                            <li><strong>API Secret:</strong> Your bKash application secret</li>
                                        </ul>
                                        <hr>
                                        <p class="mb-0"><strong>Note:</strong> Contact bKash support to get your live API credentials.</p>
                                    </div>
                                <div class="card-body">
                                    <form id="bkash-settings-form" action="{{ route('admin.settings.bkash.update') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="hidden" name="session_refresh" value="{{ time() }}">

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="bkash_is_active">
                                                        <i class="fas fa-toggle-on"></i> Enable bKash
                                                    </label>
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input" id="bkash_is_active" name="bkash_is_active" value="1"
                                                               {{ $paymentMethods->where('code', 'bkash')->first()->is_active ? 'checked' : '' }}>
                                                        <label class="custom-control-label" for="bkash_is_active">Enable bKash payment method</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="bkash_transaction_fee">
                                                        <i class="fas fa-percentage"></i> Transaction Fee (%)
                                                    </label>
                                                    <input type="number" class="form-control" id="bkash_transaction_fee" name="bkash_transaction_fee"
                                                           value="{{ $paymentMethods->where('code', 'bkash')->first()->transaction_fee ?? 1.5 }}"
                                                           step="0.01" min="0" max="10" required>
                                                    <small class="form-text text-muted">Transaction fee percentage for bKash payments</small>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="bkash_merchant_id">
                                                        <i class="fas fa-id-card"></i> Merchant ID (Username)
                                                    </label>
                                                    <input type="text" class="form-control" id="bkash_merchant_id" name="bkash_merchant_id"
                                                           value="{{ $paymentMethods->where('code', 'bkash')->first()->settings['merchant_id'] ?? '' }}" required>
                                                    <small class="form-text text-muted">Your bKash merchant ID (used as username)</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="bkash_api_key">
                                                        <i class="fas fa-key"></i> API Key
                                                    </label>
                                                    <input type="text" class="form-control" id="bkash_api_key" name="bkash_api_key"
                                                           value="{{ $paymentMethods->where('code', 'bkash')->first()->settings['api_key'] ?? '' }}" required>
                                                    <small class="form-text text-muted">Your bKash API key</small>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="bkash_merchant_password">
                                                        <i class="fas fa-user-lock"></i> Merchant Password
                                                    </label>
                                                    <input type="password" class="form-control" id="bkash_merchant_password" name="bkash_merchant_password"
                                                           value="{{ $paymentMethods->where('code', 'bkash')->first()->settings['merchant_password'] ?? '' }}" required>
                                                    <small class="form-text text-muted">Enter your bKash merchant password</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="bkash_api_secret">
                                                        <i class="fas fa-lock"></i> API Secret
                                                    </label>
                                                    <input type="password" class="form-control" id="bkash_api_secret" name="bkash_api_secret"
                                                           value="{{ $paymentMethods->where('code', 'bkash')->first()->settings['api_secret'] ?? '' }}" required>
                                                    <small class="form-text text-muted">Enter the bKash app secret</small>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="bkash_gateway_url">
                                                        <i class="fas fa-link"></i> Gateway URL
                                                    </label>
                                                    <input type="url" class="form-control" id="bkash_gateway_url" name="bkash_gateway_url"
                                                           value="{{ $paymentMethods->where('code', 'bkash')->first()->settings['gateway_url'] ?? 'https://www.bkash.com/payment' }}" required>
                                                    <small class="form-text text-muted">bKash payment gateway URL</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="bkash_display_name">
                                                        <i class="fas fa-tag"></i> Display Name
                                                    </label>
                                                    <input type="text" class="form-control" id="bkash_display_name" name="bkash_display_name"
                                                           value="{{ $paymentMethods->where('code', 'bkash')->first()->name ?? 'bKash' }}" required>
                                                    <small class="form-text text-muted">Display name for the payment method</small>
                                                </div>
                                            </div>
                                        </div>



                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="bkash_sandbox">
                                                        <i class="fas fa-flask"></i> Sandbox Mode
                                                    </label>
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input" id="bkash_sandbox" name="bkash_sandbox" value="1"
                                                               {{ $paymentMethods->where('code', 'bkash')->first()->settings['sandbox_mode'] ?? false ? 'checked' : '' }}>
                                                        <label class="custom-control-label" for="bkash_sandbox">Enable sandbox mode for testing</label>
                                                    </div>
                                                    <div id="sandbox-status" class="mt-2">
                                                        @if($paymentMethods->where('code', 'bkash')->first()->settings['sandbox_mode'] ?? false)
                                                            <span class="badge badge-warning">
                                                                <i class="fas fa-flask"></i> Sandbox Mode Active (Testing Only)
                                                            </span>
                                                        @else
                                                            <span class="badge badge-success">
                                                                <i class="fas fa-check-circle"></i> Live Mode Active
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mt-3">
                                            <div class="col-12">
                                                <div class="alert alert-warning">
                                                    <h6><i class="fas fa-exclamation-triangle"></i> Important Notes</h6>
                                                    <ul class="mb-0">
                                                        <li>Make sure all credentials are correct before saving</li>
                                                        <li>For live mode, uncheck "Sandbox Mode"</li>
                                                        <li>Test connection after saving settings</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mt-3">
                                            <div class="col-12">
                                                <button type="submit" class="btn btn-primary" id="save-bkash-btn">
                                                    <i class="fas fa-save"></i> Save bKash Settings
                                                </button>
                                                <button type="button" class="btn btn-info" onclick="testBkashConnection()">
                                                    <i class="fas fa-plug"></i> Test Connection
                                                </button>
                                                <button type="button" class="btn btn-warning" onclick="checkBkashStatus()">
                                                    <i class="fas fa-info-circle"></i> Check Status
                                                </button>
                                                <button type="button" class="btn btn-success" onclick="testBkashPayment()">
                                                    <i class="fas fa-credit-card"></i> Test Payment
                                                </button>
                                                <button type="button" class="btn btn-danger" onclick="clearBkashSettings()">
                                                    <i class="fas fa-trash"></i> Clear Settings
                                                </button>
                                                <a href="{{ route('admin.settings.index') }}" class="btn btn-secondary">
                                                    <i class="fas fa-arrow-left"></i> Back to Settings
                                                </a>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Other Payment Methods -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-cog"></i> Other Payment Methods Configuration
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('admin.settings.payment-gateway.update') }}" method="POST">
                                        @csrf

                                        @foreach($paymentMethods->whereNotIn('code', ['bkash']) as $method)
                                            <div class="row mb-3">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>
                                                            <i class="fas fa-{{ $method->code === 'nagad' ? 'wallet' : ($method->code === 'rocket' ? 'rocket' : 'university') }}"></i>
                                                            {{ $method->name }}
                                                        </label>
                                                        <div class="custom-control custom-switch">
                                                            <input type="checkbox" class="custom-control-input"
                                                                   id="method_{{ $method->id }}_active"
                                                                   name="payment_methods[{{ $method->id }}][is_active]" value="1"
                                                                   {{ $method->is_active ? 'checked' : '' }}>
                                                            <label class="custom-control-label" for="method_{{ $method->id }}_active">Enable</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Transaction Fee (%)</label>
                                                        <input type="number" class="form-control"
                                                               name="payment_methods[{{ $method->id }}][transaction_fee]"
                                                               value="{{ $method->transaction_fee }}"
                                                               step="0.01" min="0" max="10" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Settings</label>
                                                        <textarea class="form-control" rows="2"
                                                                  name="payment_methods[{{ $method->id }}][settings]"
                                                                  placeholder="Additional settings (JSON format)">{{ json_encode($method->settings, JSON_PRETTY_PRINT) }}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach

                                        <div class="row mt-3">
                                            <div class="col-12">
                                                <button type="submit" class="btn btn-success">
                                                    <i class="fas fa-save"></i> Save All Settings
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Test Payment Modal -->
<div class="modal fade" id="testPaymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Test Payment Gateway</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>This will test the payment gateway configuration with a test transaction.</p>
                <div class="form-group">
                    <label>Test Amount</label>
                    <input type="number" class="form-control" id="testAmount" value="10" min="1" max="1000">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="testPayment()">Test Payment</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function editMethod(methodId) {
    // Scroll to the specific method configuration
    const methodElement = document.querySelector(`[data-method-id="${methodId}"]`);
    if (methodElement) {
        methodElement.scrollIntoView({ behavior: 'smooth' });
    }
}

function testBkashConnection() {
    const button = event.target;
    const originalText = button.innerHTML;

    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Testing...';
    button.disabled = true;

    fetch('{{ route("admin.settings.bkash.test") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✅ ' + data.message);
        } else {
            alert('❌ ' + data.message);
        }
    })
    .catch(error => {
        alert('❌ Connection test failed: ' + error.message);
    })
    .finally(() => {
        button.innerHTML = originalText;
        button.disabled = false;
    });
}

function checkBkashStatus() {
    fetch('{{ route("admin.settings.bkash.status") }}')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const status = data.status;
            let message = 'bKash Configuration Status:\n\n';
            message += '✅ Configured: ' + (status.configured ? 'Yes' : 'No') + '\n';
            message += '✅ Merchant ID: ' + (status.username ? 'Set' : 'Not Set') + '\n';
            message += '✅ API Key: ' + (status.app_key ? 'Set' : 'Not Set') + '\n';
            message += '✅ API Secret: ' + (status.app_secret ? 'Set' : 'Not Set') + '\n';
            message += '✅ Base URL: ' + status.base_url + '\n';
            message += '🔧 Mode: ' + status.mode;

            alert(message);
        } else {
            alert('❌ ' + data.message);
        }
    })
    .catch(error => {
        alert('❌ Status check failed: ' + error.message);
    });
}

function testPayment() {
    const amount = document.getElementById('testAmount').value;
    if (!amount || amount < 1) {
        alert('Please enter a valid test amount');
        return;
    }

    // Here you would implement the test payment logic
    alert('Test payment functionality will be implemented here');
}

// Auto-save form data to localStorage
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        const formData = new FormData(form);
        const formKey = form.action;

        // Save form data on input change
        form.addEventListener('input', function() {
            const currentData = new FormData(form);
            localStorage.setItem(formKey, JSON.stringify(Object.fromEntries(currentData)));
        });

        // Restore form data on page load
        const savedData = localStorage.getItem(formKey);
        if (savedData) {
            const data = JSON.parse(savedData);
            Object.keys(data).forEach(key => {
                const input = form.querySelector(`[name="${key}"]`);
                if (input) {
                    if (input.type === 'checkbox') {
                        input.checked = data[key] === '1';
                    } else {
                        input.value = data[key];
                    }
                }
            });
        }
    });

    // Sandbox mode toggle warning
    const sandboxToggle = document.getElementById('bkash_sandbox');
    const sandboxStatus = document.getElementById('sandbox-status');

    if (sandboxToggle) {
        sandboxToggle.addEventListener('change', function() {
            if (this.checked) {
                if (!confirm('⚠️ আপনি Sandbox Mode enable করছেন। এটি শুধুমাত্র testing এর জন্য। Live payment এর জন্য এটি disable করুন।')) {
                    this.checked = false;
                    return;
                }
                // Update status indicator
                if (sandboxStatus) {
                    sandboxStatus.innerHTML = '<span class="badge badge-warning"><i class="fas fa-flask"></i> Sandbox Mode Active (Testing Only)</span>';
                }
            } else {
                if (!confirm('✅ আপনি Live Mode enable করছেন। এটি real payment এর জন্য। আপনি কি নিশ্চিত?')) {
                    this.checked = true;
                    return;
                }
                // Update status indicator
                if (sandboxStatus) {
                    sandboxStatus.innerHTML = '<span class="badge badge-success"><i class="fas fa-check-circle"></i> Live Mode Active</span>';
                }
            }
        });
    }

        // Handle form submission with loading state
    const bkashForm = document.getElementById('bkash-settings-form');
    const saveBtn = document.getElementById('save-bkash-btn');

    if (bkashForm && saveBtn) {
        bkashForm.addEventListener('submit', function(e) {
            const originalText = saveBtn.innerHTML;
            saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
            saveBtn.disabled = true;

            // Allow normal form submission
            setTimeout(() => {
                saveBtn.innerHTML = originalText;
                saveBtn.disabled = false;
            }, 3000);
        });
    }
});

function clearBkashSettings() {
    if (confirm('⚠️ আপনি কি নিশ্চিত যে আপনি সব bKash settings clear করতে চান?')) {
        // Clear form fields
        document.getElementById('bkash_merchant_id').value = '';
        document.getElementById('bkash_merchant_password').value = '';
        document.getElementById('bkash_api_key').value = '';
        document.getElementById('bkash_api_secret').value = '';
        document.getElementById('bkash_gateway_url').value = 'https://www.bkash.com/payment';
        document.getElementById('bkash_display_name').value = 'bKash';
        document.getElementById('bkash_transaction_fee').value = '1.5';
        document.getElementById('bkash_sandbox').checked = false;

        // Update status indicator
        const sandboxStatus = document.getElementById('sandbox-status');
        if (sandboxStatus) {
            sandboxStatus.innerHTML = '<span class="badge badge-success"><i class="fas fa-check-circle"></i> Live Mode Active</span>';
        }

        alert('✅ bKash settings cleared successfully!');
    }
}

function testBkashPayment() {
    const button = event.target;
    const originalText = button.innerHTML;

    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Testing Payment...';
    button.disabled = true;

    fetch('{{ route("admin.settings.bkash.test-payment") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            let message = '✅ ' + data.message + '\n\n';
            message += 'Payment ID: ' + data.paymentID + '\n';
            if (data.demo_mode) {
                message += 'Mode: Demo (Sandbox)\n';
            } else {
                message += 'Mode: Live\n';
            }
            alert(message);
        } else {
            let message = '❌ ' + data.message + '\n\n';
            if (data.suggestion) {
                message += 'Suggestion: ' + data.suggestion + '\n\n';
            }
            if (data.details && data.details.statusCode) {
                message += 'Error Code: ' + data.details.statusCode + '\n';
                message += 'Error Message: ' + data.details.statusMessage;
            }
            alert(message);
        }
    })
    .catch(error => {
        alert('❌ Payment test failed: ' + error.message);
    })
    .finally(() => {
        button.innerHTML = originalText;
        button.disabled = false;
    });
}


</script>
@endpush
