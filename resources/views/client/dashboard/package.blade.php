@push('styles')
    <style>
        .package-card {
            background-color: #f9f4f8;
        }
        .buy-button {
            background-color: #4a4849;
            color: white
        }
        .buy-button:hover {
            background-color: #343233;
            color: white
        }

        @media screen and (max-width: 767px) {
            /* Wallet card adjustments */
            .package-card p.fs-5, .package-card button {
                font-size: 1rem;
            }
        }
    </style>
@endpush

<div id="package">
    <b class="text-uppercase">Package</b>
    <div class="row mt-2">
        @foreach ($packages as $package)
        <div class="col-lg-4 col-12 col-md-6">
            <div class="card package-card p-4">
            <div class="card-body">
                    <p class="fs-3 mb-4">{{ $package->name }}</p>
                    <div class="mb-1 mt-1">
                        <p>{{ $package->description }}</p>
                        <p class="fs-5">{{ $package?->price ? get_price($package->price) : "N/A" }}</p>
                    </div>
                    <button 
                        class="btn buy-button text-uppercase openBuyModal" 
                        data-id="{{ $package->id }}">
                        Buy package
                    </button>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>