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

        <div class="row mb-4">
            <div class="col-md-12">
                <h2 class="text-center mb-4 fw-bold">Choose Your Perfect Plan</h2>
            </div>
        </div>
        <div class="row">
            @foreach ($packages as $package)
                <div class="col-md-4 col-lg-3 mb-4">
                    <div class="card h-100 p-3">
                        <div class="card-body justify-content-between d-flex flex-column">
                            <div>
                                <h5 class="card-title text-center">{{ $package->name }}</h5>
                                <div class="text-center m-4">
                                    <img class="w-75" src="{{ asset($package->logo) }}" alt="{{ $package->name }}" class="img-fluid mb-3">
                                </div>
                                <h6 class="card-subtitle mb-2 text-primary text-center price fs-3 fw-bold">
                                    ${{ number_format($package->price, 2, '.', ',') }}
                                </h6>
                                <p class="card-text text-muted text-center fw-thin">
                                    Duration: {{ $package->duration }} month{{ $package->duration > 1 ? 's' : ''}}<br>
                                </p>
                            </div>
                            @if($package->status)
                                <div class="mt-4">
                                    <button type="button"
                                        class="btn btn-primary w-100 fw-bold py-2" 
                                        onclick="showBuyModal(
                                            {{ $package->id }}, 
                                            '{{ addslashes($package->name) }}', 
                                            '{{ number_format($package->price, 2, '.', ',') }}',
                                            '{{ $package->duration }} month{{ $package->duration > 1 ? 's' : '' }}'
                                        )">
                                        Buy
                                    </button>
                                    <form id="buy-form-{{ $package->id }}" action="{{ route('user.package.buy', $package->id) }}" method="POST" style="display:none;">
                                        @csrf
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="buyModal" tabindex="-1" aria-labelledby="buyModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="buyModalLabel">
                        <i class="bi bi-bag-check-fill me-2"></i>Confirm Purchase
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="modalBuyForm" action="{{ route('user.package.buy', 0) }}" method="POST">
                    @csrf
                    <input type="hidden" name="package_id" id="modalPackageId" value="">
                    <div class="modal-body">
                        <div class="mb-3">
                            <h6 class="fw-bold mb-2 text-primary" id="modalPackageName"></h6>
                            <ul class="list-group mb-3">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>Price</span>
                                    <span class="fw-bold text-primary fs-5" id="modalPackagePrice"></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>Duration</span>
                                    <span id="modalPackageDuration"></span>
                                </li>
                            </ul>
                        </div>
                        @if($userPaymentMethods->count())
                            <div class="mb-3">
                                <label for="paymentMethod" class="form-label fw-bold">Payment Method</label>
                                <select class="form-select" id="paymentMethod" name="payment_method" required>
                                    @if($userPaymentMethods->count())
                                        @foreach($userPaymentMethods as $method)
                                            <option value="{{ $method->id }}" {{ $defaultPaymentMethod && $defaultPaymentMethod->id == $method->id ? 'selected' : '' }}>
                                                {{ $method->card_type }} ****{{ $method->last_four }}
                                                @if($method->is_default) (Default) @endif
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        @else
                            <div class="alert alert-warning">
                                No payment methods available. Please add a payment method first.
                            </div>
                        @endif
                        <div class="alert alert-info small">
                            Please review your package details and select your preferred payment method before confirming.
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary fw-bold" id="confirmBuyBtn">
                            <i class="bi bi-cart-check me-1"></i>Yes, Buy
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function showBuyModal(packageId, packageName, packagePrice, packageDuration = null) {
        document.getElementById('modalPackageId').value = packageId;
        document.getElementById('modalPackageName').textContent = packageName;
        document.getElementById('modalPackagePrice').textContent = '$' + packagePrice;
        document.getElementById('modalPackageDuration').textContent = packageDuration ? packageDuration : '';
        // Update form action with correct package id
        let form = document.getElementById('modalBuyForm');
        form.action = "{{ route('user.package.buy', ':id') }}".replace(':id', packageId);
        // Reset payment method
        document.getElementById('paymentMethod').selectedIndex = 0;
        var buyModal = new bootstrap.Modal(document.getElementById('buyModal'));
        buyModal.show();
    }
</script>
@endpush