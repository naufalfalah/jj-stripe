@extends('layouts.admin')
@section('page-css')
<link href="{{ asset('front') }}/assets/plugins/datatable/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css"
    href="{{ asset('front') }}/assets/plugins/daterangepicker/css/daterangepicker.css" />
<style>
    .card-image-icon {
        width: 50px;
        border-radius: 12px;
        padding: 11px;
    }

    .chart-container {
        height: 426px;
    }
</style>
@endsection
@section('content')
<div class="row">
    <div class="col-lg-6 col-md-6">
        <div class="card text-left" style="height: 100px;">
            <div class="card-body">
                <div class="d-flex align-items-center h-100">
                    {{-- <i class="fas fa-microchip fa-2x mr-3"></i> --}}
                    <img style="background: #2296f2;" class="card-img-left example-card-img-responsive card-image-icon"
                        src="{{asset('front/assets/icons/sheets-sheet-svgrepo-com (1).svg')}}" />
                    <div class="mx-3">
                        <h5 class="card-title" style="font-size: 20px;">Client Google Sheets</h5>
                        <a href="{{ route('admin.client_sheets.index') }}" class="">Data</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6 col-md-6">
        <div class="card text-left" style="height: 100px;">
            <div class="card-body">
                <div class="d-flex align-items-center h-100">
                    {{-- <i class="fas fa-microchip fa-2x mr-3"></i> --}}
                    <img style="background: #2296f2;" class="card-img-left example-card-img-responsive card-image-icon"
                        src="{{asset('front/assets/icons/script-1601-svgrepo-com.svg')}}" />
                    <div class="mx-3">
                        <h5 class="card-title" style="font-size: 20px;">New Scripts</h5>
                        <a href="{{ route('admin.scripts.index') }}" class="">Data</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row justify-content-end">
        <div class="form-group col-lg-3 my-2">
            <div class="input-group mb-3">
                <input class="result form-control" type="text" name="follow_up_date_time" id="date-time"
                    placeholder="No Follow Up Scheduled">
            </div>
        </div>
    </div>
</div>
<div class="row" id="loadingIndicator">
    <div>
        <p>Loading Chart...</p>
    </div>
</div>
<div class="row mb-2" id="chart-container">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="card-title text-bold">
                    Statistics for selected period
                </div>
                <div class="chart-container">
                    <canvas id="myChart" style="display: none;"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-3 col-md-6">
        <div class="card text-left" style="height: 100px;">
            <div class="card-body">
                <div class="d-flex align-items-center h-100">
                    {{-- <i class="fas fa-microchip fa-2x mr-3"></i> --}}
                    <img style="background: #2296f2;" class="card-img-left example-card-img-responsive card-image-icon"
                        src="{{asset('front/assets/icons/call-192-svgrepo-com.svg')}}" />
                    <div class="mx-3">
                        <h5 class="card-title" style="font-size: 14px;">TOTAL CALLS</h5>
                        <p class="card-text">{{$card_call['total_call']}}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card text-left" style="height: 100px;">
            <div class="card-body">
                <div class="d-flex align-items-center h-100">
                    <img style="background: #e44f43" class="card-img-left example-card-img-responsive card-image-icon"
                        src="{{asset('front/assets/icons/call-miss-svgrepo-com.svg')}}" />
                    <div class="mx-3">
                        <h5 class="card-title" style="font-size: 14px;">MISSED CALLS</h5>
                        <p class="card-text">{{$card_call['total_unanswered']}}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card text-left" style="height: 100px;">
            <div class="card-body">
                <div class="d-flex align-items-center h-100">
                    <img style="background: #f9d55b" class="card-img-left example-card-img-responsive card-image-icon"
                        src="{{asset('front/assets/icons/time-svgrepo-com.svg')}}" />
                    <div class="mx-3">
                        <h5 class="card-title" style="font-size: 14px;">AVG. CALL DURATION</h5>
                        <p class="card-text">{{$card_call['average_call_duration']}}s</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card text-left" style="height: 100px;">
            <div class="card-body">
                <div class="d-flex align-items-center h-100">
                    {{-- <i class="fas fa-microchip fa-2x mr-3"></i> --}}
                    <img style="background: #f9d55b" class="card-img-left example-card-img-responsive card-image-icon"
                        src="{{asset('front/assets/icons/time-sand-svgrepo-com.svg')}}" />
                    <div class="mx-3">
                        <h5 class="card-title" style="font-size: 14px;">AVG. WAITING TIME</h5>
                        <p class="card-text">{{$card_call['average_waiting_time']}}s</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="card text-left" style="height: 100px;">
            <div class="card-body">
                <div class="d-flex align-items-center h-100">
                    {{-- <i class="fas fa-microchip fa-2x mr-3"></i> --}}
                    <img style="background: #f9d55b" class="card-img-left example-card-img-responsive card-image-icon"
                        src="{{asset('front/assets/icons/calendar-user-svgrepo-com.svg')}}" />
                    <div class="mx-3">
                        <h5 class="card-title" style="font-size: 14px;">APPOINTMENT CONVERTED</h5>
                        <p class="card-text">12</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="card text-left" style="height: 100px;">
            <div class="card-body">
                <div class="d-flex align-items-center h-100">
                    {{-- <i class="fas fa-microchip fa-2x mr-3"></i> --}}
                    <img style="background: #f9d55b" class="card-img-left example-card-img-responsive card-image-icon"
                        src="{{asset('front/assets/icons/lead-svgrepo-com.svg')}}" />
                    <div class="mx-3">
                        <h5 class="card-title" style="font-size: 14px;">WHATSAPP LEAD</h5>
                        <p class="card-text">12</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 d-flex">
        <div class="card radius-10 w-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <h6 class="mb-0">Recent Call History</h6>
                </div>
                <div class="table-responsive mt-2">
                    <table class="table align-middle mb-0" id="call-history">
                        <thead class="table-light">
                            <tr>
                                <th>Contact</th>
                                <th>Agent</th>
                                <th>Duration</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 d-flex">
        <div class="card radius-10 w-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    {{-- <h6 class="mb-0">Recent Call History</h6> --}}
                </div>
                <div class="table-responsive mt-2">
                    <table class="table align-middle mb-0" id="timelog-history">
                        <thead class="table-light">
                            <tr>
                                <th>Client Name</th>
                                <th>Admin Name</th>
                                <th>Time clock in</th>
                                <th>Time clock out</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-scripts')
    <script src="{{ asset('front') }}/assets/plugins/vectormap/jquery-jvectormap-2.0.2.min.js"></script>
    <script src="{{ asset('front') }}/assets/plugins/vectormap/jquery-jvectormap-world-mill-en.js"></script>
    <script src="{{ asset('front') }}/assets/plugins/chartjs/js/chart.js"></script>
    <script src="{{ asset('front') }}/assets/plugins/hammer/js/hammer.js"></script>
    <script src="{{ asset('front') }}/assets/plugins/chartjs/js/chartjs-plugin-zoom.min.js"></script>
    <script src="{{ asset('front') }}/assets/plugins/apexcharts-bundle/js/apexcharts.min.js"></script>
    <script src="{{ asset('front') }}/assets/plugins/datatable/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('front') }}/assets/plugins/datatable/js/dataTables.bootstrap5.min.js"></script>
    <script src="{{ asset('front') }}/assets/js/table-datatable.js"></script>
    <script src="{{ asset('front') }}/assets/plugins/bootstrap-material-datetimepicker/js/moment.min.js"></script>
    <script src="{{ asset('front') }}/assets/plugins/daterangepicker/js/daterangepicker.min.js"></script>

    <script>
        $(document).ready(function () {
            let myChart;
            let startDate = moment().format('YYYY-MM-DD');
            let endDate = moment().endOf('isoWeek').format('YYYY-MM-DD');
            // Function to show loading indicator
            function showLoadingIndicator() {
                document.getElementById('loadingIndicator').style.display = 'block';
                document.getElementById('myChart').style.display = 'none';
            }

            // Function to hide loading indicator
            function hideLoadingIndicator() {
                document.getElementById('loadingIndicator').style.display = 'none';
                document.getElementById('myChart').style.display = 'block';
            }

            // Function to create and update the chart
            function createAndUpdateChart(responseData) {
                if (myChart) {
                    myChart.destroy();
                }
                // Extract dates and hours from the response
                const dates = Object.keys(responseData);
                const hours = Object.keys(responseData[dates[0]]);

                // Create an array to store datasets for each date
                const datasets = dates.map((date, index) => {
                    return {
                        label: date,
                        data: hours.map(hour => responseData[date][hour]),
                        backgroundColor: '#2095f3', // Set bar color to blue
                        order: index, // Maintain order in the legend
                        borderWidth: 0, // Remove border
                        borderRadius: 0,
                    };
                });

                // Combine dates and hours to create time labels
                const timeLabels = hours.map(hour => `${parseInt(hour)}H`);

                // Chart.js configuration
                const chartConfig = {
                    type: 'bar',
                    data: {
                        labels: timeLabels,
                        datasets: datasets,
                    },
                    options: {
                        y: {
                            beginAtZero: true, // Ensure the Y-axis starts from zero
                            min: 0, // Set the minimum value for the Y-axis
                            title: {
                                display: true,
                                text: 'Data Value',
                            },
                        },
                        interaction: {
                            mode: 'nearest', // or 'point' depending on your preference
                            intersect: false,
                        },
                        responsive: true, // Enable responsiveness
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                            },
                            elements: {
                                bar: {
                                    borderRadius: 0, // Remove border radius
                                },
                            },
                        },
                    }
                };

                // Create the chart
                const ctx = document.getElementById('myChart').getContext('2d');
                myChart = new Chart(ctx, chartConfig);

                // Hide the loading indicator once the chart is created
                hideLoadingIndicator();
            }

            // Function to fetch data from the API with start_date and end_date parameters
            function fetchDataAndCreateChart(tagId = '', startDate, endDate) {
                // Show the loading indicator while fetching data
                showLoadingIndicator();

                // Replace this with your actual API endpoint
                const apiUrl = `{{ route('cloudtalk.statistics') }}?start_date=${startDate}&end_date=${endDate}&tag_id=${tagId}`;

                // Fetch data from the API
                fetch(apiUrl)
                    .then(response => response.json())
                    .then(data => {
                        // Call the function to create and update the chart with the response data
                        createAndUpdateChart(data);
                    })
                    .catch(error => {
                        console.error('Error fetching data:', error);
                        // Hide the loading indicator in case of an error
                        hideLoadingIndicator();
                    });
            }

            function timelogHistory() 
            {
                let url = "{{ route('admin.list.timelog') }}";
                $("#timelog-history").DataTable({
                    processing: true,
                    serverSide: true,
                    stateSave: false,
                    pageLength: 10,
                    ajax: {
                        url: url,
                        type: 'GET',
                    },
                    columns: [
                        { data: 'client_name' },
                        { data: 'admin_name' },
                        { data: 'start_time' },
                        { data: 'stopped_time' },
                    ],
                });
            }
            // FETCH DATA CALL HISTORY TO TABLE
            function fetchDataTablesCall()
            {
                if ($.fn.DataTable.isDataTable('#call-history')) {
                    $('#call-history').DataTable().destroy();
                }
                let url = `{{ route('admin.home') }}?tag_id=${$("#filterTag").val()}&start_date=${startDate}&end_date=${endDate}`;
                $("#call-history").DataTable({
                    processing: true,
                    serverSide: true,
                    stateSave: false,
                    pageLength: 10,
                    ajax: {
                        url: url,
                        type: 'GET',
                        data: function (d) {
                            d.limit = d.length; // Send limit parameter
                            d.page = (d.start / d.length) + 1;// Send page parameter
                        }
                    },
                    columns: [
                        { data: 'contact_number' },
                        { data: 'agent' },
                        { data: 'duration' },
                        { data: 'actions', orderable: false, searchable: false, className: 'text-end' },
                    ],
                });
            }

            // INIT CALL
            timelogHistory()
            fetchDataAndCreateChart('', startDate, endDate);
            fetchDataTablesCall();
            $("#agent-history").DataTable();
            $("#filterTag").change(function (e) {
                fetchDataAndCreateChart(e.target.value, startDate, endDate);
                fetchDataTablesCall();
            });
            $('#date-time').daterangepicker({
                autoclose: true,
                locale: {
                    format: 'DD MMMM YYYY'
                }
            }, function(start, end, label) {
                var formattedStartDate = start.format('YYYY-MM-DD');
                var formattedEndDate = end.endOf('isoWeek').format('YYYY-MM-DD');
                startDate = formattedStartDate;
                endDate = formattedEndDate;
                fetchDataAndCreateChart('', startDate, endDate);
                fetchDataTablesCall();
            }).val(startDate + " - " + endDate);
        });
    </script>
@endsection