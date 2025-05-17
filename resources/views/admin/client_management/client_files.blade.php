@extends('layouts.admin')

@push('styles')
    <link href="{{ asset('front/assets/plugins/fileUpload/fileUpload.css') }}" rel="stylesheet" />
    <link href="{{ asset('front') }}/assets/plugins/datatable/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.0/fullcalendar.min.css" />
    <style>
        .new_lead {
            color: rgba(200, 99, 255, 1) !important;
        }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
        </div>
    </div>
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-3 row-cols-xxl-3">
        <div class="col">
            <div class="card radius-10 border-0 border-start border-success border-3">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="">
                            <p class="mb-1">Total Balance</p>
                            <h4 class="mb-0 text-success">{{ get_price($total_balance) }}</h4>
                        </div>
                        <div class="ms-auto widget-icon bg-success text-white">
                            <i class="bi bi-currency-dollar"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card radius-10 border-0 border-start border-pink border-3">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="">
                            <p class="mb-1">PPC Leads</p>
                            <h4 class="mb-0 text-pink">{{ $total_ppc_leads }}</h4>
                        </div>
                        <div class="ms-auto widget-icon bg-pink text-white">
                            <i class="fa-solid fa-people-group"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="card-title d-flex align-items-center">
                        <h5 class="">TopUp Requests</h5>
                    </div>
                    <hr>
                    <div class="table-responsive">
                        <table id="wallet-template-table" class="table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th class="text-uppercase ps-2 text-secondary  text-xxs font-weight-bolder opacity-7">
                                        S NO:</th>
                                    <th class="text-uppercase text-secondary  text-xxs font-weight-bolder opacity-7">
                                        TopUp Amount</th>
                                    <th class="text-uppercase text-secondary  text-xxs font-weight-bolder opacity-7">
                                        Status</th>
                                    <th class="text-uppercase text-secondary  text-xxs font-weight-bolder opacity-7">
                                        Proof Image</th>
                                    <th
                                        class="text-uppercase text-secondary text-center text-xxs font-weight-bolder opacity-7">
                                        Action</th>
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
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="card-title d-flex align-items-center">
                        <h5 class="">Transactions</h5>
                    </div>
                    <hr>
                    <div class="table-responsive">
                        <table id="transaction-table" class="table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th class="text-uppercase ps-2 text-secondary text-xxs font-weight-bolder opacity-7">
                                        S NO:</th>
                                    <th
                                        class="text-uppercase text-center text-secondary text-xxs font-weight-bolder opacity-7">
                                        Date</th>
                                    <th
                                        class="text-uppercase text-center text-secondary text-xxs font-weight-bolder opacity-7">
                                        Description</th>
                                    <th
                                        class="text-uppercase text-center text-secondary text-xxs font-weight-bolder opacity-7">
                                        Debit</th>
                                    <th
                                        class="text-uppercase text-center text-secondary text-xxs font-weight-bolder opacity-7">
                                        Credit</th>
                                    <th
                                        class="text-uppercase text-center text-secondary text-xxs font-weight-bolder opacity-7">
                                        Vat Charges</th>
                                    <th
                                        class="text-uppercase text-center text-secondary text-xxs font-weight-bolder opacity-7">
                                        Available Balance</th>
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
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="card-title d-flex align-items-center">
                        <h5 class="">All Ads</h5>
                    </div>
                    <hr>
                    <div class="table-responsive">
                        <table id="ads-template-table" class="table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th class="text-uppercase ps-2 text-secondary text-xxs font-weight-bolder opacity-7">
                                        S NO:</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Ads Title</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Type</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Status</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Amount Spend</th>
                                    <th
                                        class="text-uppercase text-secondary text-center text-xxs font-weight-bolder opacity-7">
                                        Action</th>
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

    <h5>Files</h5>
    <div class="row">
        <div class="col-md-12">
            <div class="alert border-0 bg-light-success alert-dismissible fade show" role="alert" style="display: none;">
                URL copied to clipboard!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
        <div class="col-12 col-xl-3">
            <div class="card">
                <div class="card-body border-bottom">
                </div>
                <div class="fm-menu">
                    <div class="list-group list-group-flush m-3">
                        <a href="javascript:void(0);" class="list-group-item py-1 all_files"><i
                                class='bx bx-folder me-2 text-primary'></i><span>All Files</span></a>
                        @foreach ($all_folders as $folder)
                        <a href="javascript:void(0);" class="list-group-item py-1 get_files" data-id="{{ $folder->id }}"
                            style="display: flex; align-items: center;">
                            <i class='bi bi-folder-fill me-2 text-primary'></i>
                            <input type="hidden" name="get_folder_files" value="{{ $folder->id }}">
                            <span>{{ $folder->folder_name }}</span>
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-9">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <h5 class="mb-0 folder_name">All Files</h5>
                            <input type="hidden" value="{{ $client_id }}" id="client_id">
                        </div>
                    </div>
                    <div class="table-responsive mt-3">
                        <table id="table_client_files" class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Last Modified</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="client_files">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" value="{{ $client_id }}" id="client_id">

    <h5>Leads</h5>
    <div class="row">
        <div class="col-lg-12 mx-auto">
            <div class="card">
                <div class="card-header py-3">    
                    <form action="{{ route('admin.client-management.client_leads_import', $client_id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group mb-2">
                            <label for="file">Choose file to import</label>
                            <input type="file" name="file_import" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Import</button>
                        <a href="{{ route('admin.client-management.client_leads_export', $client_id) }}"
                        class="btn btn-secondary">Export</a>
                    </form>
                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs nav-dark" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link main-tabs active" data-bs-toggle="tab" href="#allClients" role="tab"
                                aria-selected="true" data-type="allClients">
                                <div class="d-flex align-items-center">
                                    <div class="tab-title">All Clients</div>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link main-tabs" data-bs-toggle="tab" href="#newLeads" role="tab"
                                aria-selected="false" data-type="newLeads">
                                <div class="d-flex align-items-center">
                                    <div class="tab-title">New Leads</div>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link main-tabs" data-bs-toggle="tab" href="#followUps" role="tab"
                                aria-selected="false" data-type="followUps">
                                <div class="d-flex align-items-center">
                                    <div class="tab-title">Follow Ups</div>
                                </div>
                            </a>
                        </li>

                        <li class="nav-item" role="presentation">
                            <a class="nav-link main-tabs" data-bs-toggle="tab" href="#recentlyActive" role="tab"
                                aria-selected="false" data-type="recentlyActive">
                                <div class="d-flex align-items-center">
                                    <div class="tab-title">Recently Active</div>
                                </div>
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content py-3">
                        <div class="tab-pane fade active show" id="allClients" role="tabpanel">
                            <div class="row">
                                <div class="col">
                                    <div class="table-responsive" id="all-leads-responsive">
                                        <table class="table table-hover mb-0" id="allLeads-table">
                                            <thead>
                                                <tr>
                                                    <th scope="col">NAME</th>
                                                    <th scope="col">DETAILS</th>
                                                    <th scope="col">LAST ACTIVITY</th>
                                                    <th scope="col">DATE ADDED</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                            </tbody>
                                        </table>
                                    </div>
                                    <br>

                                    <div class="text-center p-5" id="all_leads_empty">
                                        <i class="fa-solid fa-users" style="
                                        font-size: 25px;
                                        color: #14a2b8;
                                    "></i>
                                        <h5>Welcome to your Client List</h5>
                                        <p>New Leads From Facebook, your website, and other integrations will appear
                                            here,alongside any contacts added via your mobile app</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="newLeads" role="tabpanel">
                            <div class="row">
                                <div class="col">
                                    <div class="table-responsive" id="new-leads-responsive">
                                        <table class="table table-hover mb-0" id="newLead-table">
                                            <thead>
                                                <tr>
                                                    <th scope="col">NAME</th>
                                                    <th scope="col">SOURCE</th>
                                                    <th scope="col">DETAILS</th>
                                                    <th scope="col">DATE ADDED</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>

                                    <br>
                                    <div class="text-center p-5" id="new_leads_empty">
                                        <i class="fa-solid fa-user-slash" style="
                                            font-size: 25px;
                                            color: #14a2b8;
                                        "></i>
                                        <h5>No new leads</h5>
                                        <p>You don't have any new leads right now
                                            You can connect to your website and Facebook Lead Ads to automatically receive
                                            new leads in your Privyr account</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="followUps" role="tabpanel">
                            <div class="row">
                                <div class="col col-lg-10">
                                    <a href="javascript:void(0)" class="nav-link get-follow-up" data-type="due_today">
                                        <div class="bg-light-info rounded py-2">
                                            <i class="fa-regular fa-calendar" style="
                                            margin-left: 10px;
                                        "></i>
                                            <span><strong>Due Today {{ $due_today }}</strong></span>
                                        </div>
                                    </a>
                                </div>
                                <div class="col">
                                    <a href="javascript:void(0)" class="nav-link get-follow-up" data-type="up_comming">
                                        <div
                                            class="d-flex justify-content-around align-items-center text-dark bg-light rounded py-2">
                                            <i class="fa-solid fa-calendar-day"></i>
                                            <span><strong>{{ $up_coming }}</strong></span>
                                        </div>
                                    </a>
                                </div>
                                <div class="col">
                                    <a href="javascript:void(0)" class="nav-link get-follow-up" data-type="over_due">
                                        <div
                                            class="d-flex justify-content-around align-items-center text-danger bg-light rounded py-2">
                                            <i class="fa-regular fa-calendar-xmark"></i>
                                            <span><strong>{{ $over_due }}</strong></span>
                                        </div>
                                    </a>
                                </div>
                                <div class="col">
                                    <a href="javascript:void(0)" class="nav-link get-follow-up" data-type="due_someday">
                                        <div
                                            class="d-flex justify-content-around align-items-center text-secondary bg-light rounded py-2">
                                            <i class="fa-regular fa-calendar"></i>
                                            <span><strong>{{ $due_someday }}</strong></span>
                                        </div>
                                    </a>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col">
                                    <div class="table-responsive" id="followup-responsive">
                                        <table class="table table-hover mb-0" id="followup-table">
                                            <thead>
                                                <tr>
                                                    <th scope="col">FOLLOW UPS</th>
                                                    <th scope="col">NAME</th>
                                                    <th scope="col">DETAILS</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                        <br>
                                    </div>

                                    <div class="text-center p-5" id="followup_empty" style="display: none;">
                                        <i class="fa-regular fa-calendar" style="
                                        font-size: 25px;
                                    "></i>
                                        <h5>No follow ups due today</h5>
                                        <p>Set follow ups to plan what's next for each client - have coffee, schedule a
                                            meeting, or anything else to keep things going</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="recentlyActive" role="tabpanel">
                            <div class="row">
                                <div class="col">
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0">
                                            <thead>
                                                <tr>
                                                    <th scope="col">NAME</th>
                                                    <th scope="col">DETAILS</th>
                                                    <th scope="col">VIEWED ITEM</th>
                                                    <th scope="col">LAST VIEWED</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="text-center p-5">
                                        <i class="fa-solid fa-user-slash" style="
                                        font-size: 25px;
                                        color: #14a2b8;
                                    "></i>
                                        <h5>No recently active clients</h5>
                                        <p>None of your links were opened in the last 7 days <br>
                                            Clients will appear here once they open a file or page link that you share with
                                            them</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if (
        !empty($user_calender) && isset($user_calender->google_access_token) &&
        !empty($user_calender->google_access_token)
    )
        <div class="row mb-2">
            <div class="col-6 col-lg-6">
                <h5>Meetings</h5>
            </div>
            <div class="col-6 col-lg-6">
                <a href="javascript:void(0);" class="float-end btn btn-primary add-event">Add Event</a>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <div id='calendar'></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <h5>Meetings</h5>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <div>
                                <h5 class="text-center">Client Google Calender Not Connected</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="modal fade" id="AddClientEventModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Event</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.client-management.event_save') }}" method="post" id="addEventForm">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="client_id" value="{{ $client_id }}">
                        <div class="form-group mb-3">
                            <label for="">Title<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="title" placeholder="Enter Event Title" required>
                        </div>

                        <div class="form-group mb-3">
                            <label>Date <span class="text-danger">*</span></label>
                            <input type="date" name="date" class="form-control" value="" required>
                        </div>

                        <div class="form-group mb-3">
                            <label>Start Time <span class="text-danger">*</span></label>
                            <input type="time" name="start_time" class="form-control" value="">
                        </div>

                        <div class="form-group mb-3">
                            <span>End Time <span class="text-danger">*</span></span>
                            <input type="time" name="end_time" class="form-control" value="">
                        </div>

                        <div class="form-group mb-3">
                            <label for="">Event Description<span class="text-danger">*</span></label>
                            <textarea name="event_description" cols="30" rows="5" class="form-control"
                                placeholder="Event Description" required></textarea>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary form-submit-btn">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="changeTopUpStatus" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Change Top Up Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.client-management.change-status') }}" method="post" id="ajaxForm">
                    @csrf
                    <input type="hidden" id="wallet_id" name="wallet_id">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="topup_status">Status</label>
                            <select name="topup_status" id="topup_status" class="form-control" required>
                                <option value="">Select Status</option>
                                <option value="pending">Pending</option>
                                <option value="approve">Approve</option>
                                <option value="rejected">Reject</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary form-submit-btn">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="adsDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ads Detail</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="">Title</label>
                            <input type="text" readonly id="title" class="form-control" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="">Spend Amount</label>
                            <input type="text" readonly id="amount" class="form-control" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="type">status</label>
                            <input type="text" readonly id="status" class="form-control" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="">Discord Link</label>
                            <input type="url" readonly id="discordLink" class="form-control" required>
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="type">Type</label>
                            <input type="text" readonly id="type" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="changeAdsStatus" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Change Ads Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.client-management.change-ads-status') }}" method="post" id="ajaxFormAds">
                    @csrf
                    <input type="hidden" id="ads_id" name="ads_id">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="ads_status">Status</label>
                            <select name="ads_status" id="ads_status" class="form-select" required>
                                <option value="">Select Status</option>
                                <option value="pending">Pending</option>
                                <option value="running">Running</option>
                                <option value="reject">Reject</option>
                                <option value="complete">Complete</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary form-submit-btn">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('front') }}/assets/plugins/datatable/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('front') }}/assets/plugins/datatable/js/dataTables.bootstrap5.min.js"></script>
    <script src="{{ asset('front') }}/assets/js/table-datatable.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.0/fullcalendar.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.8/clipboard.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
                $('#calendar').fullCalendar({
                    header: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'month,agendaWeek,agendaDay'
                    },
                    events: {
                        url: "{{ route('admin.client-management.get_events') }}",
                        method: 'GET',
                        data: {
                            id: $('#client_id').val()
                        },
                        error: function() {
                            // alert('there was an error while fetching events!');
                        },
                    },
                    eventRender: function(eventObj, $el) {
                        var dateTimeFormat = 'MMMM D, YYYY h:mm A';
                        var popoverContent = '';
                        popoverContent += 'Start: ' + eventObj.start.format(dateTimeFormat) + '<br>';
                        if (eventObj.end) {
                            popoverContent += 'End: ' + eventObj.end.format(dateTimeFormat) + '<br>';
                        }
                        if (eventObj.description) {
                            popoverContent += 'Description: <em>' + eventObj.description + '</em>';
                        }

                        $el.popover({
                            title: eventObj.title,
                            content: popoverContent,
                            trigger: 'hover',
                            placement: 'top',
                            container: 'body',
                            html: true // Enable HTML content in popover
                        });
                    }
                });
            });

            $(document).ready(function() {
                validations = $("#addEventForm").validate();
                $('#addEventForm, #ajaxForm, #ajaxFormAds').submit(function(e) {
                    e.preventDefault();
                    validations = $("#addEventForm").validate();
                    if (validations.errorList.length != 0) {
                        return false;
                    }
                    var url = $(this).attr('action');
                    var param = new FormData(this);
                    my_ajax(url, param, 'post', function(res) {}, true);
                });
            });

            getAllAds();
            getTopUps();
            allLeads();
            newLeads();
            load_client_files_list();
            getTransactions();

            function getTransactions() {
                var clientId = $('#client_id').val();
                if ($.fn.DataTable.isDataTable('#transaction-table')) {
                    $('#transaction-table').DataTable().destroy();
                }
                $('#transaction-table').DataTable({
                    processing: true,
                    serverSide: true,
                    "order": [
                        [0, "desc"]
                    ],
                    "pageLength": 10,
                    "lengthMenu": [10, 50, 100, 150, 500],
                    ajax: {
                        url: "{{ route('admin.client-management.transactions') }}",
                        data: function(d) {
                            d.search = $('#transaction-table').DataTable().search();
                            // $('#transaction-table input[type="search"]').val();
                            d.id = clientId
                        },
                    },
                    columns: [{
                            data: null,
                            render: function(data, type, row, meta) {
                                return meta.row + 1;
                            },
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'date',
                            name: 'date',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'trans_type',
                            name: 'trans_type',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'debit',
                            name: 'debit',
                            orderable: false,
                            searchable: true
                        },
                        {
                            data: 'credit',
                            name: 'credit',
                            orderable: true,
                            searchable: false
                        },
                        {
                            data: 'vat_charges',
                            name: 'vat_charges',
                            orderable: true,
                            searchable: false
                        },
                        {
                            data: 'available_balance',
                            name: 'available_balance',
                            orderable: true,
                            searchable: false
                        },
                    ],
                });
            }

            $(document).on('click', '.get_files', function() {
                let folder_id = $(this).data('id');
                var data = {
                    '_token': "{{ csrf_token() }}",
                    folder_id: folder_id
                };
                $.ajax({
                    url: "{{ route('admin.client-management.get_client_files') }}",
                    type: 'POST',
                    data: data,
                    success: function(response) {
                        console.log(response);
                        $('#folder_id').val(null);
                        $('#main_folder_id').val(null);
                        $('.folder_name').text(response.folder_name.folder_name);
                        $('#folder_id').val(response.folder_name.id);
                        $('#client_files').html(response.body);
                    },
                    error: function(xhr, status, error) {}
                });
            });

            $(document).on('click', '.all_files', function() {
                load_client_files_list();
            });

            $(document).on('click', '.add-event', function() {
                let clientId = $('#client_id').val();
                $('#AddClientEventModal').modal('show');

            });

            $(document).on('click', '.change-topup-status', function() {
                let clientId = $('#client_id').val();
                let status = $(this).data('status');
                let id = $(this).data('id');
                $('#topup_status').val(status);
                $('#wallet_id').val(id);
                $('#changeTopUpStatus').modal('show');

            });

            function load_client_files_list() {
                let client_id = $('#client_id').val();
                var data = {
                    '_token': "{{ csrf_token() }}",
                    client_id: client_id
                };
                $.ajax({
                    url: "{{ route('admin.client-management.get_client_files') }}",
                    type: 'POST',
                    data: data,
                    success: function(response) {
                        $('#folder_id').val(null);
                        $('#main_folder_id').val(null);
                        if (response.folder_name.folder_name) {
                            $('.folder_name').text(response.folder_name.folder_name);
                            $('#folder_id').val(response.folder_name.id);
                        } else {
                            $('.folder_name').text(response.folder_name);
                            $('#main_folder_id').val(response.folder_id);
                        }
                        $('#client_files').html(response.body);
                    },
                    error: function(xhr, status, error) {}
                });
            }

            $(document).on('click', '.copy-button', function() {
                var clipboard = new ClipboardJS('.copy-button');
                clipboard.on('success', function(e) {
                    var alertElement = $('.alert');
                    alertElement.show();
                    setTimeout(function() {
                        alertElement.hide();
                    }, 3000);
                    e.clearSelection();
                });
                clipboard.on('error', function(e) {
                    console.error('Copy failed: ', e);
                });
            });

            $(document).on('click', '.main-tabs', function() {
                let type = $(this).data('type');
                if (type == 'allClients') {
                    allLeads();
                } else if (type == 'newLeads') {
                    newLeads();
                } else if (type == 'followUps') {
                    followUpsDueToday();
                }
            });

            $(document).on('click', '.get-follow-up', function() {
                if ($.fn.DataTable.isDataTable('#followup-table')) {
                    $('#followup-table').DataTable().destroy();
                }
                var clientId = $('#client_id').val();
                let type = $(this).data('type');
                // $('#loader').show();
                // $('#followUps').html('<div id="loader"></div>');
                $.ajax({
                    type: "GET",
                    url: "{{ route('admin.client-management.get_follow_ups') }}",
                    data: {
                        type,
                        'is_html': true,
                        id: clientId
                    },
                    dataType: "json",
                    beforeSend: function() {
                        // $('#loader').show();
                        // $('#followUps').html('<div class="p-5"><div id="loader"></div></div>');
                    },
                    complete: function() {
                        // $('#loader').hide();
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        // $('#loader').hide();
                        ajaxErrorHandling(jqXHR, errorThrown);
                    },
                    success: function(res) {
                        $('#followUps').html(res.template);
                        switch (res.type) {
                            case 'due_today':
                                followUpsDueToday();
                                break;
                            case 'up_comming':
                                followUpsUpComming();
                                break;
                            case 'over_due':
                                followUpsOverDue();
                                break;
                            default:
                                followUpsDueSomeday();
                                break;
                        }
                    }
                });
            });

            $(document).on('click', '.view_detail', function() {
                let data = $(this).data('data');
                let title = data.adds_title;
                let email = data.email;
                let amount = data.spend_amount;
                let discord_link = data.discord_link;
                let type = data.type;
                let status = data.status;
                let description = data.description;

                $('#title').val(title);
                $('#amount').val(amount);
                $('#discordLink').val(discord_link);
                $("#type").val(ads_type_text(type));
                $("#status").val(ads_status_text(status));
                $('#adsDetailModal').modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $('#adsDetailModal').modal('show');
            });

            function ads_status_text(status) {
                let text = '';
                if (status == "pending") {
                    text = 'Pending';
                } else if (status == "running") {
                    text = 'Running';
                } else if (status == "complete") {
                    text = 'Complete';
                } else {
                    text = 'Rejected';
                }
                return text;
            }

            function ads_type_text(type) {
                let texts = [];

                type.split(",").forEach(function(item) {
                    switch (item.trim()) {
                        case "3in1_valuation":
                            texts.push('3 in 1 Valuation');
                            break;
                        case "hbd_valuation":
                            texts.push('HBD Valuation');
                            break;
                        case "condo_valuation":
                            texts.push('Condo Valuation');
                            break;
                        case "landed_valuation":
                            texts.push('Landed Valuation');
                            break;
                        case "rental_valuation":
                            texts.push('Rental Valuation');
                            break;
                        case "post_launch_generic":
                            texts.push('Post Launch Generic');
                            break;
                        case "executive_launch_generic":
                            texts.push('Executive Launch Generic');
                            break;
                    }
                });

                return texts;
            }

            $(document).on('click', '.change-ads-status', function() {
                let status = $(this).data('status');
                let id = $(this).data('id');
                $('#ads_status').val(status);
                $('#ads_id').val(id);
                $('#changeAdsStatus').modal('show');

            });

            function getTopUps() {
                var clientId = $('#client_id').val();
                if ($.fn.DataTable.isDataTable('#wallet-template-table')) {
                    $('#wallet-template-table').DataTable().destroy();
                }
                $('#wallet-template-table').DataTable({
                    processing: true,
                    serverSide: true,
                    "order": [
                        [0, "desc"]
                    ],
                    "pageLength": 10,
                    "lengthMenu": [10, 50, 100, 150, 500],
                    ajax: {
                        url: "{{ route('admin.client-management.get_topups') }}",
                        data: function(d) {
                            d.search = $('input[type="search"]').val(),
                                d.id = clientId
                        }
                    },
                    columns: [{
                            data: null,
                            render: function(data, type, row, meta) {
                                return meta.row + 1;
                            },
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'topup_amount',
                            name: 'topup_amount',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'status',
                            name: 'status',
                            orderable: false,
                            searchable: true
                        },
                        {
                            data: 'slip',
                            name: 'slip',
                            orderable: true,
                            searchable: false
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: true,
                            searchable: false
                        },
                    ],
                });
            }

            function getAllAds() {
                var clientId = $('#client_id').val();
                if ($.fn.DataTable.isDataTable('#ads-template-table')) {
                    $('#ads-template-table').DataTable().destroy();
                }
                $('#ads-template-table').DataTable({
                    processing: true,
                    serverSide: true,
                    "order": [
                        [0, "desc"]
                    ],
                    "pageLength": 10,
                    "lengthMenu": [10, 50, 100, 150, 500],
                    ajax: {
                        url: "{{ route('admin.client-management.get_ads') }}",
                        data: function(d) {
                            d.search = d.search = $('#ads-template-table').DataTable().search();
                            // $('input[type="search"]').val();
                            d.id = clientId
                        },
                    },
                    columns: [{
                            data: null,
                            render: function(data, type, row, meta) {
                                return meta.row + 1;
                            },
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'adds_title',
                            name: 'adds_title',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'type',
                            name: 'type',
                            orderable: true,
                            searchable: false
                        },
                        {
                            data: 'status',
                            name: 'status',
                            orderable: true,
                            searchable: false
                        },
                        {
                            data: 'spend_amount',
                            name: 'spend_amount',
                            orderable: true,
                            searchable: false
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: true,
                            searchable: false
                        },
                    ],
                });
            }

            function allLeads() {
                var clientId = $('#client_id').val();
                if ($.fn.DataTable.isDataTable('#allLeads-table')) {
                    $('#allLeads-table').DataTable().destroy();
                }
                $('#allLeads-table').DataTable({
                    processing: true,
                    serverSide: true,
                    "order": [
                        [0, "desc"]
                    ],
                    "pageLength": 10,
                    "lengthMenu": [10, 50, 100, 150, 500],
                    ajax: {
                        url: "{{ route('admin.client-management.get_lead') }}",
                        type: 'GET',
                        data: function(d) {
                            d.search = $('#allLeads-table').DataTable().search();
                            // $('input[type="search"]').val(),
                            d.type = 'all_leads',
                                d.id = clientId
                        }
                    },
                    columns: [{
                            data: 'name',
                            name: 'name',
                            orderable: false,
                            searchable: true
                        },
                        {
                            data: 'details',
                            name: 'details',
                            orderable: false,
                            searchable: true
                        },
                        {
                            data: 'latest_activity',
                            name: 'latest_activity',
                            orderable: false,
                            searchable: true
                        },
                        {
                            data: 'date_added',
                            name: 'date_added',
                            orderable: true,
                            searchable: false
                        },
                    ],
                    rowCallback: function(row, data, index) {
                        if (data.status == "new_lead") {
                            $('td', row).addClass('new_lead');
                            $('td > a', row).addClass('new_lead');
                        }
                    },
                    drawCallback: function(settings) {
                        // Check if the DataTable has no data
                        if (settings.json.recordsTotal === 0) {
                            // Show the additional div
                            $('#all_leads_empty').show();
                            $('#all-leads-responsive').hide();
                        } else {
                            // Hide the additional div if there is data
                            $('#all_leads_empty').hide();
                            $('#all-leads-responsive').show();
                        }
                    },
                });
            }

            function newLeads() {
                var clientId = $('#client_id').val();
                if ($.fn.DataTable.isDataTable('#newLead-table')) {
                    $('#newLead-table').DataTable().destroy();
                }
                $('#newLead-table').DataTable({
                    processing: true,
                    serverSide: true,
                    "order": [
                        [0, "desc"]
                    ],
                    "pageLength": 10,
                    "lengthMenu": [10, 50, 100, 150, 500],
                    ajax: {
                        url: "{{ route('admin.client-management.get_lead') }}",
                        data: function(d) {
                            d.search = $('#newLead-table').DataTable().search();
                            // $('input[type="search"]').val(),
                            d.type = 'new_leads',
                                d.id = clientId
                        }
                    },
                    columns: [{
                            data: 'name',
                            name: 'name',
                            orderable: false,
                            searchable: true
                        },
                        {
                            data: 'source',
                            name: 'name',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'details',
                            name: 'details',
                            orderable: false,
                            searchable: true
                        },
                        {
                            data: 'date_added',
                            name: 'date_added',
                            orderable: true,
                            searchable: false
                        },
                    ],
                    rowCallback: function(row, data, index) {
                        if (data.status == "new_lead") {
                            $('td', row).addClass('new_lead');
                            $('td > a', row).addClass('new_lead');
                        }
                    },
                    drawCallback: function(settings) {
                        // Check if the DataTable has no data
                        if (settings.json.recordsTotal === 0) {
                            // Show the additional div
                            $('#new_leads_empty').show();
                            $('#new-leads-responsive').hide();
                        } else {
                            // Hide the additional div if there is data
                            $('#new_leads_empty').hide();
                            $('#new-leads-responsive').show();
                        }
                    },
                });
            }

            function followUpsDueToday() {
                if ($.fn.DataTable.isDataTable('#followup-table')) {
                    $('#followup-table').DataTable().destroy();
                }
                var clientId = $('#client_id').val();
                $('#followup-table').DataTable({
                    processing: true,
                    serverSide: true,
                    "pageLength": 10,
                    "lengthMenu": [10, 50, 100, 150, 500],
                    ajax: {
                        url: "{{ route('admin.client-management.get_follow_ups') }}",
                        data: function(d) {
                            d.search = $('#followup-table').DataTable().search();
                            // $('input[type="search"]').val(),
                            d.type = 'due_today',
                                d.id = clientId
                        }
                    },
                    columns: [{
                            data: 'follow_ups',
                            name: 'follow_ups',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'name',
                            name: 'name',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'details',
                            name: 'details',
                            orderable: false,
                            searchable: false
                        },
                    ],
                    rowCallback: function(row, data, index) {
                        if (data.status == "new_lead") {
                            $('td', row).addClass('new_lead');
                            $('td > a', row).addClass('new_lead');
                        }
                    },
                    drawCallback: function(settings) {
                        // Check if the DataTable has no data
                        if (settings.json.recordsTotal === 0) {
                            // Show the additional div
                            $('#followup_empty').show();
                            $('#followup-responsive').hide();
                        } else {
                            // Hide the additional div if there is data
                            $('#followup_empty').hide();
                            $('#followup-responsive').show();
                        }
                    },
                });
            }

            function followUpsUpComming() {
                if ($.fn.DataTable.isDataTable('#followup-table')) {
                    $('#followup-table').DataTable().destroy();
                }
                var clientId = $('#client_id').val();
                $('#followup-table').DataTable({
                    processing: true,
                    serverSide: true,
                    "pageLength": 10,
                    "lengthMenu": [10, 50, 100, 150, 500],
                    ajax: {
                        url: "{{ route('admin.client-management.get_follow_ups') }}",
                        data: function(d) {
                            d.search = $('#followup-table').DataTable().search();
                            // $('input[type="search"]').val(),
                            d.type = 'up_comming',
                                d.id = clientId
                        }
                    },
                    columns: [{
                            data: 'follow_ups',
                            name: 'follow_ups',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'name',
                            name: 'name',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'details',
                            name: 'details',
                            orderable: false,
                            searchable: false
                        },
                    ],
                    rowCallback: function(row, data, index) {
                        if (data.status == "new_lead") {
                            $('td', row).addClass('new_lead');
                            $('td > a', row).addClass('new_lead');
                        }
                    },
                    drawCallback: function(settings) {
                        // Check if the DataTable has no data
                        if (settings.json.recordsTotal === 0) {
                            // Show the additional div
                            $('#followup_empty').show();
                            $('#followup-responsive').hide();
                        } else {
                            // Hide the additional div if there is data
                            $('#followup_empty').hide();
                            $('#followup-responsive').show();
                        }
                    },
                });
            }

            function followUpsOverDue() {
                if ($.fn.DataTable.isDataTable('#followup-table')) {
                    $('#followup-table').DataTable().destroy();
                }
                var clientId = $('#client_id').val();
                $('#followup-table').DataTable({
                    processing: true,
                    serverSide: true,
                    "pageLength": 10,
                    "lengthMenu": [10, 50, 100, 150, 500],
                    ajax: {
                        url: "{{ route('admin.client-management.get_follow_ups') }}",
                        data: function(d) {
                            d.search = $('#followup-table').DataTable().search();
                            // $('input[type="search"]').val(),
                            d.type = 'over_due',
                                d.id = clientId
                        }
                    },
                    columns: [{
                            data: 'follow_ups',
                            name: 'follow_ups',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'name',
                            name: 'name',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'details',
                            name: 'details',
                            orderable: false,
                            searchable: false
                        },
                    ],
                    rowCallback: function(row, data, index) {
                        if (data.status == "new_lead") {
                            $('td', row).addClass('new_lead');
                            $('td > a', row).addClass('new_lead');
                        }
                    },
                    drawCallback: function(settings) {
                        // Check if the DataTable has no data
                        if (settings.json.recordsTotal === 0) {
                            // Show the additional div
                            $('#followup_empty').show();
                            $('#followup-responsive').hide();
                        } else {
                            // Hide the additional div if there is data
                            $('#followup_empty').hide();
                            $('#followup-responsive').show();
                        }
                    },
                });
            }

            function followUpsDueSomeday() {
                if ($.fn.DataTable.isDataTable('#followup-table')) {
                    $('#followup-table').DataTable().destroy();
                }
                var clientId = $('#client_id').val();
                $('#followup-table').DataTable({
                    processing: true,
                    serverSide: true,
                    "pageLength": 10,
                    "lengthMenu": [10, 50, 100, 150, 500],
                    ajax: {
                        url: "{{ route('admin.client-management.get_follow_ups') }}",
                        data: function(d) {
                            d.search = $('#followup-table').DataTable().search();
                            // $('input[type="search"]').val(),
                            d.type = 'due_someday',
                                d.id = clientId
                        }
                    },
                    columns: [{
                            data: 'follow_ups',
                            name: 'follow_ups',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'name',
                            name: 'name',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'details',
                            name: 'details',
                            orderable: false,
                            searchable: false
                        },
                    ],
                    rowCallback: function(row, data, index) {
                        if (data.status == "new_lead") {
                            $('td', row).addClass('new_lead');
                            $('td > a', row).addClass('new_lead');
                        }
                    },
                    drawCallback: function(settings) {
                        // Check if the DataTable has no data
                        if (settings.json.recordsTotal === 0) {
                            // Show the additional div
                            $('#followup_empty').show();
                            $('#followup-responsive').hide();
                        } else {
                            // Hide the additional div if there is data
                            $('#followup_empty').hide();
                            $('#followup-responsive').show();
                        }
                    },
                });
            }
    </script>
@endpush