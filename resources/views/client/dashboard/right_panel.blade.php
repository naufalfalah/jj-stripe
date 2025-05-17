@push('styles')
    <style>
        /* Wallet Section */
        .wallet-card {
            background-color: #f9f4f8;
        }
        .topup-button {
            background-color: #4a4849;
            color: white
        }
        .topup-button:hover {
            background-color: #343233;
            color: white
        }
        .text-maroon {
            color: #9f5c80
        }
        a.text-maroon:hover {
            color: #58213e
        }

        /* Round Robin Section */
        .round-robin-button {
            background: none;
            border: none;
            padding: 0;
            margin: 0;
            cursor: pointer;
            outline: none;
        }

        .round-robin-button:focus {
            outline: none; /* Hilangkan outline saat tombol di klik */
        }
        .participant-card {
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background-color: #f7f8fa;
        }
        .participant {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 15px;
        }
        .participant img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }
        .participant-info {
            flex-grow: 1;
            margin-left: 15px;
        }
        .participant-info h6 {
            margin: 0;
            font-weight: bold;
        }
        .participant-info p {
            margin: 0;
            color: gray;
            font-size: 0.9rem;
        }
        .participant-price {
            background-color: #d1e7dd;
            color: #529a86;
            padding: 5px 10px;
            border-radius: 5px;
            font-weight: bold;
        }

        @media screen and (max-width: 767px) {
            /* Wallet card adjustments */
            .wallet-card p.fs-5, .wallet-card button {
                font-size: 1rem;
            }
        }
    </style>
@endpush

{{-- Righ Pane --}}
<div class="col-lg-4">
    <div id="wallet">
        <b class="text-uppercase">Wallet</b>
        <div class="card wallet-card mt-2">
            <div class="card-body">
                <p class="fs-3 mb-4">{{ get_price($main_wallet_bls) }}</p>
                <button class="btn topup-button text-uppercase">Top up money</button>
                <div class="mt-3">
                    <div class="d-flex justify-content-between">
                        <p class="fs-5">Balance funds</p>
                        <p class="fs-5">{{ get_price($main_wallet_bls) }}</p>
                    </div>
                    <div class="d-flex justify-content-between">
                        <p class="fs-5">Last top up funds</p>
                        <p class="fs-5">@if(count($wallet_topups)) {{ get_price($wallet_topups[0]->amount_in) }} @endif</p>
                    </div>
                    <div class="d-flex justify-content-between">
                        <p class="fs-5">Last top up date</p>
                        <p class="fs-5">{{ get_date(@$last_transaction_date) }} {{ get_time(@$last_transaction_date) }}</p>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('user.wallet.transfer_funds') }}" class="fs-5 text-maroon">See all top ups</a>
                        <p class="fs-5 text-maroon"><i class="bi bi-info-circle-fill"></i>&nbsp;Wallet dispute</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="project">
        <b class="text-uppercase">Project Round Robin</b>
        <div class="card participant-card mt-2">
            <div class="d-flex justify-content-between">
                <h5 class="mb-4">Total Participants</h5>
                <div class="dropdown">
                    <button class="btn round-robbin-button" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-three-dots"></i>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                        <li><a class="dropdown-item" href="#">Action</a></li>
                    </ul>
                </div>
            </div>
            @foreach($projects as $project)
                <div class="participant">
                    <img src="https://via.placeholder.com/50" alt="Participant Image">
                    <div class="participant-info">
                        <h6>{{ $project->client->client_name }}</h6>
                        <p>{{ $project->created_at->format('d-m-Y H:i:s') }}</p>
                    </div>
                    <span class="participant-price">{{ $project?->package?->price ? get_price($project->package->price) : "N/A" }}</span>
                </div>
            @endforeach
        </div>
    </div>

    <div id="valuation">
        <b class="text-uppercase">Valuation Round Robin</b>
        <div class="card participant-card mt-2">
            <div class="d-flex justify-content-between">
                <h5 class="mb-4">Total Participants</h5>
                <div class="dropdown">
                    <button class="btn round-robbin-button" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-three-dots"></i>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                        <li><a class="dropdown-item" href="#">Action</a></li>
                    </ul>
                </div>
            </div>
            @foreach($valuations as $valuation)
                <div class="participant">
                    <img src="https://via.placeholder.com/50" alt="Participant Image">
                    <div class="participant-info">
                        <h6>{{ $valuation->client->client_name }}</h6>
                        <p>{{ $valuation->created_at->format('d-m-Y H:i:s') }}</p>
                    </div>
                    <span class="participant-price">{{ $valuation?->package?->price ? get_price($valuation->package->price) : "N/A" }}</span>
                </div>
            @endforeach
        </div>
    </div>
</div>