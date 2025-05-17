<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="assets/images/favicon-32x32.png" type="image/png" />
    <!--plugins-->
    <link href="{{ asset('front') }}/assets/plugins/simplebar/css/simplebar.css" rel="stylesheet" />
    <link href="{{ asset('front') }}/assets/plugins/perfect-scrollbar/css/perfect-scrollbar.css" rel="stylesheet" />
    <link href="{{ asset('front') }}/assets/plugins/metismenu/css/metisMenu.min.css" rel="stylesheet" />
    <!-- Bootstrap CSS -->
    <link href="{{ asset('front') }}/assets/css/bootstrap.min.css" rel="stylesheet" />
    <link href="{{ asset('front') }}/assets/css/bootstrap-extended.css" rel="stylesheet" />
    <link href="{{ asset('front') }}/assets/css/style.css" rel="stylesheet" />
    <link href="{{ asset('front') }}/assets/css/icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <link href="{{ asset('front') }}/assets/plugins/highcharts/css/dark-unica.css" rel="stylesheet" />
    <title>Facebook Ads Report</title>
    <style>
        body {
            font-size: 12px !important;
            margin: 0;
            padding: 0;
        }

        .container {
            text-align: center;
            margin-top: 100px;
        }

        .heading {
            margin-top: 20px;
            /* font-size: 24px; */
        }

        .apexcharts-menu-icon {
            display: none;
        }

        #ctr-chart {
            background-color: white !important;
        }

    </style>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

    <script type="text/javascript">
        google.charts.load("current", {packages:["corechart"]});
        google.charts.setOnLoadCallback(drawChart);
        function drawChart() {
            var data = google.visualization.arrayToDataTable([
                ['Task', 'Clicks'],
                ['Facebook',     {{ $pie_chart_data['clicks']['facebook'] ?? '0' }}],
                ['Instagram',  {{ $pie_chart_data['clicks']['instagram'] ?? '0' }}],
                ['Audience Network', {{ $pie_chart_data['clicks']['audience_network'] ?? '0' }}]
            ]);

            var options = {
                pieHole: 0.3,
                colors: ['#0d6efd', '#17a00e', '#f4be5b']

            };

            var chart = new google.visualization.PieChart(document.getElementById('clicks'));
            chart.draw(data, options);

            var data = google.visualization.arrayToDataTable([
                ['Task', 'Clicks'],
                ['Facebook',     {{ $pie_chart_data['impressions']['facebook'] ?? '0' }}],
                ['Instagram',  {{ $pie_chart_data['impressions']['instagram'] ?? '0' }}],
                ['Audience Network', {{ $pie_chart_data['impressions']['audience_network'] ?? '0' }}]
            ]);

            var options = {
                pieHole: 0.4,
                colors: ['#0d6efd', '#17a00e', '#f4be5b']
            };

            var chart = new google.visualization.PieChart(document.getElementById('impressions'));
            chart.draw(data, options);
        }
    </script>
</head>

<body>

    <div class="container">

        <img src="{{ asset('front') }}/assets/images/logo.png" width="500px" height="300px" class="logo"
            alt="logo icon">

        <br><br><br><br>

        <div class="heading">
            <h1 class="text-dark">Facebook Ads Report</h1>
            <h6 class="text-dark"> {{ @$ad_account_name }} </h6>
        </div>

        <div class="generated-at">
            Generated on: {{ @$generated_on }}
        </div>
    </div>

    <div style="page-break-before:always">&nbsp;</div>

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-2 row-cols-xxl-2">
        <div class="col">
            <div class="card radius-10">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="">
                            <p class="mb-1 text-dark" style="font-weight: bold; font-size: 15px;">Clicks </p>
                            <h4 class="mb-0 text-primary">{{ isset($summary_detail->summary->clicks) ? number_with_suffixes($summary_detail->summary->clicks) : '0' }}</h4>
                        </div>
                        <div class="ms-auto">
                            <div id="chart5"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card radius-10">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="">
                            <p class="mb-1 text-dark" style="font-weight: bold; font-size: 15px;">Impressions</p>
                            <h4 class="mb-0 text-primary">{{ isset($summary_detail->summary->impressions) ? number_with_suffixes($summary_detail->summary->impressions) : '0' }}</h4>
                        </div>
                        <div class="ms-auto">
                            <div id="chart6"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card radius-10">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="">
                            <p class="mb-1 text-dark" style="font-weight: bold; font-size: 15px;">CTR</p>
                            @if (isset($summary_detail->summary->ctr) && !empty($summary_detail->summary->ctr))
                                <h4 class="mb-0 text-primary">{{ $summary_detail->summary->ctr ?? '0' }}%</h4>
                            @else
                                <h4 class="mb-0 text-primary">0%</h4>
                            @endif
                        </div>
                        <div class="ms-auto">
                            <div id="chart7"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card radius-10">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="">
                            <p class="mb-1 text-dark" style="font-weight: bold; font-size: 15px;">CPC</p>
                            @if (isset($summary_detail->summary->cpc) && !empty($summary_detail->summary->cpc))
                                <h4 class="mb-0 text-primary">{{ round($summary_detail->summary->cpc, 1) ?? '0' }}</h4>
                            @else
                                <h4 class="mb-0 text-primary">0</h4>
                            @endif
                        </div>
                        <div class="ms-auto">
                            <div id="chart8"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <br><br><br><br><br><br>

    <div class="row">
        <div class="col-12 col-xl-12">
            <div class="card shadow radius-10 w-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Campaigns Performance</h6>
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
                                </tr>
                            </thead>
                            <tbody>
                                
                                @if (isset($campaigns_with_notes) && !empty($campaigns_with_notes))
                                    @foreach ($campaigns_with_notes as $campaign)
                                        <tr>
                                            <td>{!! wordwrap($campaign['name'], 30, '<br>', true) ?? '...' !!}</td>
                                            <td>{{ number_format($campaign['total_leads']) ?? '0' }}</td>
                                            <td>${{ number_format($campaign['cost_per_lead']) ?? '0' }}</td>
                                            <td>${{ $campaign['daily_budget'] ?? "0" }}</td>
                                            <td>${{ $campaign['lifetime_budget'] ?? "0" }}</td>
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

    <div style="page-break-before:always">&nbsp;</div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-0 text-dark" style="font-weight: bold; font-size: 20px; text:">Summary</h6>
                    <p class="text-center">
                        <span>
                            <img src="{{ asset('front') }}/assets/images/impression.png" width="10px" height="10px" class="logo"
                            alt="logo icon"> Impressions
                        </span>
                        <span>
                            <img src="{{ asset('front') }}/assets/images/clicks.png" width="10px" height="10px" class="logo"
                            alt="logo icon"> Clicks
                        </span>
                        <span>
                            <img src="{{ asset('front') }}/assets/images/ctr.png" width="10px" height="10px" class="logo"
                            alt="logo icon"> CTR
                        </span>
                        <span>
                            <img src="{{ asset('front') }}/assets/images/cpc.png" width="10px" height="10px" class="logo"
                            alt="logo icon"> CPC
                        </span>
                    </p>
                    <div id="summaryChart"></div>
                    
                </div>
            </div>
        </div>
    </div>

    <div style="page-break-before:always">&nbsp;</div>

    <div class="row">
        <div class="col-6 mx-auto">
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-0 text-uppercase text-dark" style="font-weight: bold; font-size: 20px;">Clicks </h6>
                    <p>Platform</p>
                    <div id="clicks"></div>
                </div>
            </div>
        </div>

        <div class="col-6 mx-auto">
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-0 text-uppercase text-dark" style="font-weight: bold; font-size: 20px;">Impressions</h6>
                    <p>Platform</p>
                    <div id="impressions"></div>
                </div>
            </div>
        </div>

        <div class="col-6 mx-auto">
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-0 text-uppercase text-dark" style="font-weight: bold; font-size: 20px;">CTR </h6>
                    <p>Platform</p>
                    <div id="ctr-chart"></div>
                </div>
            </div>
        </div>

        <div class="col-6 mx-auto">
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-0 text-uppercase text-dark" style="font-weight: bold; font-size: 20px;">CPC </h6>
                    <p>Platform</p>
                    <div id="cpc-chart"></div>
                </div>
            </div>
        </div>

    </div><!--end row-->

    <div style="page-break-before:always">&nbsp;</div>

    <div class="row">
        <div class="col-12 col-xl-12">
            <div class="card shadow radius-10 w-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 text-dark" style="font-weight: bold; font-size: 20px;">Campaigns</h6>
                    </div>
                    <div class="table-responsive mt-2">
                        <table class="table align-middle table-bordered mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Campaign</th>
                                    <th>Clicks</th>
                                    <th>Impressions</th>
                                    <th>CTR</th>
                                    <th style="font-size: 12px;">CPC <span class="text-primary"
                                            style="font-size: 8px;">(SGD)</span></th>
                                    <th style="font-size: 12px;">Amount <br> Spent <span class="text-primary"
                                            style="font-size: 8px;">(SGD)</span></th>
                                    <th style="font-size: 12px;">Cost Per <br> Result <span class="text-primary"
                                            style="font-size: 8px;">(SGD)</span></th>
                                </tr>
                            </thead>
                            <tbody id="campaign-table">
                                @if (isset($campaigns) && !empty($campaigns))
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
                        <h6 class="mb-0 text-dark" style="font-weight: bold; font-size: 20px;">Ad Sets</h6>
                    </div>
                    <div class="table-responsive mt-2">
                        <table class="table align-middle table-bordered mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Ad Set</th>
                                    <th>Campaign</th>
                                    <th>Clicks</th>
                                    <th>Impressions</th>
                                    <th>CTR</th>
                                    <th style="font-size: 12px;">CPC <span class="text-primary"
                                            style="font-size: 8px;">(SGD)</span></th>
                                    <th style="font-size: 12px;">Amount <br> Spent <span class="text-primary"
                                            style="font-size: 8px;">(SGD)</span></th>
                                    <th style="font-size: 12px;">Cost Per <br> Result <span class="text-primary"
                                            style="font-size: 8px;">(SGD)</span></th>
                                </tr>
                            </thead>
                            <tbody id="ad-set-table">
                                @if (isset($adsets) && !empty($adsets))
                                    @foreach ($adsets as $item)
                                        <tr>
                                            <td>{!! wordwrap($item->adset_name, 10, '<br>', true) ?? '...' !!}</td>
                                            <td>{!! wordwrap($item->campaign_name, 10, '<br>', true) ?? '...' !!}</td>
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
                        <h6 class="mb-0 text-dark" style="font-weight: bold; font-size: 20px;">Ads</h6>
                    </div>
                    <div class="table-responsive mt-2">
                        <table class="table align-middle table-bordered mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="font-size: 10px;">Ads</th>
                                    <th style="font-size: 10px;">Ad Set</th>
                                    <th style="font-size: 10px;">Campaign</th>
                                    <th style="font-size: 10px;">Clicks</th>
                                    <th style="font-size: 10px;">Impressions</th>
                                    <th style="font-size: 10px;">CTR</th>
                                    <th style="font-size: 10px;">CPC <span class="text-primary"
                                            style="font-size: 6px;">(SGD)</span></th>
                                    <th style="font-size: 10px;">Amount <br> Spent <span class="text-primary"
                                            style="font-size: 6px;">(SGD)</span></th>
                                    <th style="font-size: 10px;">Cost Per <br> Result <span class="text-primary"
                                            style="font-size: 6px;">(SGD)</span></th>
                                </tr>
                            </thead>
                            <tbody id="ad-table">
                                @if ($ads && !empty($ads))
                                    @foreach ($ads as $item)
                                        <tr>
                                            <td><img src="{{ asset('front') }}/assets/images/noimage.jpg"
                                                    width="40px" height="40px" alt="">
                                                &nbsp;&nbsp;&nbsp;{!! wordwrap($item->ad_name, 10, '<br>', true) ?? '...' !!}</td>
                                            <td>{!! wordwrap($item->adset_name, 10, '<br>', true) ?? '...' !!}</td>
                                            <td>{!! wordwrap($item->campaign_name, 10, '<br>', true) ?? '...' !!}</td>
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

    <div style="page-break-before:always">&nbsp;</div>

    <div class="row">
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-0 text-dark text-uppercase" style="font-weight: bold; font-size: 20px;">Demographics
                    </h6>
                    <div class="row">
                        <div class="col-12 mx-auto">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="mb-0 text-dark text-uppercase"
                                        style="font-weight: bold; font-size: 20px;">Gender </h6>
                                    <p class="text-center">
                                        <span>
                                            <img src="{{ asset('front') }}/assets/images/impression.png" width="10px" height="10px" class="logo"
                                            alt="logo icon"> Impressions
                                        </span>
                                        <span>
                                            <img src="{{ asset('front') }}/assets/images/clicks.png" width="10px" height="10px" class="logo"
                                            alt="logo icon"> Clicks
                                        </span>
                                        <span>
                                            <img src="{{ asset('front') }}/assets/images/ctr.png" width="10px" height="10px" class="logo"
                                            alt="logo icon"> CTR
                                        </span>
                                        <span>
                                            <img src="{{ asset('front') }}/assets/images/cpc.png" width="10px" height="10px" class="logo"
                                            alt="logo icon"> CPC
                                        </span>
                                    </p>
                                    <div id="gender-chart"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 mx-auto">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="mb-0 text-dark text-uppercase"
                                        style="font-weight: bold; font-size: 20px;">Age </h6>
                                    <p class="text-center">
                                        <span>
                                            <img src="{{ asset('front') }}/assets/images/impression.png" width="10px" height="10px" class="logo"
                                            alt="logo icon"> Impressions
                                        </span>
                                        <span>
                                            <img src="{{ asset('front') }}/assets/images/clicks.png" width="10px" height="10px" class="logo"
                                            alt="logo icon"> Clicks
                                        </span>
                                        <span>
                                            <img src="{{ asset('front') }}/assets/images/ctr.png" width="10px" height="10px" class="logo"
                                            alt="logo icon"> CTR
                                        </span>
                                        <span>
                                            <img src="{{ asset('front') }}/assets/images/cpc.png" width="10px" height="10px" class="logo"
                                            alt="logo icon"> CPC
                                        </span>
                                    </p>
                                    <div id="age-chart"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div><!--end row-->

    <div style="page-break-before:always">&nbsp;</div>

    <div class="row">
        <div class="col-12 col-xl-12">
            <div class="card shadow radius-10 w-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 text-dark" style="font-weight: bold; font-size: 20px;">Country</h6>
                    </div>
                    <div class="table-responsive mt-2">
                        <table class="table align-middle table-bordered mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Country</th>
                                    <th>Clicks</th>
                                    <th>Impressions</th>
                                    <th>CTR</th>
                                    <th style="font-size: 12px;">CPC <span class="text-primary"
                                            style="font-size: 8px;">(SGD)</span></th>
                                    <th style="font-size: 12px;">Amount <br> Spent <span class="text-primary"
                                            style="font-size: 8px;">(SGD)</span></th>
                                    <th style="font-size: 12px;">Cost Per <br> Result <span class="text-primary"
                                            style="font-size: 8px;">(SGD)</span></th>
                                </tr>
                            </thead>
                            <tbody id="ad-table">
                                @if (isset($summary_detail) && !empty($summary_detail))
                                    @foreach ($summary_detail as $item)
                                        @if (!empty($item->clicks) && !empty($item->impressions))
                                            <tr>
                                                <td><img src="{{ asset('front') }}/assets/images/sg.png"
                                                        width="20px" height="20px" alt="">
                                                    &nbsp;&nbsp;&nbsp;Singapore</td>
                                                <td>{{ $item->clicks ?? '...' }}</td>
                                                <td>{{ $item->impressions ?? '...' }}</td>
                                                <td>{{ $item->ctr ?? '...' }}</td>
                                                <td>{{ $item->cpc ?? '...' }}</td>
                                                <td>{{ $item->spend ?? '...' }}</td>
                                                <td>{{ $item->spend ?? '...' }}</td>
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

    @if (isset($campaign_notes) && count($campaign_notes) > 0)
        <div class="row">
            <div class="col-12 col-xl-12">
                <div class="card shadow radius-10 w-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-3 text-dark" style="font-weight: bold; font-size: 20px;">Notes</h5>
                        </div>
                        @foreach ($campaign_notes as $note)
                            <div class="card mb-3" style="border: 1px solid #ced4da;">
                                <div class="card-body">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <h6>{{ $note->campaign_name }}</h6>
                                    </div>
                                    <p>{{ $note->note }} <br>
                                        <small>{{ $note->note_date }}</small>
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif

    

    <script src="{{ asset('front') }}/assets/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('front') }}/assets/js/jquery.min.js"></script>
    <script src="{{ asset('front') }}/assets/plugins/simplebar/js/simplebar.min.js"></script>
    <script src="{{ asset('front') }}/assets/plugins/metismenu/js/metisMenu.min.js"></script>
    <script src="{{ asset('front') }}/assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js"></script>
    <script src="{{ asset('front') }}/assets/js/pace.min.js"></script>
    <script src="{{ asset('front') }}/assets/plugins/apexcharts-bundle/js/apexcharts.min.js"></script>
    <script src="{{ asset('front') }}/assets/plugins/highcharts/js/highcharts.js"></script>
    <script src="{{ asset('front') }}/assets/js/app.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"
        integrity="sha512-GsLlZN/3F2ErC5ifS5QtgpiJtWd43JWSuIgh7mbzZ8zBps+dvLusV+eNQATqgA/HdeKFVgA5v3S/cIrLF7QnIg=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        $(document).ready(function() {
            charts();
        })

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
                        width: 130,
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
                        width: 90,
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
                        width: 130,
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
                        width: 130,
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

        //summary graph code start
            var datesArray = <?php echo $summary_dates; ?>;
            var click = <?php echo $summary_click; ?>;
            var cpc = <?php echo $summary_cpc; ?>;
            var ctr = <?php echo $summary_ctr; ?>;
            var impressions = <?php echo $summary_impressions; ?>;

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
                    name: "",
                    data: impressions
                }, {
                    name: "",
                    data: click
                }, {
                    name: "",
                    data: ctr
                }, {
                    name: "",
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
                        color: '#FF9F41'
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

        // gender graph code start
            var female_impressions = Math.round({{$gender_graph->female->impressions ?? 0}});
            var male_impressions = Math.round({{$gender_graph->male->impressions ?? 0}});
            var unknown_impressions = Math.round({{$gender_graph->unknown->impressions ?? 0}});

            var female_clicks = Math.round({{$gender_graph->female->clicks ?? 0}});
            var male_clicks = Math.round({{$gender_graph->male->clicks ?? 0}});
            var unknown_clicks = Math.round({{$gender_graph->unknown->clicks ?? 0}});

            var female_ctr = Math.round({{$gender_graph->female->ctr ?? 0}} * 100); // Round to 2 decimal places for CTR
            var male_ctr = Math.round({{$gender_graph->male->ctr ?? 0}} * 100);
            var unknown_ctr = Math.round({{$gender_graph->unknown->ctr ?? 0}} * 100);

            var female_cpc = Math.round({{$gender_graph->female->cpc ?? 0}} * 100); // Round to 2 decimal places for CPC
            var male_cpc = Math.round({{$gender_graph->male->cpc ?? 0}} * 100);
            var unknown_cpc = Math.round({{$gender_graph->unknown->cpc ?? 0}} * 100);

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
                    name: "",
                    data: [female_impressions, male_impressions, unknown_impressions]
                }, {
                    name: "",
                    data: [female_clicks, male_clicks, unknown_clicks]
                }, {
                    name: "",
                    data: [female_ctr, male_ctr, unknown_ctr]
                }, {
                    name: "",
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
                    name: "",
                    data: [
                        <?php echo isset($age_graph['18-24']) ? round($age_graph['18-24']['impressions']) : '0'; ?>,
                        <?php echo isset($age_graph['25-34']) ? round($age_graph['25-34']['impressions']) : '0'; ?>,
                        <?php echo isset($age_graph['35-44']) ? round($age_graph['35-44']['impressions']) : '0'; ?>,
                        <?php echo isset($age_graph['45-54']) ? round($age_graph['45-54']['impressions']) : '0'; ?>,
                        <?php echo isset($age_graph['55-64']) ? round($age_graph['55-64']['impressions']) : '0'; ?>,
                        <?php echo isset($age_graph['65+']) ? round($age_graph['65+']['impressions']) : '0'; ?>
                    ]
                }, {
                    name: "",
                    data: [
                        <?php echo isset($age_graph['18-24']) ? round($age_graph['18-24']['clicks']) : 0 ?>,
                        <?php echo isset($age_graph['25-34']) ? round($age_graph['25-34']['clicks']) : 0 ?>,
                        <?php echo isset($age_graph['35-44']) ? round($age_graph['35-44']['clicks']) : 0 ?>,
                        <?php echo isset($age_graph['45-54']) ? round($age_graph['45-54']['clicks']) : 0 ?>,
                        <?php echo isset($age_graph['55-64']) ? round($age_graph['55-64']['clicks']) : 0 ?>,
                        <?php echo isset($age_graph['65+']) ? round($age_graph['65+']['clicks']) : 0 ?>,
                    ]
                }, {
                    name: "",
                    data: [
                        <?php echo isset($age_graph['18-24']) ? round($age_graph['18-24']['ctr']) : '0'; ?>,
                        <?php echo isset($age_graph['25-34']) ? round($age_graph['25-34']['ctr']) : '0'; ?>,
                        <?php echo isset($age_graph['35-44']) ? round($age_graph['35-44']['ctr']) : '0'; ?>,
                        <?php echo isset($age_graph['45-54']) ? round($age_graph['45-54']['ctr']) : '0'; ?>,
                        <?php echo isset($age_graph['55-64']) ? round($age_graph['55-64']['ctr']) : '0'; ?>,
                        <?php echo isset($age_graph['65+']) ? round($age_graph['65+']['ctr']) : '0'; ?>
                    ]
                }, {
                    name: "",
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
</body>

</html>
