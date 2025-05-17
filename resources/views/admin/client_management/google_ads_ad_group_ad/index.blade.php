
@extends('layouts.admin')

@section('content')
    <div class="card radius-15">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12 border-right">
                    <div class="d-md-flex align-items-center">
                        <div class="flex-grow-1">
                            <h5 class="mb-2">Google Ads Ad Group Ad</h5>
                        </div>
                        @if ($client->google_account_id)
                            <div>
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#syncAdModal">Sync Ad</button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <!--end row-->

            @if (!$client->google_account_id)
                <div class="alert border-0 border-danger border-start border-4 bg-light-danger alert-dismissible fade show py-2 my-2" id="act_expire_alert">
                    <div class="d-flex align-items-center">
                        <div class="fs-3 text-danger">
                            <i class="bi bi-x-circle-fill"></i>
                        </div>
                        <div class="ms-3">
                            <div class="text-danger">Google Ads account not connected.</div>
                        </div>
                    </div>
                </div>
            @else
                @if (session('success'))
                    <div class="alert alert-success my-2" role="alert">
                        {{ session('success') }}
                    </div>
                    <!-- <x-client_management.google_ads.website_conversion /> -->
                @elseif (session('error'))
                    <div class="alert alert-danger my-2" role="alert">
                        {{ session('error') }}
                    </div>
                @endif
                
                <div class="tab-content mt-3" id="page-1">
                    <div class="tab-pane fade show active" id="Edit-Profile">
                        <div class="card shadow-none border mb-0 radius-15">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="low_bls-template-table" class="table table-striped table-bordered" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th class="text-uppercase ps-2 text-secondary text-xxs font-weight-bolder opacity-7">
                                                    Name</th>
                                                <th class="text-uppercase ps-2 text-secondary text-xxs font-weight-bolder opacity-7">
                                                    Campaign</th>
                                                <th class="text-uppercase ps-2 text-secondary text-xxs font-weight-bolder opacity-7">
                                                    Ad Group</th>
                                                <th class="text-uppercase ps-2 text-secondary text-xxs font-weight-bolder opacity-7">
                                                    Status</th>
                                            </tr>
                                        </thead>
                                        <tbody id="ad_group_ad-table-body">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

     <!-- Modal Sync Ad -->
     <div class="modal fade" id="syncAdModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Sync Google Ads Ad</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.sub_account.client-management.google_ads.sync',  ['sub_account_id' => $sub_account_id]) }}" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="ad_id">Ad ID</label>
                            <input type="text" class="form-control" id="ad_id" name="ad_id" placeholder="Enter Ad ID" required>
                            <small>Example: 170420966092~706675170402</small>
                        </div>
                        <div class="form-group mt-2">
                            <label for="ad_request_id">Ads Request</label>
                            <select class="form-select" name="ad_request_id" required>
                                <option value="" selected>Select an ad request</option>
                                @foreach ($adsRequests as $adsRequest)
                                    <option value="{{ $adsRequest->id }}" 
                                        {{ old('ad_request_id') == $adsRequest->id ? 'selected' : '' }}>
                                        {{ $adsRequest->adds_title }} - {{ $adsRequest->client->client_name }} - {{ $adsRequest->client->customer_id }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Sync</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const clientId = `{{ $client->id ?? null }}`;
        
        function capitalize(str) {
            return str.charAt(0).toUpperCase() + str.slice(1);
        }

        function getAdGroupAds() {
            $('#ad_group_ad-table-body').empty()

            if (clientId) {
                $.ajax({
                    url: "{{ route('google_ads.ad_group_ad') }}",
                    method: 'GET',
                    data: {
                        client_id: clientId,
                    },
                    success: function(data) {
                        if (data.results) {
                            for (const adGroupAd of data.results) {
                                $('#ad_group_ad-table-body').append(`
                                    <tr>
                                        <td>${adGroupAd.adGroupAd.name}</td>
                                        <td>${adGroupAd.campaign.name}</td>
                                        <td>${adGroupAd.adGroup.name}</td>
                                        <td>
                                            <span class="badge ${adGroupAd.adGroupAd.status === 'ENABLED' ? 'bg-info text-dark' : 'bg-secondary text-white'}">
                                                ${capitalize(adGroupAd.adGroupAd.status)}
                                            </span>
                                        </td>
                                    </tr>
                                `);
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                    }
                });
            }

        }

        $(document).ready(function() {
            getAdGroupAds();
        });
    </script>
@endpush