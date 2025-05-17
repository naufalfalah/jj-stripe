
@extends('layouts.admin')

@section('content')
    <div class="card radius-15">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 col-lg-6 border-right">
                    <div class="d-md-flex align-items-center">
                        <div class="ms-md-6 flex-grow-1">
                            <h5 class="mb-2">Google Ads Ad Group</h5>
                        </div>
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
                                                    Status</th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                    Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="ad_group-table-body">
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
@endsection

@push('scripts')
    <script>
        const clientId = `{{ $client->id ?? null }}`;

        function capitalize(str) {
            return str.charAt(0).toUpperCase() + str.slice(1);
        }

        function getAdGroups() {
            $('#ad_group-table-body').empty()

            if (clientId) {
                $.ajax({
                    url: "{{ route('google_ads.ad_group') }}",
                    method: 'GET',
                    data: {
                        client_id: clientId,
                    },
                    success: function(data) {
                        if (data.results) {
                            for (const adGroup of data.results) {
                                $('#ad_group-table-body').append(`
                                    <tr>
                                        <td>${adGroup.adGroup.name}</td>
                                        <td>${adGroup.campaign.name}</td>
                                        <td>
                                            <span class="badge ${adGroup.adGroup.status === 'ENABLED' ? 'bg-info text-dark' : 'bg-secondary text-white'}">
                                                ${capitalize(adGroup.adGroup.status)}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="table-actions d-flex align-items-center gap-3 fs-6">
                                                <a href="{{ route('admin.sub_account.client-management.google_ads_ad_group.show', ['sub_account_id' => $sub_account_id]) }}?client_id=${clientId}&ad_group_resource_name=${adGroup.adGroup.resourceName}" class="text-info" data-bs-toggle="tooltip" data-bs-placement="bottom" aria-label="Show"><i class="bi bi-eye-fill"></i></a>
                                            </div>
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
            getAdGroups();
        });
    </script>
@endpush