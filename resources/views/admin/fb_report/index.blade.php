@extends('layouts.admin')
@section('page-css')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link href="{{ asset('front') }}/assets/plugins/datatable/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
<link href="{{ asset('front') }}/assets/plugins/highcharts/css/dark-unica.css" rel="stylesheet" />

<link rel="stylesheet" href="{{ asset('front') }}/assets/plugins/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.min.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
<style>
    .loader-container {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.7);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 999;
        opacity: 0.7;
        transition: opacity 0.3s ease-in-out;
    }

    .loader {
        border: 8px solid #f1f1f1;
        border-top: 10px solid #39548A;
        border-radius: 50%;
        width: 80px;
        height: 80px;
        animation: spin 2s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    .apexcharts-menu-icon {
        display: none;
    }

    #ctr-chart {
        background-color: white !important;
    }
</style>
@endsection
@section('content')
<div class="loader-container" id="loader-container" style="display: none;">
    <div class="loader"></div>
</div>

<div class="alert border-0 border-danger border-start border-4 bg-light-danger alert-dismissible fade show py-2" id="act_expire_alert" style="display: none;">
    <div class="d-flex align-items-center">
      <div class="fs-3 text-danger"><i class="bi bi-x-circle-fill"></i>
      </div>
      <div class="ms-3">
        <div class="text-danger">Facebook Ad account has expired.</div>
      </div>
    </div>
    {{-- <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button> --}}
</div>

<div class="row">
    <div class="col-4">
        <a href="javascript:void(0);" class="text-dark" data-bs-toggle="tooltip" data-bs-placement="top"
            title="Last Updated: {{ $last_updated }} ({{ $last_updated_date }})">
            <i class="fa-regular fa-clock"></i> {{ $last_updated }}
        </a>
    </div>
    
    <div class="col-8">

        @if ($check_access_token > 0)
        <a href="javascript:void(0);" class="btn btn-primary float-end mb-2"
            id="generate_pdf_btn" data-bs-toggle="tooltip" data-bs-placement="top" title="Generate PDF">
            <i class="fa-solid fa-file-pdf"></i>
        </a>
        <a href="javacript:void(0);" id="refresh_fb_report" class="btn btn-primary float-end me-1 mb-2"
            data-bs-toggle="tooltip" data-bs-placement="top"  title="Refresh Facebook Ads Data">
            <i class="fa-solid fa-rotate"></i>
        </a>

        <a href="javacript:void(0);" class="btn btn-primary float-end me-1 mb-2" id="date_range_btn"
            data-bs-toggle="tooltip" data-bs-placement="top" title="Date Range">
            <i class="fa-regular fa-calendar-days me-1"></i>
        </a>

        <a href="javacript:void(0);" class="btn btn-primary float-end me-1 mb-2" id="campaignNotesBtn"
            data-bs-toggle="tooltip" data-bs-placement="top" title="Campaign Notes">
            <i class="fa-solid fa-file-pen"></i>
        </a>

        <a href="javascript:void(0);" id="disconnect_fb_account" class="btn btn-danger float-end me-1 mb-2"
            data-bs-toggle="tooltip" data-bs-placement="top" title="Disconnect Facebook Account">
            <span class="default-text">
                <i class="fa-solid fa-user-slash me-1"></i>
            </span>
        </a>
        @else
        <a href="{{ route('admin.facebook-ads-report.fb_connect') }}" class="btn btn-primary float-end me-1 mb-2"
            data-bs-toggle="tooltip" data-bs-placement="top" title="Connect Facebook Account">
            <span class="default-text">
                <i class="fa-solid fa-user-plus me-1"></i>
            </span>
        </a>
        @endif


    </div>
</div>

<form method="post" action="{{ route('admin.facebook-ads-report.save_fb_report') }}" class="row g-3 ajaxFormAdAct">
    <div class="row">
        <div class="form-group col-md-6 my-2">
            <h6 class="mb-2 text-uppercase">Select Facebook Ad Account</h6>
            <input type="hidden" name="act_start_date" value="{{ $start_date ?? '' }}" id="act_start_date">
            <input type="hidden" name="act_end_date" value="{{ $end_date ?? '' }}" id="act_end_date">
            <select name="account_id" id="account_id" class="form-control mb-2 single-select dropup" required>
                <option value="" disabled selected>Select Facebook Ad Account</option>
                @foreach ($get_accounts as $val)
                <option value="{{ $val->act_id }}" {{ isset($acct_id) && $acct_id==$val->act_id ? 'selected' : '' }}
                    {{ request()->input('account_id') == $val->act_id ? 'selected' : '' }}>
                    {{ $val->act_name }} - {{ str_replace('act_', '', $val->act_id) }}
                </option>
                @endforeach
            </select>
        </div>

        @if ($get_accounts->count() > 0)
            <div class="form-group col-md-6 my-2">
                <h6 class="mb-2 text-uppercase">Set Account Expiry Date</h6>
                <input class="result form-control" type="text" id="expiry_date" value="{{ $get_facebook_ads_account->account_expiry_date ?? "" }}" placeholder="Select Account Expiry Date">
            </div>
        @endif
        
    </div>
</form>

<div class="row row-cols-1 row-cols-md-4 row-cols-lg-4 row-cols-xxl-4">

    <div class="col">
        <div class="card radius-10">
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <h4 class="mb-0 text-primary">{{ isset($summary_detail->summary->clicks) ? number_with_suffixes($summary_detail->summary->clicks) : '0' }}</h4>
                        <p class="mb-1">Clicks <span style="font-size: 13px;" class="text-primary"
                            data-bs-toggle="tooltip" data-bs-placement="top"
                            title="The number of clicks on an ad."><i class="fa-solid fa-circle-info"></i></span>
                        </p>
                    </div>
                    <div class="col-12 ms-auto">
                        <div id="chart5"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col">
        <div class="card radius-10">
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <h4 class="mb-0 text-primary">{{ isset($summary_detail->summary->impressions) ? number_with_suffixes($summary_detail->summary->impressions) : '0' }}</h4>
                        <p class="mb-1">Impressions <span style="font-size: 13px;" class="text-primary"
                                data-bs-toggle="tooltip" data-bs-placement="top"
                                title="The number of times an ad was displayed."><i
                                    class="fa-solid fa-circle-info"></i></span></p>
                    </div>
                    <div class="col-12 ms-auto">
                        <div id="chart6"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col">
        <div class="card radius-10">
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        @if (isset($summary_detail->summary->ctr) && !empty($summary_detail->summary->ctr))
                        <h4 class="mb-0 text-primary">{{ $summary_detail->summary->ctr ?? '0' }}%</h4>
                        @else
                        <h4 class="mb-0 text-primary">0%</h4>
                        @endif
                        <p class="mb-1">CTR <span style="font-size: 13px;" class="text-primary" data-bs-toggle="tooltip"
                                data-bs-placement="top"
                                title="CTR (click-through rate) measures how many times users clicked on an ad (clicks compared to impressions)."><i
                                    class="fa-solid fa-circle-info"></i></span></p>
                    </div>
                    <div class="col-12 ms-auto">
                        <div id="chart7"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col">
        <div class="card radius-10">
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        @if (isset($summary_detail->summary->cpc) && !empty($summary_detail->summary->cpc))
                        <h4 class="mb-0 text-primary">{{ round($summary_detail->summary->cpc, 1) ?? '0' }}</h4>
                        @else
                        <h4 class="mb-0 text-primary">0</h4>
                        @endif
                        <p class="mb-1">CPC <span style="font-size: 13px;" class="text-primary" data-bs-toggle="tooltip"
                                data-bs-placement="top" title="CPC is the average cost paid for each click on an ad."><i
                                    class="fa-solid fa-circle-info"></i></span></p>
                    </div>
                    <div class="col-12 ms-auto">
                        <div id="chart8"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="alert border-0 bg-light-success alert-dismissible fade show copy_campaign" role="alert" style="display: none;">
        URL copied to clipboard!
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
</div>


<div class="row">
    <div class="col-12 col-xl-12">
        <div class="card shadow radius-10 w-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Campaigns Performance <span style="font-size: 13px;" class="text-primary"
                            data-bs-toggle="tooltip" data-bs-placement="top"
                            title="A series of ad sets and ads that aim to accomplish a single objective."><i
                                class="fa-solid fa-circle-info"></i></span></h6>
                </div>
                <div class="table-responsive mt-2">
                    <table id="campaign-performance-data-table" class="table table-striped align-middle table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Campaign</th>
                                <th>Total Leads</th>
                                <th>Cost Per Lead <span class="text-primary" style="font-size: 10px;">(SGD)</th>
                                <th>Daily Budget <span class="text-primary" style="font-size: 10px;">(SGD)</span></th>
                                <th>LifeTime Budget <span class="text-primary" style="font-size: 10px;">(SGD)</span></th>
                                <th>Campaign Notes</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>

                            @if (isset($campaigns_with_notes) && !empty($campaigns_with_notes))
                                @foreach ($campaigns_with_notes as $campaign)
                                    @php
                                        $copy_campaign_data = "";
                                        $copy_campaign_data = "Campaign name: {$campaign['name']}";

                                        $copy_campaign_data .= "\nAds performance\n";
                                        $copy_campaign_data .= "- Total leads: " . number_format($campaign['total_leads']) . "\n";
                                        $copy_campaign_data .= "- Daily budget: $" . number_format($campaign['daily_budget']) . "\n";
                                        $copy_campaign_data .= "- Lifetime budget: $" . number_format($campaign['lifetime_budget']) . "\n";
                                        $copy_campaign_data .= "- CPL: $" . number_format($campaign['cost_per_lead']) . "\n";
                                        if (isset($campaign['campaign_notes']) && !empty($campaign['campaign_notes'])){
                                            $copy_campaign_data .= "Notes:\n";
                                            foreach ($campaign['campaign_notes'] as $key => $note) {
                                                $copy_campaign_data .= "- {$note}\n";
                                            }
                                        }
                                    @endphp
                                    <tr>
                                        <td>{!! wordwrap($campaign['name'], 30, '<br>', true) ?? '...' !!}</td>
                                        <td>{{ number_format($campaign['total_leads']) ?? '0' }}</td>
                                        <td>${{ number_format($campaign['cost_per_lead']) ?? '0' }}</td>
                                        <td>${{ $campaign['daily_budget'] ?? "0" }}</td>
                                        <td>${{ $campaign['lifetime_budget'] ?? "0" }}</td>
                                        <td>
                                            @if (isset($campaign['campaign_notes']) && !empty($campaign['campaign_notes']))
                                                @foreach ($campaign['campaign_notes'] as $note)

                                                        <a href="javascript:void(0);" class="text-dark" data-bs-toggle="tooltip" data-bs-placement="top"
                                                            title="{{ $note }}">
                                                            {{ Str::limit($note, 20, "...") }}<br>
                                                        </a>
                                                @endforeach
                                            @else
                                                <a href="javascript:void(0);" class="text-dark">
                                                    No Data Found
                                                </a>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="javascript:void(0)" class="text-success copy-button fs-5" data-clipboard-text="{{ $copy_campaign_data }}" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Copy Campaign Text">
                                                <i class="fadeIn animated bx bx-copy"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-12 mx-auto">
        <div class="card">
            <div class="card-body">
                <h6 class="mb-0 text-uppercase">Summary <span style="font-size: 13px;" class="text-primary"
                        data-bs-toggle="tooltip" data-bs-placement="top"
                        title="Collection of data that reflects a campaign’s performance."><i
                            class="fa-solid fa-circle-info"></i></span></h6>
                <div id="summaryChart"></div>
            </div>
        </div>
    </div>
</div>
<!--end row-->

<div class="row">
    <div class="col-xl-6 mx-auto">
        <div class="card">
            <div class="card-body">
                <h6 class="mb-0 text-uppercase">Clicks <span style="font-size: 13px;" class="text-primary"
                        data-bs-toggle="tooltip" data-bs-placement="top"
                        title="The number of times that an ad was clicked based on data received from an ad platform."><i
                            class="fa-solid fa-circle-info"></i></span></h6>
                <p>Platform</p>
                <div id="clicks"></div>
            </div>
        </div>
    </div>

    <div class="col-xl-6 mx-auto">
        <div class="card">
            <div class="card-body">
                <h6 class="mb-0 text-uppercase">Impressions <span style="font-size: 13px;" class="text-primary"
                        data-bs-toggle="tooltip" data-bs-placement="top"
                        title="The number of times an ad was served/displayed, based on data received from an ad platform."><i
                            class="fa-solid fa-circle-info"></i></span></h6>
                <p>Platform</p>
                <div id="impressions"></div>
            </div>
        </div>
    </div>
</div>
<!--end row-->

<div class="row">
    <div class="col-xl-6 mx-auto">
        <div class="card">
            <div class="card-body">
                <h6 class="mb-0 text-uppercase">CTR <span style="font-size: 13px;" class="text-primary"
                        data-bs-toggle="tooltip" data-bs-placement="top"
                        title="The percentage of clicks there were out of the total number of impressions from an ad platform."><i
                            class="fa-solid fa-circle-info"></i></span></h6>
                <p>Platform</p>
                <div id="ctr-chart"></div>
            </div>
        </div>
    </div>

    <div class="col-xl-6 mx-auto">
        <div class="card">
            <div class="card-body">
                <h6 class="mb-0 text-uppercase">CPC <span style="font-size: 13px;" class="text-primary"
                        data-bs-toggle="tooltip" data-bs-placement="top"
                        title="The average cost paid for each click on an ad."><i
                            class="fa-solid fa-circle-info"></i></span></h6>
                <p>Platform</p>
                <div id="cpc-chart"></div>
            </div>
        </div>
    </div>
</div>
<!--end row-->

<div class="row">
    <div class="col-12 col-xl-12">
        <div class="card shadow radius-10 w-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Campaigns <span style="font-size: 13px;" class="text-primary"
                            data-bs-toggle="tooltip" data-bs-placement="top"
                            title="A series of ad sets and ads that aim to accomplish a single objective."><i
                                class="fa-solid fa-circle-info"></i></span></h6>
                </div>
                <div class="table-responsive mt-2">
                    <table id="campaign-data-table" class="table table-striped align-middle table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Campaign</th>
                                <th>Clicks</th>
                                <th>Impressions</th>
                                <th>CTR</th>
                                <th>CPC <span class="text-primary" style="font-size: 10px;">(SGD)</span></th>
                                <th>Amount<br> Spent <span class="text-primary" style="font-size: 10px;">(SGD)</span>
                                </th>
                                <th>Cost Per<br> Result <span class="text-primary" style="font-size: 10px;">(SGD)</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody id="campaign-table">
                            @if (is_array($campaigns) && count($campaigns) > 0)
                            @foreach ($campaigns as $campaign)
                            @if (isset($campaign->insights) && isset($campaign->insights->data[0]))
                            <tr>
                                <td>{!! wordwrap($campaign->name, 30, '<br>', true) ?? '...' !!}</td>
                                <td>{{ $campaign->insights->data[0]->clicks ?? '...' }}</td>
                                <td>{{ $campaign->insights->data[0]->impressions ?? '...' }}</td>
                                <td>{{ isset($campaign->insights->data[0]->ctr) ? round($campaign->insights->data[0]->ctr, 2) . '%' : '0%' }}</td>
                                <td>{{ isset($campaign->insights->data[0]->cpc) ? round($campaign->insights->data[0]->cpc, 2) : '...' }}</td>
                                <td>{{ isset($campaign->insights->data[0]->spend) ? round($campaign->insights->data[0]->spend) : '...' }}</td>
                                <td>{{ isset($campaign->insights->data[0]->cost_per_action_type[3]->value) ? round($campaign->insights->data[0]->cost_per_action_type[3]->value, 2) : '...' }}</td>
                            </tr>
                            @endif
                            @endforeach
                            @endif

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 col-xl-12">
        <div class="card shadow radius-10 w-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Ad Sets <span style="font-size: 13px;" class="text-primary"
                            data-bs-toggle="tooltip" data-bs-placement="top"
                            title="Ad Sets are groups of ads that share settings for how, when and where to run."><i
                                class="fa-solid fa-circle-info"></i></span></h6>
                </div>
                <div class="table-responsive mt-2">
                    <table id="adsets-data-table" class="table table-striped align-middle table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Ad Set</th>
                                <th>Campaign</th>
                                <th>Clicks</th>
                                <th>Impressions</th>
                                <th>CTR</th>
                                <th>CPC <span class="text-primary" style="font-size: 10px;">(SGD)</span></th>
                                <th>Amount<br> Spent <span class="text-primary" style="font-size: 10px;">(SGD)</span>
                                </th>
                                <th>Cost Per<br> Result <span class="text-primary" style="font-size: 10px;">(SGD)</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody id="adsets-table">
                            @if (is_array($adsets) && count($adsets) > 0)
                            @foreach ($adsets as $item)
                            <tr>
                                <td>{!! wordwrap($item->adset_name, 22, '<br>', true) ?? '...' !!}</td>
                                <td>{!! wordwrap($item->campaign_name, 22, '<br>', true) ?? '...' !!}</td>
                                <td>{{ $item->clicks ?? '...' }}</td>
                                <td>{{ $item->impressions ?? '...' }}</td>
                                <td>{{ isset($item->ctr) ? round($item->ctr, 2) . '%' : '0%' }}</td>
                                <td>{{ isset($item->cpc) ? round($item->cpc, 2) : '...' }}</td>
                                <td>{{ isset($item->spend) ? round($item->spend) : '...' }}</td>
                                <td>{{ isset($item->cost_per_action_type[3]->value) ? round($item->cost_per_action_type[3]->value) : '...' }}</td>
                            </tr>
                            @endforeach
                            @endif

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 col-xl-12">
        <div class="card shadow radius-10 w-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Ads <span style="font-size: 13px;" class="text-primary" data-bs-toggle="tooltip"
                            data-bs-placement="top"
                            title="Ad Sets are groups of ads that share settings for how, when and where to run."><i
                                class="fa-solid fa-circle-info"></i></span></h6>
                </div>
                <div class="table-responsive mt-2">
                    <table id="ads-data-table" class="table align-middle table-striped table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Ads</th>
                                <th>Ad Set</th>
                                <th>Campaign</th>
                                <th>Clicks</th>
                                <th>Impressions</th>
                                <th>CTR</th>
                                <th>CPC <span class="text-primary" style="font-size: 10px;">(SGD)</span></th>
                                <th>Amount <br> Spent <span class="text-primary" style="font-size: 10px;">(SGD)</span>
                                </th>
                                <th>Cost Per <br> Result <span class="text-primary"
                                        style="font-size: 10px;">(SGD)</span></th>
                            </tr>
                        </thead>
                        <tbody id="ad-table">
                            @if (is_array($ads) && count($ads) > 0)
                            @foreach ($ads as $item)
                            <tr>
                                <td><img src="{{ asset('front') }}/assets/images/noimage.jpg" width="40px" height="40px"
                                        alt="">
                                    &nbsp;&nbsp;&nbsp;{!! wordwrap($item->ad_name, 10, '<br>', true) ?? '...' !!}</td>
                                <td>{!! wordwrap($item->adset_name, 12, '<br>', true) ?? '...' !!}</td>
                                <td>{!! wordwrap($item->campaign_name, 12, '<br>', true) ?? '...' !!}</td>
                                <td>{{ $item->clicks ?? '...' }}</td>
                                <td>{{ $item->impressions ?? '...' }}</td>
                                <td>{{ isset($item->ctr) ? round($item->ctr, 2) . '%' : '0%' }}</td>
                                <td>{{ isset($item->cpc) ? round($item->cpc, 2) : '...' }}</td>
                                <td>{{ isset($item->spend) ? round($item->spend) : '...' }}</td>
                                <td>{{ isset($item->cost_per_action_type[3]->value) ? round($item->cost_per_action_type[3]->value) : '...' }}</td>
                            </tr>
                            @endforeach
                            @endif

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-12 mx-auto">
        <div class="card">
            <div class="card-body">
                <h6 class="mb-0 text-uppercase">Demographics <span style="font-size: 13px;" class="text-primary"
                        data-bs-toggle="tooltip" data-bs-placement="top"
                        title="Choose your audience based on age, gender, education, job title and more. You can keep track of the types of people your ads are reaching, but Facebook will never share personally-identifiable information about them."><i
                            class="fa-solid fa-circle-info"></i></span></h6>
                <div class="row">
                    <div class="col-6 mx-auto">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="mb-0 text-uppercase">Gender <span style="font-size: 13px;"
                                        class="text-primary" data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="title: Age and the gender of people you reach with your Facebook ad. People who don't list their gender are shown as not specified/unknown when you review your ad's performance."><i
                                            class="fa-solid fa-circle-info"></i></span></h6>
                                <div id="gender-chart"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 mx-auto">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="mb-0 text-uppercase">Age <span style="font-size: 13px;" class="text-primary"
                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="The maximum age on Meta technologies is 65+, which reaches all people over 65. The minimum age is 13, so all ads will be targeted only to people at least 13 years of age."><i
                                            class="fa-solid fa-circle-info"></i></span></h6>
                                <div id="age-chart"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<!--end row-->

<div class="row">
    <div class="col-12 col-xl-12">
        <div class="card shadow radius-10 w-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Country <span style="font-size: 13px;" class="text-primary"
                            data-bs-toggle="tooltip" data-bs-placement="top"
                            title="Countries are where the people you've reached are located."><i
                                class="fa-solid fa-circle-info"></i></span></h6>
                </div>
                <div class="table-responsive mt-2">
                    <table id="country-data-table" class="table align-middle table-striped table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Country</th>
                                <th>Clicks</th>
                                <th>Impressions</th>
                                <th>CTR</th>
                                <th>CPC <span class="text-primary" style="font-size: 10px;">(SGD)</span></th>
                                <th>Amount Spent <span class="text-primary" style="font-size: 10px;">(SGD)</span></th>
                                <th>Cost Per Result <span class="text-primary" style="font-size: 10px;">(SGD)</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody id="country">
                            @if (!empty($summary_detail->summary->clicks))
                                <tr>
                                    <td><img src="{{ asset('front') }}/assets/images/sg.png" width="20px" height="20px"
                                            alt="">
                                        &nbsp;&nbsp;&nbsp;Singapore</td>
                                    <td>{{ $summary_detail->summary->clicks ?? '...' }}</td>
                                    <td>{{ $summary_detail->summary->impressions ?? '...' }}</td>
                                    <td>{{ $summary_detail->summary->ctr ?? '0' }}%</td>
                                    <td>{{ $summary_detail->summary->cpc ?? '...' }}</td>
                                    <td>{{ $summary_detail->summary->spend ?? '...' }}</td>
                                    <td>{{ $summary_detail->summary->cost_per_result ?? '...' }}</td>
                                </tr>
                            @endif

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- campaign notes modal -->

<div class="modal fade" id="campaignNotesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Campaign Notes</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">

                <form action="{{ route('admin.facebook-ads-report.campaign_note_save') }}" method="POST"
                    class="saveNoteajaxForm">
                    @csrf
                    <div class="row">
                        <div class="col-md-12 col-lg-12 mb-2">
                            <label for="">Date</label>
                            <input type="date" class="form-control" name="note_date" id="note_date" required>
                        </div>
                        <div class="col-md-12 col-lg-12 mb-2">
                            <label for="">Campaign</label>
                            <select name="campaign" class="form-select modal-single-select" id="campaign_select">
                                <option value="">Select Campaign</option>
                                @if (is_array($campaigns) && count($campaigns) > 0)
                                @foreach (@$campaigns as $campaign)
                                <option value="{{ $campaign->name }}">{{ $campaign->name }}</option>
                                @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 mb-2">
                            <label for="">Note</label>
                            <textarea name="notes" id="notes" class="form-control" required
                                placeholder="Start typing your note..." cols="30" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="float-end btn btn-primary form-submit-btn mb-2">Save
                                Note</button>
                        </div>
                    </div>
                </form>

                @foreach ($campaign_notes as $note)
                    <div class="card mb-3" style="border: 1px solid #ced4da;">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <h6>{{ @$note->campaign_name }}</h6>
                                <span class="">
                                    <a href="javascript:void(0)" onclick="ajaxRequest(this)"
                                        data-url="{{ route('admin.facebook-ads-report.campaign_note_delete', @$note->hashid) }}"
                                        class="text-danger"><i class="fas fa-trash-alt"></i></a>
                                </span>
                            </div>
                            <p>{{ @$note->note }} <br>
                                <small>{{ @$note->note_date }}</small>
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
            {{-- <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div> --}}
        </div>
    </div>
</div>

<!-- date range modal -->
<div class="modal fade" id="dateRangeModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Date Range</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.facebook-ads-report.save_fb_report') }}" method="POST" class="date_range_ajaxForm">
                <div class="modal-body">
                    @csrf
                    <div class="row">
                        <div class="col-md-12 col-lg-12 mb-2">
                            <label for="">Select DateRange</label>
                            <input type="text" id="reportrange" name="daterange" required class="form-control" />
                            <input type="hidden" name="date_act_id" id="date_act_id">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    @if (!empty($acct_id))
                        <button type="button" class="btn btn-primary form-submit-btn mb-2 ajax_date_range_btn">Save</button>
                    @endif
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@section('page-scripts')
<script src="{{ asset('front') }}/assets/plugins/apexcharts-bundle/js/apexcharts.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="{{ asset('front') }}/assets/plugins/datatable/js/jquery.dataTables.min.js"></script>
<script src="{{ asset('front') }}/assets/plugins/datatable/js/dataTables.bootstrap5.min.js"></script>
<script src="{{ asset('front') }}/assets/js/table-datatable.js"></script>
<script src="{{ asset('front') }}/assets/plugins/highcharts/js/highcharts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.8/clipboard.min.js"></script>

<!-- date picker -->
<script src="{{ asset('front') }}/assets/plugins/datetimepicker/js/picker.date.js"></script>
<script src="{{ asset('front') }}/assets/plugins/bootstrap-material-datetimepicker/js/moment.min.js"></script>
<script src="{{ asset('front') }}/assets/plugins/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.min.js"></script>

<script>

    $(function() {
        "use strict";
        $('#expiry_date').bootstrapMaterialDatePicker({
            time: false,
            // minDate: new Date()
        });
    });

    $(document).ready(function() {

        var expiry_date = $('#expiry_date').val();
        var today = new Date();
        var dd = String(today.getDate()).padStart(2, '0');
        var mm = String(today.getMonth() + 1).padStart(2, '0');
        var yyyy = today.getFullYear();

        today = yyyy + '-' + mm + '-' + dd;

        if (expiry_date && (expiry_date === today || expiry_date < today)) {
            $("#act_expire_alert").show();
        }else{
            $("#act_expire_alert").hide();
        }
        

        charts();
        $('#campaign-data-table').DataTable();
        $('#campaign-performance-data-table').DataTable();
        $('#adsets-data-table').DataTable();
        $('#ads-data-table').DataTable();
        $('#country-data-table').DataTable();

        var start = $('#act_start_date').val();
        var end = $('#act_end_date').val();
        if (start == "" && end == "") {
            var start = moment().subtract(31, 'days').format("YYYY-MM-DD");
            var end = moment().subtract(1, 'days').format("YYYY-MM-DD");
            $('#act_start_date').val(start);
            $('#act_end_date').val(end);
        }
    });

    $(document).on('change', '#expiry_date', function () {
        var expiry_date = $('#expiry_date').val();
        var account_id = $('#account_id').val();
        var loader_id = "#loader-container";
        var data = {
                '_token' : "{{ csrf_token() }}",
                expiry_date : expiry_date,
                account_id : account_id,
                };

        update_expiry_date("{{ route('admin.facebook-ads-report.update_act_expiry_date') }}",data,'POST');

    });

    $(document).on('click', '#date_range_btn', function() {
        $(function() {
            var start = moment($('#act_start_date').val());
            var end = moment($('#act_end_date').val());

            var acct_id = $('#account_id').val();
            $('#date_act_id').val(acct_id);

            function cb(start, end) {
                $('#reportrange span').html(start.format('YYYY-MM-DD') + ' - ' + end.format(
                    'YYYY-MM-DD'));
                $('#act_start_date').val(start.format('YYYY-MM-DD'));
                $('#act_end_date').val(end.format('YYYY-MM-DD'));
            }

            $('#reportrange').daterangepicker({
                startDate: start,
                endDate: end,
                maxDate: moment(),
                ranges: {
                    'One Month': [moment().subtract(1, 'month'), moment()],
                    'Three Months': [moment().subtract(3, 'month'), moment()],
                    'Six Months': [moment().subtract(6, 'month'), moment()],
                    'Nine Months': [moment().subtract(9, 'month'), moment()],
                    'One Year': [moment().subtract(1, 'year'), moment()]
                }
            }, cb);

            cb(start, end);
        });

        $('#dateRangeModal').modal({
            backdrop: 'static',
            keyboard: false
        });
        $('#dateRangeModal').modal('show');
    });

    $('.saveNoteajaxForm').submit(function(e) {
        e.preventDefault();
        var url = $(this).attr('action');
        var formData = new FormData(this);
        my_ajax(url, formData, 'post', function(res) {

        }, true);
    })

    $('.single-select').select2({
        theme: 'bootstrap4',
        width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
        placeholder: $(this).data('placeholder'),
        allowClear: Boolean($(this).data('allow-clear')),
    });

    $('.modal-single-select').select2({
        dropdownParent: $("#campaignNotesModal"),
        theme: 'bootstrap4',
        width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
        placeholder: $(this).data('placeholder'),
        allowClear: Boolean($(this).data('allow-clear')),
    });

    $(document).on('click', '.copy-button', function (){
        var clipboard = new ClipboardJS('.copy-button');
        clipboard.on('success', function (e) {
            var alertElement = $('.copy_campaign');
            alertElement.show();
            setTimeout(function () {
                alertElement.hide();
            }, 3000);
            e.clearSelection();
        });
        clipboard.on('error', function (e) {
            console.error('Copy failed: ', e);
        });
    });


    $(document).on('change', '#account_id', function() {
        send_ajax_save_report('.ajaxFormAdAct');
    });

    $(document).on('click', '.ajax_date_range_btn', function() {
        $('#dateRangeModal').modal('hide');
        send_ajax_save_report('.date_range_ajaxForm');
    });

    $(document).on('click', '#refresh_fb_report', function() {
        send_ajax_save_report('.ajaxFormAdAct');
    });

    function send_ajax_save_report(form_class){
        var url = $(form_class).attr('action');
        var formData = new FormData($(form_class)[0]);
        $.ajax({
            url: url,
            method: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: formData,
            contentType: false,
            processData: false,
            dataType: "json",
            beforeSend: function() {
                $('#loader-container').show();
            },
            complete: function() {
                $('#loader-container').hide();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                $('#loader-container').hide();
            },
            success: function(data) {
                $('#loader-container').hide();
                var timer = 3000;
                if (data['reload'] !== undefined) {
                    toast(data['success'], "Success!", 'success', timer);
                    setTimeout(function() {
                        window.location.reload(true);
                    }, 600);
                    return false;
                }

                if (data['error'] !== undefined) {
                    toast(data['error'], "Error!", 'error');
                    return false;
                }

                if (data['success'] !== undefined) {
                    toast(data['success'], "Success!", 'success', timer);
                }
                callback(data);
            }
        });
    }

    function update_expiry_date(url, params, method){
        $.ajax({
            url: url,
            method: method,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: params,
            dataType: "json",
            beforeSend: function() {
                $('#loader-container').show();
            },
            complete: function () {
                $('#loader-container').hide();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                $('#loader-container').hide();
                ajaxErrorHandling(jqXHR, errorThrown);
            },
            success: function (data) {
                var timer = 5000;
                if (data['reload'] !== undefined) {
                    toast(data['success'], "Success!", 'success', timer);
                    setTimeout(function () {
                        window.location.reload(true);
                    }, 2000);
                    return false;
                }
                if (data['redirect'] !== undefined) {
                    toast(data['success'], "Success!", 'success', timer);
                    setTimeout(function () {
                        window.location = data['redirect'];
                    }, 2000);
                    return false;
                }
                if (data['error'] !== undefined) {
                    toast(data['error'], "Error!", 'error');
                    return false;
                }

                if (data['errors'] !== undefined) {
                    multiple_errors_ajax_handling(data['errors']);
                }
                callback(data);
            }
        });
    }

    $(document).on('click', '#campaignNotesBtn', function() {
        var today = new Date().toISOString().split('T')[0];
        $('#note_date').attr('max', today);
        $('#note_date').val(today);
        $('#campaignNotesModal').modal({
            backdrop: 'static',
            keyboard: false
        });
        $('#campaignNotesModal').modal('show');
    });

    $(function() {
        $('[data-bs-toggle="tooltip"]').tooltip();
    });

    $('#disconnect_fb_account').on('click', function() {
        Swal.fire({
            title: "Are you sure?",
            text: "By this action your facebook account is disconnected",
            type: "warning",
            showCancelButton: !0,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, confirm it!"
        }).then(function(t) {
            if (t.value) {
                $.ajax({
                    url: "{{ route('admin.facebook-ads-report.fb_disconnect') }}",
                    type: 'GET',
                    success: function(response) {
                        if (response.success) {
                            toast('Facebook Account Successfully Disconnected', "Success",
                                'success', 3000);
                            location.reload();
                        }
                    },
                    error: function(xhr, status, error) {
                        toast('Some Thing went Wrong', "Error!", 'error');
                    }
                });
            }
        });
    });


    $('#generate_pdf_btn').click(function(event) {
        var expiry_date = $('#expiry_date').val();
        var today = new Date();
        var dd = String(today.getDate()).padStart(2, '0');
        var mm = String(today.getMonth() + 1).padStart(2, '0');
        var yyyy = today.getFullYear();

        today = yyyy + '-' + mm + '-' + dd;

        if (expiry_date && (expiry_date === today || expiry_date < today)) {
            toast('Facebook Ad account has expired. Please recharge it to proceed with downloading the PDF.', "warning!", 'warning', 3000);
        }else{
            window.open("{{ route('admin.facebook-ads-report.download_pdf') }}", '_blank') 
            $('#loader-container').show();
            setTimeout(function() {
                $('#loader-container').hide();
                window.location.reload();
            }, 20000);
        }
    });

    //summary graph code start
        <?php
        $dates = '';
        $click = '';
        $cpc = '';
        $ctr = '';
        $impressions = '';
        if (is_array($summary_graph) && !empty($summary_graph['dates'])) {
            foreach ($summary_graph['dates'] as $key => $summary_graph_v) {
                $dates .= "'" . $summary_graph_v . "',";
                $click .= "'" . round($summary_graph['clicks'][$key]) . "',";
                $cpc .= "'" . round($summary_graph['cpc'][$key]) . "',";
                $ctr .= "'" . round($summary_graph['ctr'][$key]) . "',";
                $impressions .= "'" . round($summary_graph['impressions'][$key]) . "',";
            }
        }

        ?>

        var datesArray = [<?php echo $dates; ?>];
        var click = [<?php echo $click; ?>];
        var cpc = [<?php echo $cpc; ?>];
        var ctr = [<?php echo $ctr; ?>];
        var impressions = [<?php echo $impressions; ?>];


        var optionsLine = {
            chart: {
                foreColor: '#9ba7b2',
                height: 360,
                type: 'line',
                zoom: {
                    enabled: false
                },
                dropShadow: {
                    enabled: true,
                    top: 3,
                    left: 2,
                    blur: 4,
                    opacity: 0.1,
                }
            },
            stroke: {
                curve: 'smooth',
                width: 5
            },
            colors: ["#337AEE", '#FF9F41', '#97BE6D', '#EC41A7'],
            series: [{
                name: "Impressions",
                data: impressions
            }, {
                name: "Clicks",
                data: click
            }, {
                name: "CTR",
                data: ctr
            }, {
                name: "CPC",
                data: cpc
            }],
            title: {
                text: '',
                align: 'left',
                offsetY: 25,
                offsetX: 20
            },
            subtitle: {
                text: '',
                offsetY: 55,
                offsetX: 20
            },
            markers: {
                size: 4,
                strokeWidth: 0,
                hover: {
                    size: 7
                }
            },
            grid: {
                show: true,
                padding: {
                    bottom: 0
                }
            },
            labels: datesArray,
            yaxis: [{
                axisTicks: {
                    show: true,
                },
                axisBorder: {
                    show: true,
                },
                labels: {
                    style: {}
                },
                title: {
                    text: "Impressions",
                    style: {}
                },
                tooltip: {
                    enabled: true
                }
            }, {
                opposite: true,
                axisTicks: {
                    show: true,
                },
                axisBorder: {
                    show: true,
                },
                labels: {
                    style: {}
                },
                title: {
                    text: "Clicks",
                    style: {}
                },
                tooltip: {
                    enabled: true
                }
            }],
            legend: {
                position: 'top',
                horizontalAlign: 'center',
            }
        };
        var chartLine = new ApexCharts(document.querySelector('#summaryChart'), optionsLine);
        chartLine.render();

    //summary graph code end


    //clicks pie chart code start
        var options = {
            series: [{{ $pie_chart_data['clicks']['facebook'] ?? '0' }},
                {{ $pie_chart_data['clicks']['instagram'] ?? '0' }},
                {{ $pie_chart_data['clicks']['audience_network'] ?? '0' }}
            ],
            chart: {
                foreColor: '#9ba7b2',
                height: 250,
                type: 'donut',
            },
            colors: ["#0d6efd", "#17a00e", "#f4be5b"],
            labels: ['Facebook', 'Instagram', 'Audience Network'],
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        height: 320
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }]
        };
        var chart = new ApexCharts(document.querySelector("#clicks"), options);
        chart.render();
    //clicks pie chart code end

    //IMPRESSIONS  pie chart code start
        var options = {
            series: [{{ $pie_chart_data['impressions']['facebook'] ?? '0' }},
                {{ $pie_chart_data['impressions']['instagram'] ?? '0' }},
                {{ $pie_chart_data['impressions']['audience_network'] ?? '0' }}
            ],
            chart: {
                foreColor: '#9ba7b2',
                height: 250,
                type: 'donut',
            },
            colors: ["#0d6efd", "#17a00e", "#f4be5b"],
            labels: ['Facebook', 'Instagram', 'Audience Network'],
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        height: 320
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }]
        };
        var chart = new ApexCharts(document.querySelector("#impressions"), options);
        chart.render();
    //IMPRESSIONS  pie chart code end

    //CTR pie chart code start
        $(function() {
            "use strict";
            Highcharts.chart('ctr-chart', {
                chart: {
                    type: 'column',
                    styledMode: true
                },
                credits: {
                    enabled: false
                },
                title: {
                    text: 'CTR'
                },
                accessibility: {
                    announceNewData: {
                        enabled: true
                    }
                },
                xAxis: {
                    type: 'category'
                },

                legend: {
                    enabled: false
                },
                plotOptions: {
                    series: {
                        borderWidth: 0,
                        dataLabels: {
                            enabled: true,
                            format: '{point.y:.1f}%'
                        }
                    }
                },
                tooltip: {
                    headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
                    pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.2f}%</b> of total<br/>'
                },
                series: [{
                    name: "",
                    colorByPoint: true,
                    data: [{
                        name: "Facebook",
                        y: {{ $pie_chart_data['ctr']['facebook'] ?? '0' }},
                        drilldown: "Facebook"
                    }, {
                        name: "Instagram",
                        y: {{ $pie_chart_data['ctr']['instagram'] ?? '0' }},
                        drilldown: "Instagram"
                    }, {
                        name: "Audience Network",
                        y: {{ $pie_chart_data['ctr']['audience_network'] ?? '0' }},
                        drilldown: "Audience Network"
                    }]
                }],
                drilldown: {
                    series: [{
                        name: "Facebook",
                        id: "Facebook",
                        data: [
                            ["v65.0",
                                0.1
                            ],
                            ["v64.0",
                                1.3
                            ],
                            ["v63.0",
                                53.02
                            ],
                            ["v62.0",
                                1.4
                            ],
                            ["v61.0",
                                0.88
                            ],
                            ["v60.0",
                                0.56
                            ],
                            ["v59.0",
                                0.45
                            ],
                            ["v58.0",
                                0.49
                            ],
                            ["v57.0",
                                0.32
                            ],
                            ["v56.0",
                                0.29
                            ],
                            ["v55.0",
                                0.79
                            ],
                            ["v54.0",
                                0.18
                            ],
                            ["v51.0",
                                0.13
                            ],
                            ["v49.0",
                                2.16
                            ],
                            ["v48.0",
                                0.13
                            ],
                            ["v47.0",
                                0.11
                            ],
                            ["v43.0",
                                0.17
                            ],
                            ["v29.0",
                                0.26
                            ]
                        ]
                    }, {
                        name: "Instagram",
                        id: "Instagram",
                        data: [
                            ["v58.0",
                                1.02
                            ],
                            ["v57.0",
                                7.36
                            ],
                            ["v56.0",
                                0.35
                            ],
                            ["v55.0",
                                0.11
                            ],
                            ["v54.0",
                                0.1
                            ],
                            ["v52.0",
                                0.95
                            ],
                            ["v51.0",
                                0.15
                            ],
                            ["v50.0",
                                0.1
                            ],
                            ["v48.0",
                                0.31
                            ],
                            ["v47.0",
                                0.12
                            ]
                        ]
                    }, {
                        name: "Audience Network",
                        id: "Audience Network",
                        data: [
                            ["v11.0",
                                6.2
                            ],
                            ["v10.0",
                                0.29
                            ],
                            ["v9.0",
                                0.27
                            ],
                            ["v8.0",
                                0.47
                            ]
                        ]
                    }]
                }
            });
        });
    //CTR pie chart code end

    //cpc pie chart code start
        $(function() {
            "use strict";
            Highcharts.chart('cpc-chart', {
                chart: {
                    type: 'column',
                    styledMode: true
                },
                credits: {
                    enabled: false
                },
                title: {
                    text: 'CPC'
                },
                accessibility: {
                    announceNewData: {
                        enabled: true
                    }
                },
                xAxis: {
                    type: 'category'
                },

                legend: {
                    enabled: false
                },
                plotOptions: {
                    series: {
                        borderWidth: 0,
                        dataLabels: {
                            enabled: true,
                            format: '{point.y:.1f}%'
                        }
                    }
                },
                tooltip: {
                    headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
                    pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.2f}%</b> of total<br/>'
                },
                series: [{
                    name: "",
                    colorByPoint: true,
                    data: [{
                        name: "Facebook",
                        y: {{ $pie_chart_data['cpc']['facebook'] ?? '0' }},
                        drilldown: "Facebook"
                    }, {
                        name: "Instagram",
                        y: {{ $pie_chart_data['cpc']['instagram'] ?? '0' }},
                        drilldown: "Instagram"
                    }, {
                        name: "Audience Network",
                        y: {{ $pie_chart_data['cpc']['audience_network'] ?? '0' }},
                        drilldown: "Audience Network"
                    }]
                }],
                drilldown: {
                    series: [{
                        name: "Facebook",
                        id: "Facebook",
                        data: [
                            ["v58.0",
                                1.02
                            ],
                            ["v57.0",
                                7.36
                            ],
                            ["v56.0",
                                0.35
                            ],
                            ["v55.0",
                                0.11
                            ],
                            ["v54.0",
                                0.1
                            ],
                            ["v52.0",
                                0.95
                            ],
                            ["v51.0",
                                0.15
                            ],
                            ["v50.0",
                                0.1
                            ],
                            ["v48.0",
                                0.31
                            ],
                            ["v47.0",
                                0.12
                            ]
                        ]
                    }, {
                        name: "Instagram",
                        id: "Instagram",
                        data: [
                            ["v65.0",
                                0.1
                            ],
                            ["v64.0",
                                1.3
                            ],
                            ["v63.0",
                                53.02
                            ],
                            ["v62.0",
                                1.4
                            ],
                            ["v61.0",
                                0.88
                            ],
                            ["v60.0",
                                0.56
                            ],
                            ["v59.0",
                                0.45
                            ],
                            ["v58.0",
                                0.49
                            ],
                            ["v57.0",
                                0.32
                            ],
                            ["v56.0",
                                0.29
                            ],
                            ["v55.0",
                                0.79
                            ],
                            ["v54.0",
                                0.18
                            ],
                            ["v51.0",
                                0.13
                            ],
                            ["v49.0",
                                2.16
                            ],
                            ["v48.0",
                                0.13
                            ],
                            ["v47.0",
                                0.11
                            ],
                            ["v43.0",
                                0.17
                            ],
                            ["v29.0",
                                0.26
                            ]
                        ]
                    }, {
                        name: "Audience Network",
                        id: "Audience Network",
                        data: [
                            ["v11.0",
                                6.2
                            ],
                            ["v10.0",
                                0.29
                            ],
                            ["v9.0",
                                0.27
                            ],
                            ["v8.0",
                                0.47
                            ]
                        ]
                    }]
                }
            });
        });
    //cpc pie chart code end

    //widget graph code start
        <?php
        $widget_dates = '';
        $widget_click = '';
        $widget_cpc = '';
        $widget_ctr = '';
        $widget_impressions = '';
        if (!empty($summary_detail->dates)) {
            foreach ($summary_detail->dates as $key => $summary_graph_v) {
                $widget_dates .= "'" . $summary_graph_v . "',";
                $widget_click .= "'" . round($summary_detail->clicks[$key]) . "',";
                $widget_cpc .= "'" . round($summary_detail->cpc[$key]) . "',";
                $widget_ctr .= "'" . round($summary_detail->ctr[$key]) . "',";
                $widget_impressions .= "'" . round($summary_detail->impressions[$key]) . "',";
            }
        }

        ?>

        var widget_click = [<?php echo $widget_click; ?>];
        var widget_cpc = [<?php echo $widget_cpc; ?>];
        var widget_ctr = [<?php echo $widget_ctr; ?>];
        var widget_impressions = [<?php echo $widget_impressions; ?>];

        function charts() {
            // chart 5
            var options = {
                series: [{
                    name: "Clicks",
                    data: widget_click
                }],
                chart: {
                    type: "line",
                    width: 200,
                    height: 50,
                    toolbar: {
                        show: !1
                    },
                    zoom: {
                        enabled: !1
                    },
                    dropShadow: {
                        enabled: 0,
                        top: 3,
                        left: 14,
                        blur: 4,
                        opacity: .12,
                        color: "#3461ff"
                    },
                    sparkline: {
                        enabled: !0
                    }
                },
                markers: {
                    size: 0,
                    colors: ["#3461ff"],
                    strokeColors: "#fff",
                    strokeWidth: 2,
                    hover: {
                        size: 7
                    }
                },
                plotOptions: {
                    bar: {
                        horizontal: !1,
                        columnWidth: "35%",
                        endingShape: "rounded"
                    }
                },
                dataLabels: {
                    enabled: !1
                },
                stroke: {
                    show: !0,
                    width: 2.5,
                    curve: "smooth"
                },
                colors: ["#3461ff"],
                xaxis: {
                    categories: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"]
                },
                fill: {
                    opacity: 1
                },
                tooltip: {
                    theme: "dark",
                    fixed: {
                        enabled: !1
                    },
                    x: {
                        show: !1
                    },
                    y: {
                        title: {
                            formatter: function(e) {
                                return ""
                            }
                        }
                    },
                    marker: {
                        show: !1
                    }
                }
            };

            var chart = new ApexCharts(document.querySelector("#chart5"), options);
            chart.render();

            // chart 6
            var options = {
                series: [{
                    name: "Impressions ",
                    data: widget_impressions
                }],
                chart: {
                    type: "line",
                    width: 200,
                    height: 50,
                    toolbar: {
                        show: !1
                    },
                    zoom: {
                        enabled: !1
                    },
                    dropShadow: {
                        enabled: 0,
                        top: 3,
                        left: 14,
                        blur: 4,
                        opacity: .12,
                        color: "#3461ff"
                    },
                    sparkline: {
                        enabled: !0
                    }
                },
                markers: {
                    size: 0,
                    colors: ["#3461ff"],
                    strokeColors: "#fff",
                    strokeWidth: 2,
                    hover: {
                        size: 7
                    }
                },
                plotOptions: {
                    bar: {
                        horizontal: !1,
                        columnWidth: "35%",
                        endingShape: "rounded"
                    }
                },
                dataLabels: {
                    enabled: !1
                },
                stroke: {
                    show: !0,
                    width: 2.5,
                    curve: "smooth"
                },
                colors: ["#3461ff"],
                xaxis: {
                    categories: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"]
                },
                fill: {
                    opacity: 1
                },
                tooltip: {
                    theme: "dark",
                    fixed: {
                        enabled: !1
                    },
                    x: {
                        show: !1
                    },
                    y: {
                        title: {
                            formatter: function(e) {
                                return ""
                            }
                        }
                    },
                    marker: {
                        show: !1
                    }
                }
            };

            var chart = new ApexCharts(document.querySelector("#chart6"), options);
            chart.render();

            // chart 7
            var options = {
                series: [{
                    name: "CTR",
                    data: widget_ctr
                }],
                chart: {
                    type: "line",
                    width: 200,
                    height: 50,
                    toolbar: {
                        show: !1
                    },
                    zoom: {
                        enabled: !1
                    },
                    dropShadow: {
                        enabled: 0,
                        top: 3,
                        left: 14,
                        blur: 4,
                        opacity: .12,
                        color: "#3461ff"
                    },
                    sparkline: {
                        enabled: !0
                    }
                },
                markers: {
                    size: 0,
                    colors: ["#3461ff"],
                    strokeColors: "#fff",
                    strokeWidth: 2,
                    hover: {
                        size: 7
                    }
                },
                plotOptions: {
                    bar: {
                        horizontal: !1,
                        columnWidth: "35%",
                        endingShape: "rounded"
                    }
                },
                dataLabels: {
                    enabled: !1
                },
                stroke: {
                    show: !0,
                    width: 2.5,
                    curve: "smooth"
                },
                colors: ["#3461ff"],
                xaxis: {
                    categories: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"]
                },
                fill: {
                    opacity: 1
                },
                tooltip: {
                    theme: "dark",
                    fixed: {
                        enabled: !1
                    },
                    x: {
                        show: !1
                    },
                    y: {
                        title: {
                            formatter: function(e) {
                                return ""
                            }
                        }
                    },
                    marker: {
                        show: !1
                    }
                }
            };

            var chart = new ApexCharts(document.querySelector("#chart7"), options);
            chart.render();

            // chart 8
            var options = {
                series: [{
                    name: "CPC",
                    data: widget_cpc
                }],
                chart: {
                    type: "line",
                    width: 200,
                    height: 50,
                    toolbar: {
                        show: !1
                    },
                    zoom: {
                        enabled: !1
                    },
                    dropShadow: {
                        enabled: 0,
                        top: 3,
                        left: 14,
                        blur: 4,
                        opacity: .12,
                        color: "#3461ff"
                    },
                    sparkline: {
                        enabled: !0
                    }
                },
                markers: {
                    size: 0,
                    colors: ["#3461ff"],
                    strokeColors: "#fff",
                    strokeWidth: 2,
                    hover: {
                        size: 7
                    }
                },
                plotOptions: {
                    bar: {
                        horizontal: !1,
                        columnWidth: "35%",
                        endingShape: "rounded"
                    }
                },
                dataLabels: {
                    enabled: !1
                },
                stroke: {
                    show: !0,
                    width: 2.5,
                    curve: "smooth"
                },
                colors: ["#3461ff"],
                xaxis: {
                    categories: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"]
                },
                fill: {
                    opacity: 1
                },
                tooltip: {
                    theme: "dark",
                    fixed: {
                        enabled: !1
                    },
                    x: {
                        show: !1
                    },
                    y: {
                        title: {
                            formatter: function(e) {
                                return ""
                            }
                        }
                    },
                    marker: {
                        show: !1
                    }
                }
            };

            var chart = new ApexCharts(document.querySelector("#chart8"), options);
            chart.render();
        }

    //widget graph code end

    // gender graph code start

        var female_impressions = Math.round({{ $gender_graph->female->impressions ?? 0 }});
        var male_impressions = Math.round({{ $gender_graph->male->impressions ?? 0 }});
        var unknown_impressions = Math.round({{ $gender_graph->unknown->impressions ?? 0 }});

        var female_clicks = Math.round({{ $gender_graph->female->clicks ?? 0 }});
        var male_clicks = Math.round({{ $gender_graph->male->clicks ?? 0 }});
        var unknown_clicks = Math.round({{ $gender_graph->unknown->clicks ?? 0 }});

        var female_ctr = Math.round({{ $gender_graph->female->ctr ?? 0 }} * 100); // Round to 2 decimal places for CTR
        var male_ctr = Math.round({{ $gender_graph->male->ctr ?? 0 }} * 100);
        var unknown_ctr = Math.round({{ $gender_graph->unknown->ctr ?? 0 }} * 100);

        var female_cpc = Math.round({{ $gender_graph->female->cpc ?? 0 }} * 100); // Round to 2 decimal places for CPC
        var male_cpc = Math.round({{ $gender_graph->male->cpc ?? 0 }} * 100);
        var unknown_cpc = Math.round({{ $gender_graph->unknown->cpc ?? 0 }} * 100);

        var optionsLine = {
            chart: {
                foreColor: '#9ba7b2',
                height: 360,
                type: 'line',
                zoom: {
                    enabled: false
                },
                dropShadow: {
                    enabled: true,
                    top: 3,
                    left: 2,
                    blur: 4,
                    opacity: 0.1,
                },
                animations: {
                    enabled: false
                }
            },
            stroke: {
                curve: 'smooth',
                width: 5
            },
            colors: ["#337AEE", '#FF9F41', '#97BE6D', '#EC41A7'],
            series: [{
                name: "impressions",
                data: [female_impressions, male_impressions, unknown_impressions]
            }, {
                name: "clicks",
                data: [female_clicks, male_clicks, unknown_clicks]
            }, {
                name: "ctr",
                data: [female_ctr, male_ctr, unknown_ctr]
            }, {
                name: "cpc",
                data: [female_cpc, male_cpc, unknown_cpc]
            }],
            title: {
                text: '',
                align: 'left',
                offsetY: 25,
                offsetX: 20
            },
            subtitle: {
                text: '',
                offsetY: 55,
                offsetX: 20
            },
            markers: {
                size: 4,
                strokeWidth: 0,
                hover: {
                    size: 7
                }
            },
            grid: {
                show: true,
                padding: {
                    bottom: 0
                }
            },
            labels: ['Female', 'Male', 'Unknown'],
            yaxis: [{
                axisTicks: {
                    show: true,
                },
                axisBorder: {
                    show: true
                },
                labels: {
                    style: {}
                },
                title: {
                    text: "Impressions",
                    style: {}
                },
                tooltip: {
                    enabled: true
                }
            }, {
                opposite: true,
                axisTicks: {
                    show: true,
                },
                axisBorder: {
                    show: true,
                },
                labels: {
                    style: {}
                },
                title: {
                    text: "Clicks",
                    style: {}
                },
                tooltip: {
                    enabled: true
                }
            }],
            legend: {
                position: 'top',
                horizontalAlign: 'center',
            }
        };
        var chartLine = new ApexCharts(document.querySelector('#gender-chart'), optionsLine);
        chartLine.render();
    // gender graph code end

    // age graph code start
        var optionsLine = {
            chart: {
                foreColor: '#9ba7b2',
                height: 360,
                type: 'line',
                zoom: {
                    enabled: false
                },
                dropShadow: {
                    enabled: true,
                    top: 3,
                    left: 2,
                    blur: 4,
                    opacity: 0.1,
                },
                animations: {
                    enabled: false
                }
            },
            stroke: {
                curve: 'smooth',
                width: 5
            },
            colors: ["#337AEE", '#FF9F41', '#97BE6D', '#EC41A7'],
            series: [{
                name: "impressions",
                data: [
                    <?php echo isset($age_graph['18-24']) ? round($age_graph['18-24']['impressions']) : '0'; ?>,
                    <?php echo isset($age_graph['25-34']) ? round($age_graph['25-34']['impressions']) : '0'; ?>,
                    <?php echo isset($age_graph['35-44']) ? round($age_graph['35-44']['impressions']) : '0'; ?>,
                    <?php echo isset($age_graph['45-54']) ? round($age_graph['45-54']['impressions']) : '0'; ?>,
                    <?php echo isset($age_graph['55-64']) ? round($age_graph['55-64']['impressions']) : '0'; ?>,
                    <?php echo isset($age_graph['65+']) ? round($age_graph['65+']['impressions']) : '0'; ?>
                ]
            }, {
                name: "clicks",
                data: [
                    <?php echo isset($age_graph['18-24']) ? round($age_graph['18-24']['clicks']) : 0; ?>,
                    <?php echo isset($age_graph['25-34']) ? round($age_graph['25-34']['clicks']) : 0; ?>,
                    <?php echo isset($age_graph['35-44']) ? round($age_graph['35-44']['clicks']) : 0; ?>,
                    <?php echo isset($age_graph['45-54']) ? round($age_graph['45-54']['clicks']) : 0; ?>,
                    <?php echo isset($age_graph['55-64']) ? round($age_graph['55-64']['clicks']) : 0; ?>,
                    <?php echo isset($age_graph['65+']) ? round($age_graph['65+']['clicks']) : 0; ?>,
                ]
            }, {
                name: "ctr",
                data: [
                    <?php echo isset($age_graph['18-24']) ? round($age_graph['18-24']['ctr']) : '0'; ?>,
                    <?php echo isset($age_graph['25-34']) ? round($age_graph['25-34']['ctr']) : '0'; ?>,
                    <?php echo isset($age_graph['35-44']) ? round($age_graph['35-44']['ctr']) : '0'; ?>,
                    <?php echo isset($age_graph['45-54']) ? round($age_graph['45-54']['ctr']) : '0'; ?>,
                    <?php echo isset($age_graph['55-64']) ? round($age_graph['55-64']['ctr']) : '0'; ?>,
                    <?php echo isset($age_graph['65+']) ? round($age_graph['65+']['ctr']) : '0'; ?>
                ]
            }, {
                name: "cpc",
                data: [
                    <?php echo isset($age_graph['18-24']) ? round($age_graph['18-24']['cpc']) : '0'; ?>,
                    <?php echo isset($age_graph['25-34']) ? round($age_graph['25-34']['cpc']) : '0'; ?>,
                    <?php echo isset($age_graph['35-44']) ? round($age_graph['35-44']['cpc']) : '0'; ?>,
                    <?php echo isset($age_graph['45-54']) ? round($age_graph['45-54']['cpc']) : '0'; ?>,
                    <?php echo isset($age_graph['55-64']) ? round($age_graph['55-64']['cpc']) : '0'; ?>,
                    <?php echo isset($age_graph['65+']) ? round($age_graph['65+']['cpc']) : '0'; ?>,
                ]
            }],
            title: {
                text: '',
                align: 'left',
                offsetY: 25,
                offsetX: 20
            },
            subtitle: {
                text: '',
                offsetY: 55,
                offsetX: 20
            },
            markers: {
                size: 4,
                strokeWidth: 0,
                hover: {
                    size: 7
                }
            },
            grid: {
                show: true,
                padding: {
                    bottom: 0
                }
            },
            labels: ['18-24', '25-34', '35-44', '45-54', '55-64', '65+'],
            yaxis: [{
                axisTicks: {
                    show: true,
                },
                axisBorder: {
                    show: true,
                },
                labels: {
                    style: {}
                },
                title: {
                    text: "Impressions",
                    style: {}
                },
                tooltip: {
                    enabled: true
                }
            }, {
                opposite: true,
                axisTicks: {
                    show: true,
                },
                axisBorder: {
                    show: true,
                },
                labels: {
                    style: {}
                },
                title: {
                    text: "Clicks",
                    style: {}
                },
                tooltip: {
                    enabled: true
                }
            }],
            legend: {
                position: 'top',
                horizontalAlign: 'center',
            }
        };
        var chartLine = new ApexCharts(document.querySelector('#age-chart'), optionsLine);
        chartLine.render();
    // age graph code end

</script>
@endsection
