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

        <div class="row">
            @foreach ($packages as $package)
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">{{ $package->name }}</h5>
                            <div class="text-center">
                                <img class="w-50" src="{{ asset($package->logo) }}" alt="{{ $package->name }}" class="img-fluid mb-3">
                            </div>
                            <h6 class="card-subtitle mb-2 text-muted">
                                ${{ number_format($package->price, 2, '.', ',') }}
                            </h6>
                            <p class="card-text">
                                <strong>Duration:</strong> {{ $package->duration }} month{{ $package->duration > 1 ? 's' : ''}}<br>
                            </p>
                            @if($package->status)
                                <button type="button" class="btn btn-primary w-100" 
                                    onclick="showBuyModal({{ $package->id }}, '{{ addslashes($package->name) }}', '{{ number_format($package->price, 2, '.', ',') }}')">
                                    Buy
                                </button>
                                <form id="buy-form-{{ $package->id }}" action="{{ route('user.package.buy', $package->id) }}" method="POST" style="display:none;">
                                    @csrf
                                </form>
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
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="buyModalLabel">Confirm Purchase</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="modalBuyForm" action="{{ route('user.package.buy', 0) }}" method="POST">
                    @csrf
                    <input type="hidden" name="package_id" id="modalPackageId" value="">
                    <div class="modal-body">
                        Are you sure you want to buy <span id="modalPackageName"></span> for <span id="modalPackagePrice"></span>?
                    </div>
                    <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="confirmBuyBtn">Yes, Buy</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    let selectedPackageId = null;

    function showBuyModal(packageId, packageName, packagePrice) {
        document.getElementById('modalPackageId').value = packageId;
        document.getElementById('modalPackageName').textContent = packageName;
        document.getElementById('modalPackagePrice').textContent = '$' + packagePrice;
        // Update form action with correct package id
        let form = document.getElementById('modalBuyForm');
        form.action = "{{ route('user.package.buy', ':id') }}".replace(':id', packageId);
        var buyModal = new bootstrap.Modal(document.getElementById('buyModal'));
        buyModal.show();
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('confirmBuyBtn').addEventListener('click', function () {
            if (selectedPackageId) {
                document.getElementById('buy-form-' + selectedPackageId).submit();
            }
        });
    });
</script>
@endpush