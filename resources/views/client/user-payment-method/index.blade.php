@extends('layouts.front')

@section('content')
    <div class="container-fluid">
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    <h2 class="mb-4">Your Payment Methods</h2>
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addPaymentMethodModal">Add Payment Method</button>
    <div class="card">
        <div class="card-body">
            @if($paymentMethods->isEmpty())
                <p>No payment methods registered.</p>
            @else
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Payment Method Name</th>
                        <th>Number / Details</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($paymentMethods as $index => $method)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            {{ $method->card_type ?? '-' }}
                            @if($method->is_default)
                                <span class="badge bg-success ms-1">Default</span>
                            @endif
                        </td>
                        <td>
                            ****{{ $method->last_four }}
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm btn-warning"
                                onclick='showEditPaymentMethodModal(@json($method))'>
                                Edit
                            </button>
                            <button type="button" class="btn btn-sm btn-danger"
                                onclick="showDeletePaymentMethodModal({{ $method->id }})">
                                Delete
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>
    </div>
</div>

<!-- Add Payment Method Modal -->
<div class="modal fade" id="addPaymentMethodModal" tabindex="-1" aria-labelledby="addPaymentMethodModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('user.user-payment-method.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="addPaymentMethodModalLabel">Add Credit/Debit Card</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="cardType" class="form-label">Card Type</label>
                    <select class="form-select" id="cardType" name="card_type" required>
                        <option value="" disabled selected>Select card type</option>
                        <option value="Visa">Visa</option>
                        <option value="MasterCard">MasterCard</option>
                        <option value="Amex">American Express</option>
                        <option value="Discover">Discover</option>
                        <option value="JCB">JCB</option>
                        <option value="Diners">Diners Club</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="cardNumber" class="form-label">Card Number</label>
                    <input type="text" maxlength="19" class="form-control" id="cardNumber" name="card_number" placeholder="•••• •••• •••• ••••" required oninput="formatCardNumber(this)">
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="expiryMonth" class="form-label">Expiry Month</label>
                        <select class="form-select" id="expiryMonth" name="expiry_month" required>
                            <option value="" disabled selected>MM</option>
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}">{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="expiryYear" class="form-label">Expiry Year</label>
                        <input type="text" maxlength="4" class="form-control" id="expiryYear" name="expiry_year" placeholder="YYYY" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="billingAddress" class="form-label">Billing Address</label>
                    <input type="text" class="form-control" id="billingAddress" name="billing_address">
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="billingCity" class="form-label">City</label>
                        <input type="text" class="form-control" id="billingCity" name="billing_city">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="billingState" class="form-label">State</label>
                        <input type="text" class="form-control" id="billingState" name="billing_state">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="billingZip" class="form-label">ZIP/Postal Code</label>
                        <input type="text" class="form-control" id="billingZip" name="billing_zip">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="billingCountry" class="form-label">Country</label>
                        <input type="text" class="form-control" id="billingCountry" name="billing_country">
                    </div>
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" value="1" id="isDefault" name="is_default">
                    <label class="form-check-label" for="isDefault">
                        Set as default
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Card</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Payment Method Modal -->
<div class="modal fade" id="editPaymentMethodModal" tabindex="-1" aria-labelledby="editPaymentMethodModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="editPaymentMethodForm" method="POST" class="modal-content">
            @csrf
            @method('PUT')
            <input type="hidden" id="editPaymentMethodId" name="id">
            <div class="modal-header">
                <h5 class="modal-title" id="editPaymentMethodModalLabel">Edit Credit/Debit Card</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="editExpiryMonth" class="form-label">Expiry Month</label>
                        <select class="form-select" id="editExpiryMonth" name="expiry_month" required>
                            <option value="" disabled>MM</option>
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}">{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="editExpiryYear" class="form-label">Expiry Year</label>
                        <input type="text" maxlength="4" class="form-control" id="editExpiryYear" name="expiry_year" placeholder="YYYY" required>
                    </div>
                </div>
                <button class="btn btn-link p-0 mb-3" type="button" data-bs-toggle="collapse" data-bs-target="#editAdvancedFields" aria-expanded="false" aria-controls="editAdvancedFields">
                    More options
                </button>
                <div class="collapse" id="editAdvancedFields">
                    <div class="mb-3">
                        <label for="editCardType" class="form-label">Card Type</label>
                        <select class="form-select" id="editCardType" name="card_type">
                            <option value="" disabled>Select card type</option>
                            <option value="Visa">Visa</option>
                            <option value="MasterCard">MasterCard</option>
                            <option value="Amex">American Express</option>
                            <option value="Discover">Discover</option>
                            <option value="JCB">JCB</option>
                            <option value="Diners">Diners Club</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="editCardNumber" class="form-label">Card Number</label>
                        <input type="text" maxlength="19" class="form-control" id="editCardNumber" name="card_number" placeholder="•••• •••• •••• ••••" oninput="formatCardNumber(this)">
                        <small class="text-muted">Leave blank to keep unchanged.</small>
                    </div>
                    <div class="mb-3">
                        <label for="editBillingAddress" class="form-label">Billing Address</label>
                        <input type="text" class="form-control" id="editBillingAddress" name="billing_address">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="editBillingCity" class="form-label">City</label>
                            <input type="text" class="form-control" id="editBillingCity" name="billing_city">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="editBillingState" class="form-label">State</label>
                            <input type="text" class="form-control" id="editBillingState" name="billing_state">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="editBillingZip" class="form-label">ZIP/Postal Code</label>
                            <input type="text" class="form-control" id="editBillingZip" name="billing_zip">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="editBillingCountry" class="form-label">Country</label>
                            <input type="text" class="form-control" id="editBillingCountry" name="billing_country">
                        </div>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" value="1" id="editIsDefault" name="is_default">
                        <label class="form-check-label" for="editIsDefault">
                            Set as default
                        </label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Update Card</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Payment Method Modal -->
<div class="modal fade" id="deletePaymentMethodModal" tabindex="-1" aria-labelledby="deletePaymentMethodModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="deletePaymentMethodForm" method="POST" class="modal-content">
            @csrf
            @method('DELETE')
            <div class="modal-header">
                <h5 class="modal-title" id="deletePaymentMethodModalLabel">Delete Payment Method</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this payment method?</p>
                <div class="alert alert-warning mb-0">
                    This action cannot be undone.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-danger">Delete</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function showEditPaymentMethodModal(method) {
        // Set values
        document.getElementById('editPaymentMethodId').value = method.id;
        document.getElementById('editCardType').value = method.card_type ?? '';
        document.getElementById('editExpiryMonth').value = method.expiry_month ?? '';
        document.getElementById('editExpiryYear').value = method.expiry_year ?? '';
        document.getElementById('editCardNumber').value = '';
        document.getElementById('editBillingAddress').value = method.billing_address ?? '';
        document.getElementById('editBillingCity').value = method.billing_city ?? '';
        document.getElementById('editBillingState').value = method.billing_state ?? '';
        document.getElementById('editBillingZip').value = method.billing_zip ?? '';
        document.getElementById('editBillingCountry').value = method.billing_country ?? '';
        document.getElementById('editIsDefault').checked = !!method.is_default;

        // Hide advanced fields by default
        document.getElementById('editAdvancedFields').classList.remove('show');

        // Set form action
        document.getElementById('editPaymentMethodForm').action = `/user/user-payment-method/${method.id}`;

        // Show modal
        var modal = new bootstrap.Modal(document.getElementById('editPaymentMethodModal'));
        modal.show();
    }

    // Toggle chevron icon on collapse
    document.addEventListener('DOMContentLoaded', function () {
        var collapse = document.getElementById('editAdvancedFields');
        var chevron = document.getElementById('chevronIcon');
        var btn = document.getElementById('toggleAdvancedFieldsBtn');
        if (collapse && chevron && btn) {
            collapse.addEventListener('show.bs.collapse', function () {
                chevron.classList.remove('bi-chevron-down');
                chevron.classList.add('bi-chevron-up');
            });
            collapse.addEventListener('hide.bs.collapse', function () {
                chevron.classList.remove('bi-chevron-up');
                chevron.classList.add('bi-chevron-down');
            });
        }
    });
    
    function showDeletePaymentMethodModal(id) {
        var form = document.getElementById('deletePaymentMethodForm');
        form.action = `/user/user-payment-method/${id}`;
        var modal = new bootstrap.Modal(document.getElementById('deletePaymentMethodModal'));
        modal.show();
    }

    function formatCardNumber(input) {
        let value = input.value.replace(/\D/g, '').substring(0, 16);
        let formatted = value.replace(/(.{4})/g, '$1 ').trim();
        input.value = formatted;
    }
</script>
@endpush