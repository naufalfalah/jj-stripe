@push('styles')
    <style>
        /* Account section */
        #date-filter {
            min-width: 150px;
        }
        .create-account-button {
            background-color: #e8da79;
            margin-top: 10px;
        }
        .account-table-filter-item {
            width: 20%;
        }
        .dot {
            height: 10px;
            width: 10px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 5px;
        }

        .dot.green { background-color: #28a745; }
        .dot.red { background-color: #dc3545; }
        .dot.orange { background-color: #ffc107; }

        .create-account-button {
            background-color: #f0c040; /* Sesuaikan warna tombol */
            color: #fff;
            border: none;
        }

        .table tbody tr td {
            vertical-align: middle;
        }

        .badge {
            font-size: 0.8rem;
            padding: 0.2rem 0.5rem;
        }

        .btn-outline-secondary.btn-sm {
            font-size: 0.8rem;
            padding: 0.2rem 0.5rem;
            color: #c17fa5;
            border: 0px;
        }
        .btn-outline-secondary.btn-sm:hover {
            background-color: transparent;
            color: #945279;
        }
        .table-striped tbody tr:nth-child(odd) {
            background-color: #f7f8fa !important;
        }
        .table-striped tbody tr:nth-child(even) {
            background-color: #f9f4f8;
        }

        @media screen and (max-width: 767px) {
            /* Table adjustments */
            .account-table-header {
                flex-direction: column;
                gap: 5px;
            }
            .account-table-filter {
                width: 100%;
                gap: 5px;
                display: flex;
                flex-direction: column;
                flex-wrap: wrap;
            }
            .create-account-button {
                width: 100%;
            }
            .table-responsive {
                overflow-x: auto;
            }
            .filter-label {
                color: #38538a !important;
            }
            .filter-input {
                background-color: #fcfcfc !important;
            }
            
            /* Form adjustments */
            #account-table .d-flex.gap-3 > div {
                width: 100%;
                margin-top: 10px;
            }
        }
    </style>
@endpush

<div id="account-table">
    <div class="mb-3 d-flex justify-content-between align-items-center account-table-header">
        <div class="d-flex gap-3 account-table-filter">
            <div class="account-table-filter-item">
                <label class="d-block filter-label">Currency</label>
                <select class="form-select form-select-sm filter-input" name="" id="">
                    <option value="">All</option>
                </select>
            </div>
            <div class="account-table-filter-item">
                <label class="d-block filter-label">Balance</label>
                <select class="form-select form-select-sm filter-input" name="" id="">
                    <option value="">Any</option>
                </select>
            </div>
            <div class="account-table-filter-item">
                <label class="d-block filter-label">Status</label>
                <select class="form-select form-select-sm filter-input" name="" id="">
                    <option value="">Any</option>
                </select>
            </div>
            <div class="account-table-filter-item" id="date-filter">
                <label class="d-block filter-label">Last 7 Days</label>
                <select class="form-select form-select-sm filter-input" name="" id="">
                    <option value="">16.10 - 22.10.24</option>
                </select>
            </div>
            <div class="account-table-filter-item">
                <label class="d-block filter-label">Search</label>
                <input type="text" class="form-control form-control-sm filter-input" placeholder="Search">
            </div>
        </div>
        <button class="btn btn-warning btn-sm text-dark create-account-button"><i class="bi bi-plus"></i>&nbsp;Create Account</button>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm radius-10 w-100">
                <div class="card-body p-0">
                    <div class="table-responsive mt-2">
                        <table class="table align-middle mb-0 table-striped">
                            <thead class="table-light">
                                <tr>
                                    <th class="fs-6">Account&nbsp;<i class="bi bi-question-circle"></i></th>
                                    <th class="fs-6">Policy Status&nbsp;<i class="bi bi-question-circle"></i></th>
                                    <th class="fs-6">Spend&nbsp;<i class="bi bi-question-circle"></i></th>
                                    <th class="fs-6">Available Balance&nbsp;<i class="bi bi-question-circle"></i></th>
                                    <th class="fs-6">Balance Depletion&nbsp;<i class="bi bi-question-circle"></i></th>
                                    <th class="fs-6">Edit Balance&nbsp;<i class="bi bi-question-circle"></i></th>
                                    <th class="fs-6">User Management&nbsp;<i class="bi bi-question-circle"></i></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($accounts as $account)
                                    <tr>
                                        <td class="d-flex">
                                            <span class="dot green m-2"></span>
                                            <div class="">
                                                {{ $account->client->client_name }}<br><small>{{ $account->client->phone_number }}</small>
                                            </div>
                                        </td>
                                        <td>Approved</td>
                                        <td>{{ $account?->package?->price ? get_price($account->package->price) : "N/A" }}</td>
                                        <td>{{ $account?->package?->price ? get_price($account->package->price) : "N/A" }}</td>
                                        <td>
                                            <span>
                                                @if (5 < 30)
                                                    <i class="bi bi-battery-full text-success"></i>
                                                @elseif (1 < 5)
                                                    <i class="bi bi-battery-half text-warning"></i>
                                                @else
                                                    <i class="bi bi-battery text-danger"></i> 
                                                @endif
                                                Days
                                            </span>
                                        </td>
                                        <td><button class="btn btn-outline-secondary btn-sm"><i class="bi bi-plus-circle"></i>&nbsp;Add</button>
                                            <button class="btn btn-outline-secondary btn-sm"><i class="bi bi-patch-minus"></i>&nbsp;Reduce</button>
                                        </td>
                                        </td>
                                        <td><button class="btn btn-outline-secondary btn-sm"><i class="bi bi-plus-circle"></i>&nbsp;Add</button>
                                            <button class="btn btn-outline-secondary btn-sm"><i class="bi bi-patch-minus"></i>&nbsp;Remove</button>
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