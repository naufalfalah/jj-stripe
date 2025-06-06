@extends('layouts.admin')
@section('page-css')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link href="{{ asset('front/assets/plugins/fileUpload/fileUpload.css') }}" rel="stylesheet" />
<link href="{{ asset('front') }}/assets/plugins/datatable/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
<style>
    #loader {
        position: absolute;
        left: 50%;
        top: 70%;
        z-index: 1;
        width: 80px;
        height: 80px;
        margin: -76px 0 0 -76px;
        border: 12px solid #f3f3f3;
        border-radius: 50%;
        border-top: 12px solid #3498db;
        -webkit-animation: spin 2s linear infinite;
        animation: spin 2s linear infinite;
    }

    @-webkit-keyframes spin {
        0% {
            -webkit-transform: rotate(0deg);
        }

        100% {
            -webkit-transform: rotate(360deg);
        }
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    .pointer-cursor {
        cursor: pointer;
    }


    @media(max-width: 767px) {
        #btn-align {
            display: flex !important;
            flex-flow: column-reverse !important;
        }

        a.btn.btn-dark.float-end.copy-zapier-url,
        a.add-new-client,
        a.upload-file {
            margin-left: 0px !important;
            margin-right: 0px !important;
            margin-bottom: 15px !important;
            margin-top: 10px !important;
        }
    }

    .new_lead {
        color: rgba(200, 99, 255, 1) !important;
    }
</style>
<style>
    .select-picture-container .select-picture-box {
        height: 90px !important;
    }

    .select-picture-container {
        grid-template-columns: 100px 100px 100px 100px !important;
    }

    .select-picture-container .select-picture-box .select-picture-content .select-picture-option {
        width: 15px !important;
        height: 15px !important;
    }

    .select-picture-container .select-picture-box img {
        width: 40px !important;
        height: 40px !important;
    }
</style>
<style>
    .modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal-content {
        background: white;
        padding: 20px;
        border-radius: 5px;
        text-align: center;
    }
</style>
@endsection

@section('content')
@if (null !== request()->input('client') && !empty(request()->input('client')))
<div class="row">
    <div class="col-lg-12 mx-auto pb-2" id="btn-align">
        <a href="javascript:void(0);" class="btn btn-dark float-end copy-zapier-url"
            data-url="{{ route('webhook.save_data', request()->input('client')) }}" style="margin-left: 5px;"><img
                src="{{ asset('front') }}/assets/images/webhook_logo.svg" alt=""> WEBHOOK</a>

        <a href="javascript:void(0);" class="btn btn-primary float-end add-new-client">+ ADD NEW CLIENT</a>

        <a href="javascript:void(0);" class="btn btn-info text-white float-end upload-file"
            style="margin-right: 5px;"><i class="fa-solid fa-file-arrow-up"></i> UPLOAD FILE</a>

        <div class="btn-group float-end">
            <button type="button" class="btn btn-primary split-bg-primary dropdown-toggle dropdown-toggle-split"
                data-bs-toggle="dropdown" style="margin-right: 5px; border-radius:0.375rem;"> <span
                    class="">Groups
                </span>
            </button>
            <div class="dropdown-menu dropdown-menu-right dropdown-menu-lg-end">
                <a class="dropdown-item add_new_group" href="javascript:void(0);">Create New Group</a>
                <a class="dropdown-item all_groups" href="javascript:void(0);">All Groups</a>
            </div>
        </div>
    </div>
</div>
@endif

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-12 col-md-6 mb-2">
                        <form method="get" action="" class="ajaxFormClient">
                            <label for="">All Client</label>
                            <input type="hidden" value="{{ request()->input('client') }}" id="client_id">
                            <select name="client" id="client" class="form-control single-select" required>
                                <option value="">All Client</option>
                                @foreach ($clients as $val)
                                <option value="{{ $val->hashid }}"
                                    {{ request()->input('client') == $val->hashid ? 'selected' : '' }}>
                                    {{ $val->client_name }} ( {{ $val->email }} )
                                </option>
                                @endforeach
                            </select>
                        </form>
                    </div>

                    <div class="col-12 col-md-6 mb-2">
                        <label for="">Lead Source</label>
                        <select name="lead_source" id="lead_source" class="form-control single-select" required>
                            <option value="" selected>Select Lead Source</option>
                            @foreach ($lead_source as $source)
                            <option value="{{ $source->id }}"> {{ $source->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 col-md-6 mb-2">
                        <label for="">Select DateRange</label>
                        <input type="text" id="searchRange" name="daterange" autocomplete="off" required
                            class="form-control" />
                        <input type="hidden" name="date_range_input" id="date_range_input">
                    </div>

                    <div class="col-12 col-md-6 mb-2">
                        <label for="">Lead Activity</label>
                        <select name="lead_activity" id="lead_activity" class="form-control single-select" required>
                            <option value="" selected>Any Activity</option>
                            <option value="email">Email</option>
                            <option value="phone">Phone Call</option>
                            <option value="message">WhatsApp Message</option>
                            <option value="file">File Send</option>
                        </select>
                    </div>

                    <div class="col-12 col-md-6 mb-2">
                        <label for="">ISA Status</label>
                        <select name="isa_status" id="isa_status" class="form-control single-select" required>
                            <option value="" selected>All Leads</option>
                            <option value="assign">Assigned</option>
                            <option value="unassign">Un Assigned</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12 mx-auto">
        <div class="card">
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
                        <a class="nav-link main-tabs" data-bs-toggle="tab" href="#uncontactedLeads" role="tab"
                            aria-selected="false" data-type="uncontactedLeads">
                            <div class="d-flex align-items-center">
                                <div class="tab-title">Uncontacted</div>
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
                            aria-selected="false" data-type="recentlyViewed">
                            <div class="d-flex align-items-center">
                                <div class="tab-title">Recently Viewed Content</div>
                            </div>
                        </a>
                    </li>
                </ul>
                <div class="tab-content py-3">
                    <div class="tab-pane fade active show" id="allClients" role="tabpanel">

                        <!-- Start: Search and Action Buttons Section -->
                        <div class="d-flex align-items-center mb-3">
                            <div class="input-group me-3">
                                <input type="hidden" name="search_group" id="search_group">
                                <label class="input-group-text" for="all-groups"><i
                                        class="fa-solid fa-search"></i></label>
                                <select name="" id="all-groups" class="form-select single-select">
                                    <option value="">Search with group</option>
                                    @foreach ($client_groups as $group)
                                    <option value="{{ $group->id }}">{{ $group->group_title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="btn-group" role="group">
                                <a href="javascript:void(0);" class="btn btn-primary text-nowrap" id="add_leads_discord">Send Lead To Discord</a>
                                <a href="javascript:void(0);" class="btn btn-primary text-nowrap" id="add_leads_spam">Mark as Spam</a>
                                <a href="javascript:void(0);" class="btn btn-primary text-nowrap" id="assign_leads_btn">Leads Assign To ISA</a>
                            </div>
                        </div>
                        <!-- End: Search and Action Buttons Section -->
                    
                        <div class="row">
                            <div class="col">
                                <div class="table-responsive" id="all-leads-responsive">
                                    <table class="table table-hover mb-0" id="allLeads-table">
                                        <thead>
                                            <tr>
                                                <th></th>
                                                <th scope="col">ISA Status</th>
                                                <th scope="col">CLIENT NAME</th>
                                                <th scope="col">NAME</th>
                                                <th scope="col">EMAIL</th>
                                                <th scope="col">PHONE</th>
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

                                <div class="text-center p-5" style="display: none !important;" id="all_leads_empty">
                                    <i class="fa-solid fa-users" style="font-size: 25px; color: #14a2b8;"></i>
                                    <h5>Welcome to your Client List</h5>
                                    <p>New Leads From Facebook, your website, and other integrations will appear
                                        here,alongside any contacts added via your mobile app</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="uncontactedLeads" role="tabpanel">
                        <div class="row">
                            <div class="col">
                                <input type="hidden" id="uncontacted_leads_count"
                                    value="{{ $uncontacted_leads_count }}">
                                @if ($uncontacted_leads_count > 0)
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
                                @else
                                <br>
                                <div class="text-center p-5" id="new_leads_empty">
                                    <i class="fa-solid fa-user-slash" style="font-size: 25px;color: #14a2b8;"></i>
                                    <h5>No Uncontacted leads</h5>
                                    <p>You don't have any Uncontacted leads right now
                                        You can connect to your website and Facebook Lead Ads to automatically
                                        receive
                                        Uncontacted leads in your Privyr account</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="followUps" role="tabpanel">
                        <div class="row">
                            <div class="col col-lg-10">
                                <a href="javascript:void(0)" class="nav-link get-follow-up" data-type="due_today">
                                    <div class="bg-light-info rounded py-2">
                                        <i class="fa-regular fa-calendar"
                                            style="margin-left: 10px;
                                        "></i>
                                        <span><strong>Due Today {{ @$due_today }}</strong></span>
                                    </div>
                                </a>
                            </div>
                            <div class="col">
                                <a href="javascript:void(0)" class="nav-link get-follow-up" data-type="up_comming">
                                    <div
                                        class="d-flex justify-content-around align-items-center text-dark bg-light rounded py-2">
                                        <i class="fa-solid fa-calendar-day"></i>
                                        <span><strong>{{ @$up_coming }}</strong></span>
                                    </div>
                                </a>
                            </div>
                            <div class="col">
                                <a href="javascript:void(0)" class="nav-link get-follow-up" data-type="over_due">
                                    <div
                                        class="d-flex justify-content-around align-items-center text-danger bg-light rounded py-2">
                                        <i class="fa-regular fa-calendar-xmark"></i>
                                        <span><strong>{{ @$over_due }}</strong></span>
                                    </div>
                                </a>
                            </div>
                            <div class="col">
                                <a href="javascript:void(0)" class="nav-link get-follow-up" data-type="due_someday">
                                    <div
                                        class="d-flex justify-content-around align-items-center text-secondary bg-light rounded py-2">
                                        <i class="fa-regular fa-calendar"></i>
                                        <span><strong>{{ @$due_someday }}</strong></span>
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
                                                <th scope="col">CLIENT NAME</th>
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
                                    <i class="fa-regular fa-calendar"
                                        style="
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
                                <input type="hidden" id="recently_viewed_count"
                                    value="{{ $recently_viewed_count }}">
                                @if ($recently_viewed_count > 0)
                                <div class="table-responsive" id="recently-view-responsive">
                                    <table class="table table-hover mb-0" id="recently-view-table">
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
                                @else
                                <div class="text-center p-5" id="recently-view-empty">
                                    <i class="fa-solid fa-user-slash" style="font-size: 25px;color: #14a2b8;"></i>
                                    <h5>No recently viewed content</h5>
                                    <p>None of your links were opened in the last 7 days <br>
                                        Clients will appear here once they open a file or page link that you share
                                        with
                                        them</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@if (null !== request()->input('client') && !empty(request()->input('client')))
{{-- groups crud start --}}
<div class="modal fade" id="AddNewGroup" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create New Group</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.lead-management.group_save') }}" method="post" id="add_group">
                @csrf
                <input type="hidden" name="client_id" value="{{ hashids_decode(request()->input('client')) }}">
                <div class="modal-body">
                    <input class="form-check-input" type="radio" name="group_colour" value="primary" hidden
                        id="colour_1" checked>
                    <input class="form-check-input" type="radio" name="group_colour" value="secondary" hidden
                        id="colour_2">
                    <input class="form-check-input" type="radio" name="group_colour" value="success" hidden
                        id="colour_3">
                    <input class="form-check-input" type="radio" name="group_colour" value="danger" hidden
                        id="colour_4">
                    <input class="form-check-input" type="radio" name="group_colour" value="warning" hidden
                        id="colour_5">
                    <input class="form-check-input" type="radio" name="group_colour" value="info" hidden
                        id="colour_6">
                    <input type="hidden" name="lead_id" id="lead_id">

                    <div class="form-group mb-3">
                        <label for="">Gruop Name</label>
                        <input type="text" class="form-control" name="group_name" id="group_name"
                            value="" placeholder="Enter Group Name" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="" class="mb-3">Select Colour</label>

                        <div class="row">
                            <div class="col-12 col-lg-12 col-md-12">
                                <div class="select-picture-container" id="pictureList">
                                    <div class="select-picture-box default_selected">
                                        <i class="bx bx-check"></i>
                                        <div class="selected-ribbon"></div>
                                        <img src="{{ asset('front') }}/assets/images/group_colours/primary_color.png"
                                            width="30px" height="30px" alt="-">
                                    </div>

                                    <div class="select-picture-box">
                                        <i class="bx bx-check"></i>
                                        <div class="selected-ribbon"></div>
                                        <img src="{{ asset('front') }}/assets/images/group_colours/secondary_color.png"
                                            alt="-">
                                    </div>

                                    <div class="select-picture-box">
                                        <i class="bx bx-check"></i>
                                        <div class="selected-ribbon"></div>
                                        <img src="{{ asset('front') }}/assets/images/group_colours/seccess_color.png"
                                            alt="-">
                                    </div>

                                    <div class="select-picture-box">
                                        <i class="bx bx-check"></i>
                                        <div class="selected-ribbon"></div>
                                        <img src="{{ asset('front') }}/assets/images/group_colours/danger_color.png"
                                            alt="-">
                                    </div>

                                    <div class="select-picture-box">
                                        <i class="bx bx-check"></i>
                                        <div class="selected-ribbon"></div>
                                        <img src="{{ asset('front') }}/assets/images/group_colours/warning_color.png"
                                            alt="-">
                                    </div>

                                    <div class="select-picture-box">
                                        <i class="bx bx-check"></i>
                                        <div class="selected-ribbon"></div>
                                        <img src="{{ asset('front') }}/assets/images/group_colours/info_color.png"
                                            alt="-">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary form-submit-btn">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="EditGroup" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Group</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.lead-management.group_save') }}" method="post" id="edit_group">
                @csrf
                <input type="hidden" name="client_id" value="{{ hashids_decode(request()->input('client')) }}">
                <div class="modal-body">
                    <input class="form-check-input" type="radio" name="edit_group_colour" value="primary"
                        hidden id="edit_colour_1">
                    <input class="form-check-input" type="radio" name="edit_group_colour" value="secondary"
                        hidden id="edit_colour_2">
                    <input class="form-check-input" type="radio" name="edit_group_colour" value="success"
                        hidden id="edit_colour_3">
                    <input class="form-check-input" type="radio" name="edit_group_colour" value="danger"
                        hidden id="edit_colour_4">
                    <input class="form-check-input" type="radio" name="edit_group_colour" value="warning"
                        hidden id="edit_colour_5">
                    <input class="form-check-input" type="radio" name="edit_group_colour" value="info"
                        hidden id="edit_colour_6">
                    <input type="hidden" name="edit_group_id" id="edit_group_id">

                    <div class="form-group mb-3">
                        <label for="">Gruop Name</label>
                        <input type="text" class="form-control" name="group_name" id="edit_group_name"
                            placeholder="Enter Group Name" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="" class="mb-3">Select Colour</label>

                        <div class="row">
                            <div class="col-12 col-lg-12 col-md-12">
                                <div class="select-picture-container" id="edit_pictureList">
                                    <div class="select-picture-box" id="primary">
                                        <i class="bx bx-check"></i>
                                        <div class="selected-ribbon"></div>
                                        <img src="{{ asset('front') }}/assets/images/group_colours/primary_color.png"
                                            width="30px" height="30px" alt="-">
                                    </div>

                                    <div class="select-picture-box" id="secondary">
                                        <i class="bx bx-check"></i>
                                        <div class="selected-ribbon"></div>
                                        <img src="{{ asset('front') }}/assets/images/group_colours/secondary_color.png"
                                            alt="-">
                                    </div>

                                    <div class="select-picture-box" id="success">
                                        <i class="bx bx-check"></i>
                                        <div class="selected-ribbon"></div>
                                        <img src="{{ asset('front') }}/assets/images/group_colours/seccess_color.png"
                                            alt="-">
                                    </div>

                                    <div class="select-picture-box" id="danger">
                                        <i class="bx bx-check"></i>
                                        <div class="selected-ribbon"></div>
                                        <img src="{{ asset('front') }}/assets/images/group_colours/danger_color.png"
                                            alt="-">
                                    </div>

                                    <div class="select-picture-box" id="warning">
                                        <i class="bx bx-check"></i>
                                        <div class="selected-ribbon"></div>
                                        <img src="{{ asset('front') }}/assets/images/group_colours/warning_color.png"
                                            alt="-">
                                    </div>

                                    <div class="select-picture-box" id="info">
                                        <i class="bx bx-check"></i>
                                        <div class="selected-ribbon"></div>
                                        <img src="{{ asset('front') }}/assets/images/group_colours/info_color.png"
                                            alt="-">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary form-submit-btn">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="AllGroups" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">All Groups</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="card w-100" style="box-shadow: none;">
                <div class="card-body">
                    <div class="table-responsive mt-2">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Group Title</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($client_groups as $k => $group)
                                <tr>
                                    <td> {{ $k + 1 }} </td>
                                    <td> {{ $group->group_title }} </td>
                                    <td>
                                        <a href="javascript:void(0);" class="text-warning edit-group"
                                            data-bs-toggle="tooltip" data-bs-placement="bottom"
                                            title="Edit Group" data-bs-original-title="Edit Group"
                                            aria-label="Edit" data-data="{{ $group }}"><i
                                                class="bi bi-pencil-fill"></i></a>
                                        <a class="text-danger" data-bs-toggle="tooltip"
                                            data-bs-placement="bottom" title="Delete"
                                            data-bs-original-title="Delete" aria-label="Delete"
                                            href="javascript:void(0);" onclick="ajaxRequest(this)"
                                            data-url="{{ route('admin.lead-management.delete_group', $group->id) }}"><i
                                                class="bi bi-trash-fill"></i></a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center">No Groups Found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- groups crud end --}}

<div class="modal fade" id="clientAddModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Client</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.lead-management.save') }}" method="post" id="leadForm">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="client_id" id="client_id"
                        value="{{ request()->input('client') }}">
                    <div class="form-group mb-3">
                        <label for="">Client Name<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="client_name"
                            placeholder="Enter Client NAME" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="">Email<span class="text-danger">*</span></label>
                        <input type="email" class="form-control" name="email" placeholder="Enter Email"
                            required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="">Mobile Number<span class="text-danger">*</span></label>
                        <div class="input-group mb-3">
                            <span class="input-group-text bg-white p-0" id="basic-addon1">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                    width="30" zoomAndPan="magnify" viewBox="0 0 30 30.000001"
                                    height="40" preserveAspectRatio="xMidYMid meet" version="1.0">
                                    <defs>
                                        <clipPath id="id1">
                                            <path
                                                d="M 2.675781 6.132812 L 27.355469 6.132812 L 27.355469 24.277344 L 2.675781 24.277344 Z M 2.675781 6.132812 "
                                                clip-rule="nonzero" />
                                        </clipPath>
                                        <clipPath id="id2">
                                            <path
                                                d="M 2.675781 6.132812 L 27.355469 6.132812 L 27.355469 16 L 2.675781 16 Z M 2.675781 6.132812 "
                                                clip-rule="nonzero" />
                                        </clipPath>
                                        <clipPath id="id3">
                                            <path d="M 4 6.132812 L 10 6.132812 L 10 15 L 4 15 Z M 4 6.132812 "
                                                clip-rule="nonzero" />
                                        </clipPath>
                                    </defs>
                                    <g clip-path="url(#id1)">
                                        <path fill="rgb(93.328857%, 93.328857%, 93.328857%)"
                                            d="M 27.347656 21.488281 C 27.347656 23.027344 26.121094 24.277344 24.609375 24.277344 L 5.421875 24.277344 C 3.910156 24.277344 2.683594 23.027344 2.683594 21.488281 L 2.683594 8.925781 C 2.683594 7.382812 3.910156 6.132812 5.421875 6.132812 L 24.609375 6.132812 C 26.121094 6.132812 27.347656 7.382812 27.347656 8.925781 Z M 27.347656 21.488281 "
                                            fill-opacity="1" fill-rule="nonzero" />
                                    </g>
                                    <g clip-path="url(#id2)">
                                        <path fill="rgb(92.939758%, 16.079712%, 22.349548%)"
                                            d="M 27.347656 15.207031 L 27.347656 8.925781 C 27.347656 7.382812 26.121094 6.132812 24.609375 6.132812 L 5.421875 6.132812 C 3.910156 6.132812 2.683594 7.382812 2.683594 8.925781 L 2.683594 15.207031 Z M 27.347656 15.207031 "
                                            fill-opacity="1" fill-rule="nonzero" />
                                    </g>
                                    <g clip-path="url(#id3)">
                                        <path fill="rgb(100%, 100%, 100%)"
                                            d="M 6.792969 10.671875 C 6.792969 8.867188 7.90625 7.355469 9.402344 6.945312 C 9.117188 6.875 8.816406 6.832031 8.507812 6.832031 C 6.425781 6.832031 4.738281 8.550781 4.738281 10.671875 C 4.738281 12.789062 6.425781 14.507812 8.507812 14.507812 C 8.816406 14.507812 9.117188 14.464844 9.402344 14.394531 C 7.90625 13.984375 6.792969 12.472656 6.792969 10.671875 Z M 6.792969 10.671875 "
                                            fill-opacity="1" fill-rule="nonzero" />
                                    </g>
                                    <path fill="rgb(93.328857%, 93.328857%, 93.328857%)"
                                        d="M 10.90625 7.53125 L 11.058594 8.011719 L 11.554688 8.011719 L 11.152344 8.308594 L 11.308594 8.792969 L 10.90625 8.492188 L 10.5 8.792969 L 10.65625 8.308594 L 10.253906 8.011719 L 10.75 8.011719 Z M 9.535156 12.414062 L 9.6875 12.898438 L 10.1875 12.898438 L 9.78125 13.195312 L 9.9375 13.675781 L 9.535156 13.378906 L 9.132812 13.675781 L 9.285156 13.195312 L 8.882812 12.898438 L 9.378906 12.898438 Z M 12.273438 12.414062 L 12.429688 12.898438 L 12.925781 12.898438 L 12.523438 13.195312 L 12.675781 13.675781 L 12.273438 13.378906 L 11.871094 13.675781 L 12.027344 13.195312 L 11.625 12.898438 L 12.121094 12.898438 Z M 8.847656 9.625 L 9.003906 10.105469 L 9.5 10.105469 L 9.097656 10.402344 L 9.253906 10.886719 L 8.847656 10.585938 L 8.445312 10.886719 L 8.601562 10.402344 L 8.199219 10.105469 L 8.695312 10.105469 Z M 12.960938 9.625 L 13.113281 10.105469 L 13.613281 10.105469 L 13.207031 10.402344 L 13.363281 10.886719 L 12.960938 10.585938 L 12.558594 10.886719 L 12.710938 10.402344 L 12.308594 10.105469 L 12.804688 10.105469 Z M 12.960938 9.625 "
                                        fill-opacity="1" fill-rule="nonzero" />
                                </svg>
                                +65
                            </span>
                            <input type="text" pattern="\d*" maxlength="8" required
                                oninput="this.value = this.value.replace(/[^0-9]/g, '');" class="form-control"
                                name="mobile_number" placeholder="Enter Mobile Number">
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <td>Data Key</td>
                                    <td>Data Value</td>
                                    <td>Action</td>
                                </tr>
                            </thead>
                            <tbody id="leadData_body">
                                <tr>
                                    <td>
                                        <input type="text" class="form-control" name="data[0][key]"
                                            placeholder="Enter Data Key" required>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="data[0][value]"
                                            placeholder="Enter Data Value" required>
                                    </td>
                                    <td>
                                        <a href="javascript:void(0);" class="btn btn-primary add_lead_data_tr"><i
                                                class="fa-solid fa-circle-plus"
                                                style="margin-left: 0px; vertical-align: initial;"></i></a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
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

<div class="modal fade" id="uploadFileModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload File</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="uploadFileForm" action="{{ route('admin.lead-management.import_file') }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="client_id" id="client_id" value="{{ request()->input('client') }}">
                <div class="modal-body">
                    <div id="fileUpload"></div>

                    <div class="d-flex justify-content-between mt-3">
                        <p>Support formats: XLS,XLSX</p>
                        <p>Maximum Size: 25MB</p>
                    </div>

                    <div class="card bg-light">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-8">
                                    <div style="display: flex;">
                                        <img src="{{ asset('front') }}/assets/images/excel-logo.png"
                                            alt="excel logo" style="width: 40px;">
                                        <p style="margin-left: 10px;margin-top: 10px;">Table Example</p>
                                    </div>

                                    <p style="font-size: 12px;">You can download the attached example and use them
                                        as a
                                        starting point for your own file.</p>
                                </div>
                                <div class="col-4">
                                    <a href="{{ asset('Sample Leads CSV.xlsx') }}" download
                                        class="btn btn-white mt-4 float-end">Download</a>
                                </div>
                            </div>
                        </div>
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

<div class="modal fade" id="copyZapierUrlModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Zapier Detail</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="alert alert-success alert-dismissible fade show" style="display: none;" role="alert"
                id="copy_alert">
                Webhook Url Copied.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <form action="#">
                @csrf
                <div class="modal-body">

                    <div class="input-group mb-3">

                        <input type="text" readonly class="form-control zap_url_input"
                            placeholder="Recipient's username" aria-describedby="button-addon2">
                        <button class="btn btn-outline-secondary copy-zap-url"
                            data-url="{{ route('webhook.save_data', request()->input('client')) }}"
                            type="button" id="button-addon2">Copy Url</button>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 col-12">
                            <h4>Set Up Zapier</h4>
                            <hr>
                            <h6>Method: POST</h6>
                            <h6>Payload Type: Json</h6>
                            <h6>Required Data Fields</h6>
                            <ul>
                                <li>name: <small class="text-danger">( Set lead name )</small></li>
                                <li>email: <small class="text-danger">( Set lead email )</small></li>
                                <li>mobile_number: <small class="text-danger"> ( Set lead phone number )</small>
                                </li>
                                <li>source_type: <small class="text-danger"> ( Set lead source type key )</small>
                                </li>
                            </ul>
                            <h6>Optional Fields</h6>
                            <ul>
                                <li>Fields other than required fields. <br> Example: <small
                                        class="text-danger">Country
                                        => Singapore</small></li>
                            </ul>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection

@section('page-scripts')
<script src="{{ asset('front/assets/plugins/fileUpload/fileUpload.js') }}"></script>
<script src="{{ asset('front') }}/assets/plugins/datatable/js/jquery.dataTables.min.js"></script>
<script src="{{ asset('front') }}/assets/plugins/datatable/js/dataTables.bootstrap5.min.js"></script>
<script src="{{ asset('front') }}/assets/js/table-datatable.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script>
    $(document).ready(function() {
        allLeads();


        $(document).on('click', '#assign_leads_btn', function() {
            var selectedLeadValues = [];
            $('.selected_leads:checked').each(function() {
                selectedLeadValues.push($(this).val());
            });

            $.ajax({
                url: "{{ route('admin.lead-management.assign_lead') }}",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    lead_ids: selectedLeadValues
                },
                dataType: "json",
                beforeSend: function() {
                    $('#loader-container').show();
                },
                complete: function() {
                    $('#loader-container').hide();
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    $('#loader-container').hide();
                    ajaxErrorHandling(jqXHR, errorThrown);
                },
                success: function(data) {
                    var timer = 1200;
                    if (data['reload'] !== undefined) {
                        toast(data['success'], "Success!", 'success', timer);
                        setTimeout(function() {
                            window.location.reload(true);
                        }, 600);
                        return false;
                    }
                    if (data['error'] !== undefined) {
                        $('#loader-container').hide();
                        toast(data['error'], "Error!", 'error');
                        return false;
                    }

                    if (data['errors'] !== undefined) {
                        $('#loader-container').hide();
                        multiple_errors_ajax_handling(data['errors']);
                    }
                    callback(data);
                }
            });

        });



        // code for add spam
        $(document).on('click', '#add_leads_spam', function() {
            // alert("hello assign to spam");
            var selectedLeadValues = [];
            $('.selected_leads:checked').each(function() {
                selectedLeadValues.push($(this).val());
            });

            $.ajax({
                url: "{{ route('admin.lead-management.add_lead_to_spam') }}",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    lead_ids: selectedLeadValues
                },
                dataType: "json",
                beforeSend: function() {
                    $('#loader-container').show();
                },
                complete: function() {
                    $('#loader-container').hide();
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    $('#loader-container').hide();
                    ajaxErrorHandling(jqXHR, errorThrown);
                },
                success: function(data) {
                    var timer = 1200;
                    if (data['reload'] !== undefined) {
                        toast(data['success'], "Success!", 'success', timer);
                        setTimeout(function() {
                            window.location.reload(true);
                        }, 600);
                        return false;
                    }
                    if (data['error'] !== undefined) {
                        $('#loader-container').hide();
                        toast(data['error'], "Error!", 'error');
                        return false;
                    }

                    if (data['errors'] !== undefined) {
                        $('#loader-container').hide();
                        multiple_errors_ajax_handling(data['errors']);
                    }
                    callback(data);
                }
            });

        });
        // code for add spam



        // $(document).on('click', '#add_leads_discord', function() {
        //     // Show the popup modal
        //     $('#discordPopup').fadeIn();
        // });





        // code for send lead on discord


        // $(document).on('click', '#add_leads_discord', function() {
        //     // alert("hello add_leads_discord");
        //     var selectedLeadValues = [];
        //     $('.selected_leads:checked').each(function() {
        //         selectedLeadValues.push($(this).val());
        //     });

        //     $.ajax({
        //         url: "{{ route('admin.lead-management.add_lead_to_spam') }}",
        //         method: 'POST',
        //         headers: {
        //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //         },
        //         data: {
        //             lead_ids: selectedLeadValues
        //         },
        //         dataType: "json",
        //         beforeSend: function() {
        //             $('#loader-container').show();
        //         },
        //         complete: function() {
        //             $('#loader-container').hide();
        //         },
        //         error: function(jqXHR, textStatus, errorThrown) {
        //             $('#loader-container').hide();
        //             ajaxErrorHandling(jqXHR, errorThrown);
        //         },
        //         success: function(data) {
        //             var timer = 1200;
        //             if (data['reload'] !== undefined) {
        //                 toast(data['success'], "Success!", 'success', timer);
        //                 setTimeout(function() {
        //                     window.location.reload(true);
        //                 }, 600);
        //                 return false;
        //             }
        //             if (data['error'] !== undefined) {
        //                 $('#loader-container').hide();
        //                 toast(data['error'], "Error!", 'error');
        //                 return false;
        //             }

        //             if (data['errors'] !== undefined) {
        //                 $('#loader-container').hide();
        //                 multiple_errors_ajax_handling(data['errors']);
        //             }
        //             callback(data);
        //         }
        //     });

        // });



        $(document).on('click', '#sendImmediately', function() {
            // Close modal
            $('#discordPopup').fadeOut();

            var selectedLeadValues = [];
            $('.selected_leads:checked').each(function() {
                selectedLeadValues.push($(this).val());
            });

            // Send leads to Discord immediately
            sendLeadsToDiscord(selectedLeadValues, "immediate");
        });

        $(document).on('click', '#sendAfterDelay', function() {
            // Close modal
            $('#discordPopup').fadeOut();

            var selectedLeadValues = [];
            $('.selected_leads:checked').each(function() {
                selectedLeadValues.push($(this).val());
            });

            // Schedule leads to be sent after x minutes
            sendLeadsToDiscord(selectedLeadValues, 'with_delay');
        });

        function sendLeadsToDiscord(selectedLeadValues, sendType, delay) {
            $.ajax({
                url: "{{ route('admin.lead-management.send_lead_to_discord') }}",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    lead_ids: selectedLeadValues,
                    send_type: sendType,
                    delay
                },
                dataType: "json",
                beforeSend: function() {
                    $('#loader-container').show();
                },
                complete: function() {
                    $('#loader-container').hide();
                },
                success: function(data) {
                    if (data.success) {
                        toast(data.success, "Success!", 'success');
                    } else if (data.error) {
                        toast(data.error, "Error!", 'error');
                    } else if (data.message) {
                        $.alert({
                            title: 'Completed!',
                            content: data.message
                        });
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    ajaxErrorHandling(jqXHR, errorThrown);
                }
            });
        }


        /**
         * Author: Muhammad Wajahat
         * Description: This script provides functionality for handling lead selection and 
         * sending them to Discord either immediately or with a delay via a popup dialog.
         */

        // Displays a popup dialog to choose the sending option for selected leads
        const displaySendLeadsPopup = (selectedLeadIds) => {

            const count = selectedLeadIds.length;
            const msg = `Would you like to send the selected <b>${count} lead${count > 1 ? 's' : ''}</b> immediately or send each lead with a delay?`;

            $.alert({
                title: 'Select Action',
                content: msg,
                columnClass: 'medium',
                buttons: {
                    sendImmediate: {
                        text: 'Send Immediate',
                        btnClass: 'btn-blue',
                        action: function() {
                            sendLeadsToDiscord(selectedLeadIds, 'immediate');
                        }
                    },
                    sendWithDelay: {
                        text: 'Send with Delay',
                        btnClass: 'btn-green',
                        action: function() {
                            $.confirm({
                                title: 'Please Select Delay',
                                content: `  <form action="" class="formName">
                                                <div class="mb-3">
                                                    <div class="form-label">Delay (minutes)</div>
                                                    <select class="form-select" name="delay">
                                                        <option value="5" selected>5 Minutes</option>
                                                        <option value="10">10 Minutes</option>
                                                        <option value="15">15 Minutes</option>
                                                        <option value="20">20 Minutes</option>
                                                        <option value="25">25 Minutes</option>
                                                        <option value="30">30 Minutes</option>
                                                    </select>
                                                </div>
                                            </form>`,
                                buttons: {
                                    formSubmit: {
                                        text: 'Send',
                                        btnClass: 'btn-blue',
                                        action: function() {
                                            const delay = parseInt(this.$content.find('[name=delay]').val());
                                            sendLeadsToDiscord(selectedLeadIds, 'with_delay', delay);
                                        }
                                    },
                                    cancel: function() {

                                    },
                                },
                                onContentReady: function() {
                                    var jc = this;
                                    this.$content.find('form').on('submit', function(e) {
                                        e.preventDefault();
                                        jc.$$formSubmit.trigger('click');
                                    });
                                }
                            });
                        }
                    },
                    cancel: {
                        text: 'Cancel',
                        btnClass: 'btn-secondary',
                        action: function() {

                        }
                    }
                }
            });
        }

        // Handles the click event for adding leads to Discord
        const handleClickAddLeadsToDiscord = () => {
            const tableId = '#allLeads-table';

            if (!$.fn.DataTable.isDataTable(tableId)) {
                $.alert({
                    title: 'Error!',
                    content: 'Leads data table is not initialized!',
                    type: 'red'
                });
                return;
            }

            const dt = $(tableId).DataTable();
            const rows = $(dt.rows().nodes().toArray());
            const selectedCount = rows.find('[name="assign_lead"]:checked').length;

            if (selectedCount <= 0) {
                $.alert({
                    title: 'Error!',
                    content: 'Please select at least on lead.',
                    type: 'red'
                });
                return;
            }

            const selectedLeadIds = [];
            rows.find('[name="assign_lead"]:checked').each(function() {
                selectedLeadIds.push(parseInt($(this).val()));
            });

            displaySendLeadsPopup(selectedLeadIds);
        }

        // Bind the click event to the add leads button
        $(document).on('click', '#add_leads_discord', handleClickAddLeadsToDiscord);

        // code for send lead on discord

        $('#searchRange').daterangepicker();

        $('#searchRange').on('change', function() {
            var dateRange = $(this).val();
            $('#date_range_input').val(dateRange);

            var leadSource = $('#lead_source').val();
            var leadActivity = $('#lead_activity').val();
            var isa_status = $('#isa_status').val();

            allLeads(leadSource, dateRange, leadActivity, isa_status);
            uncontactedLeads(leadSource, dateRange, leadActivity, isa_status);
            recentlyViewedContent(leadSource, dateRange, leadActivity, isa_status);
        });

        $('#lead_activity').on('change', function() {
            var leadActivity = $(this).val();
            var leadSource = $('#lead_source').val();
            var dateRange = $('#date_range_input').val();
            var isa_status = $('#isa_status').val();

            allLeads(leadSource, dateRange, leadActivity, isa_status);
            uncontactedLeads(leadSource, dateRange, leadActivity, isa_status);
            recentlyViewedContent(leadSource, dateRange, leadActivity, isa_status);
        });

        $('.single-select').select2({
            theme: 'bootstrap4',
            width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
            placeholder: $(this).data('placeholder'),
            allowClear: Boolean($(this).data('allow-clear')),
        });

        // groups crud start
        $(document).on('click', '.all_groups', function() {
            $('#AllGroups').modal('show');
        });

        $(document).on('click', '.edit-group', function() {
            $('#AllGroups').modal('hide');
            $('#EditGroup').modal('show');
            $("#edit_pictureList .select-picture-box").removeClass("selected");
            var data = $(this).data('data');
            $('#edit_group_name').val(data.group_title);
            $('#edit_group_id').val(data.id);
            var color = data.background_color;
            if (color == 'primary') {
                $('#edit_colour_1').prop("checked", true);
            } else if (color == 'secondary') {
                $('#edit_colour_2').prop("checked", true);
            } else if (color == 'success') {
                $('#edit_colour_3').prop("checked", true);
            } else if (color == 'danger') {
                $('#edit_colour_4').prop("checked", true);
            } else if (color == 'warning') {
                $('#edit_colour_5').prop("checked", true);
            } else if (color == 'info') {
                $('#edit_colour_6').prop("checked", true);
            }
            $("#edit_pictureList .select-picture-box#" + color).addClass("selected");
        });

        $("#edit_pictureList .select-picture-box").on("click", function() {
            $("#edit_pictureList .select-picture-box").removeClass("selected");
            $(this).addClass("selected");
            var index = $(this).index();
            $("input[name='edit_group_colour']").prop("checked", false);
            $("input[name='edit_group_colour']").eq(index).prop("checked", true);
        });

        $(document).on('click', '.add_new_group', function() {
            $("#pictureList .select-picture-box").removeClass("selected");
            $('.default_selected').addClass('selected');
            $('#add_group')[0].reset();
            $('#AddNewGroup').modal('show');
        });

        $("#pictureList .select-picture-box").on("click", function() {
            $("#pictureList .select-picture-box").removeClass("selected");
            $(this).addClass("selected");
            var index = $(this).index();
            $("input[name='group_colour']").prop("checked", false);
            $("input[name='group_colour']").eq(index).prop("checked", true);
        });

        // groups crud end

        $(document).on('change', '#client', function() {
            $('.ajaxFormClient').submit();
        })

        $(document).on('change', '#all-groups', function() {
            var group = $(this).val();
            $('#search_group').val(group);
            allLeads();
        });

        $(document).on('click', '.add-new-client', function() {
            $("#clientAddModal").modal('show');
        });

        $(document).on('click', '.upload-file', function() {
            $("#fileUpload-1").val('');
            $("#csvfileupload").html('');
            $("#fileUpload").fileUpload();
            $("#uploadFileModal").modal('show');
        });

        $(document).on('click', '.copy-zapier-url', function() {
            let url = $(this).data('url');
            $('.zap_url_input').val(url);
            $("#copyZapierUrlModal").modal('show');
        });

        $('#uploadFileForm,#leadForm,#add_group,#edit_group').submit(function(e) {
            e.preventDefault();
            var url = $(this).attr('action');
            var param = new FormData(this);
            my_ajax(url, param, 'post', function(res) {}, true);
        });

        $(document).on('click', '.get-follow-up', function() {
            if ($.fn.DataTable.isDataTable('#followup-table')) {
                $('#followup-table').DataTable().destroy();
            }
            let type = $(this).data('type');
            var client = $('#client').val();
            $.ajax({
                type: "GET",
                url: "{{ route('admin.lead-management.get_follow_ups') }}",
                data: {
                    type,
                    'is_html': true,
                    'client': client
                },
                dataType: "json",
                beforeSend: function() {

                },
                complete: function() {

                },
                error: function(jqXHR, textStatus, errorThrown) {
                    ajaxErrorHandling(jqXHR, errorThrown);
                },
                success: function(res) {
                    $('#followUps').html(res.template);
                    var isa_status = $('#isa_status').val();
                    var leadSource = $('#lead_source').val();
                    var leadActivity = $('#lead_activity').val();
                    var dateRange = $('#date_range_input').val();
                    switch (res.type) {
                        case 'due_today':
                            followUpsDueToday(leadSource, dateRange, leadActivity,
                                isa_status);
                            break;
                        case 'up_comming':
                            followUpsUpComming(leadSource, dateRange, leadActivity,
                                isa_status);
                            break;
                        case 'over_due':
                            followUpsOverDue(leadSource, dateRange, leadActivity,
                                isa_status);
                            break;
                        default:
                            followUpsDueSomeday(leadSource, dateRange, leadActivity,
                                isa_status);
                            break;
                    }
                }
            });
        });

        function copyZapUrl() {
            var inputElement = $('.zap_url_input');
            inputElement.select();
            document.execCommand('copy');
            window.getSelection().removeAllRanges();
            $("#copy_alert").fadeTo(2000, 500).slideUp(500, function() {
                $("#copy_alert").slideUp(500);
            });
        }

        var copyButton = $('.copy-zap-url');
        copyButton.on('click', copyZapUrl);

        $(document).on('click', '.main-tabs', function() {
            let type = $(this).data('type');
            var isa_status = $('#isa_status').val();
            let dateRange = $('#date_range_input').val();
            let leadActivity = $('#lead_activity').val();

            if (type == 'allClients') {
                var leadSource = $('#lead_source').val();
                allLeads(leadSource, dateRange, leadActivity, isa_status);

            } else if (type == 'uncontactedLeads') {
                var uncontacted_leads_count = $('#uncontacted_leads_count').val();
                if (uncontacted_leads_count > 0) {
                    var leadSource = $('#lead_source').val();
                    uncontactedLeads(leadSource, dateRange, leadActivity, isa_status);
                }
            } else if (type == 'followUps') {
                var followup_leads_count = $('#followup_leads_count').val();
                if (followup_leads_count > 0) {
                    var leadSource = $('#lead_source').val();
                    followUpsDueToday(leadSource, dateRange, leadActivity, isa_status);
                }
            } else if (type == 'recentlyViewed') {
                var recently_viewed_count = $('#recently_viewed_count').val();
                if (recently_viewed_count > 0) {
                    var leadSource = $('#lead_source').val();
                    recentlyViewedContent(leadSource, dateRange, leadActivity, isa_status);
                }
            }
        });

        $(document).on('change', '#lead_source', function() {
            var leadSource = $(this).val();
            var isa_status = $('#isa_status').val();
            let dateRange = $('#date_range_input').val();
            let leadActivity = $('#lead_activity').val();
            allLeads(leadSource, dateRange, leadActivity, isa_status);
            uncontactedLeads(leadSource, dateRange, leadActivity, isa_status);
            recentlyViewedContent(leadSource, dateRange, leadActivity, isa_status);
        });

        $(document).on('change', '#isa_status', function() {
            var isa_status = $(this).val();
            var leadSource = $('#lead_source').val();
            let dateRange = $('#date_range_input').val();
            let leadActivity = $('#lead_activity').val();
            allLeads(leadSource, dateRange, leadActivity, isa_status);
            uncontactedLeads(leadSource, dateRange, leadActivity, isa_status);
            recentlyViewedContent(leadSource, dateRange, leadActivity, isa_status);
        });

        function allLeads(leadSource = "", dateRange = "", leadActivity = "", isa_status = "") {
            if ($.fn.DataTable.isDataTable('#allLeads-table')) {
                $('#allLeads-table').DataTable().destroy();
            }
            var client = $('#client').val();
            $('#allLeads-table').DataTable({
                processing: true,
                serverSide: true,
                "order": [
                    [0, "desc"]
                ],
                "pageLength": 10,
                "lengthMenu": [10, 50, 100, 150, 500],
                ajax: {
                    url: "{{ route('admin.lead-management.all') }}",
                    data: function(d) {
                        d.search = $('input[type="search"]').val(),
                            d.type = 'all_leads',
                            d.group_id = $('#search_group').val()
                        d.client = client,
                            d.leadSource = leadSource,
                            d.dateRange = dateRange,
                            d.leadActivity = leadActivity,
                            d.isa_status = isa_status
                    }
                },
                columns: [{
                        data: 'check_boxes',
                        name: 'check_boxes',
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: 'assign_status',
                        name: 'assign_status',
                        orderable: false,
                        searchable: true
                    },

                    {
                        data: 'client_name',
                        name: 'client_name',
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: 'name',
                        name: 'name',
                        orderable: false,
                        searchable: true
                    },

                    {
                        data: 'email',
                        name: 'email',
                        orderable: false,
                        searchable: true
                    },

                    {
                        data: 'mobile_number',
                        name: 'mobile_number',
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

        function uncontactedLeads(leadSource = "", dateRange = "", leadActivity = "", isa_status = "") {
            if ($.fn.DataTable.isDataTable('#newLead-table')) {
                $('#newLead-table').DataTable().destroy();
            }
            var client = $('#client').val();
            $('#newLead-table').DataTable({
                processing: true,
                serverSide: true,
                "order": [
                    [0, "desc"]
                ],
                "pageLength": 10,
                "lengthMenu": [10, 50, 100, 150, 500],
                ajax: {
                    url: "{{ route('admin.lead-management.all') }}",
                    data: function(d) {
                        d.search = $('input[type="search"]').val(),
                            d.type = 'uncontacted_leads',
                            d.client = client,
                            d.leadSource = leadSource,
                            d.dateRange = dateRange,
                            d.leadActivity = leadActivity,
                            d.isa_status = isa_status
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
                    if (data.status == "uncontacted") {
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

        function followUpsDueToday(leadSource = "", dateRange = "", leadActivity = "", isa_status = "") {
            if ($.fn.DataTable.isDataTable('#followup-table')) {
                $('#followup-table').DataTable().destroy();
            }
            var client = $('#client').val();
            $('#followup-table').DataTable({
                processing: true,
                serverSide: true,
                "pageLength": 10,
                "lengthMenu": [10, 50, 100, 150, 500],
                ajax: {
                    url: "{{ route('admin.lead-management.get_follow_ups') }}",
                    data: function(d) {
                        d.search = $('input[type="search"]').val(),
                            d.type = 'due_today',
                            d.client = client,
                            d.leadSource = leadSource,
                            d.dateRange = dateRange,
                            d.leadActivity = leadActivity,
                            d.isa_status = isa_status
                    }
                },
                columns: [{
                        data: 'client_name',
                        name: 'client_name',
                        orderable: false,
                        searchable: true
                    },
                    {
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

        function followUpsUpComming(leadSource = "", dateRange = "", leadActivity = "", isa_status = "") {
            if ($.fn.DataTable.isDataTable('#followup-table')) {
                $('#followup-table').DataTable().destroy();
            }
            var client = $('#client').val();
            $('#followup-table').DataTable({
                processing: true,
                serverSide: true,
                "pageLength": 10,
                "lengthMenu": [10, 50, 100, 150, 500],
                ajax: {
                    url: "{{ route('admin.lead-management.get_follow_ups') }}",
                    data: function(d) {
                        d.search = $('input[type="search"]').val(),
                            d.type = 'up_comming',
                            d.client = client,
                            d.leadSource = leadSource,
                            d.dateRange = dateRange,
                            d.leadActivity = leadActivity,
                            d.isa_status = isa_status
                    }
                },
                columns: [{
                        data: 'client_name',
                        name: 'client_name',
                        orderable: false,
                        searchable: true
                    },
                    {
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

        function followUpsOverDue(leadSource = "", dateRange = "", leadActivity = "", isa_status = "") {
            if ($.fn.DataTable.isDataTable('#followup-table')) {
                $('#followup-table').DataTable().destroy();
            }
            var client = $('#client').val();
            $('#followup-table').DataTable({
                processing: true,
                serverSide: true,
                "pageLength": 10,
                "lengthMenu": [10, 50, 100, 150, 500],
                ajax: {
                    url: "{{ route('admin.lead-management.get_follow_ups') }}",
                    data: function(d) {
                        d.search = $('input[type="search"]').val(),
                            d.type = 'over_due',
                            d.client = client,
                            d.leadSource = leadSource,
                            d.dateRange = dateRange,
                            d.leadActivity = leadActivity,
                            d.isa_status = isa_status
                    }
                },
                columns: [{
                        data: 'client_name',
                        name: 'client_name',
                        orderable: false,
                        searchable: true
                    },
                    {
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

        function followUpsDueSomeday(leadSource = "", dateRange = "", leadActivity = "", isa_status = "") {
            if ($.fn.DataTable.isDataTable('#followup-table')) {
                $('#followup-table').DataTable().destroy();
            }
            var client = $('#client').val();
            $('#followup-table').DataTable({
                processing: true,
                serverSide: true,
                "pageLength": 10,
                "lengthMenu": [10, 50, 100, 150, 500],
                ajax: {
                    url: "{{ route('admin.lead-management.get_follow_ups') }}",
                    data: function(d) {
                        d.search = $('input[type="search"]').val(),
                            d.type = 'due_someday',
                            d.client = client,
                            d.leadSource = leadSource,
                            d.dateRange = dateRange,
                            d.leadActivity = leadActivity,
                            d.isa_status = isa_status
                    }
                },
                columns: [{
                        data: 'client_name',
                        name: 'client_name',
                        orderable: false,
                        searchable: false
                    },
                    {
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

        function recentlyViewedContent(leadSource = "", dateRange = "", leadActivity = "", isa_status = "") {
            if ($.fn.DataTable.isDataTable('#recently-view-table')) {
                $('#recently-view-table').DataTable().destroy();
            }
            $.fn.dataTable.ext.errMode = 'throw';
            var client = $('#client').val();
            $('#recently-view-table').DataTable({
                processing: true,
                serverSide: true,
                "order": [
                    [0, "desc"]
                ],
                "pageLength": 10,
                "lengthMenu": [10, 50, 100, 150, 500],
                ajax: {
                    url: "{{ route('admin.lead-management.all') }}",
                    data: function(d) {
                        d.search = $('input[type="search"]').val(),
                            d.type = 'recently_viewed_leads',
                            d.client = client,
                            d.leadSource = leadSource,
                            d.dateRange = dateRange,
                            d.leadActivity = leadActivity
                        d.isa_status = isa_status
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
                        data: 'viewed_item',
                        name: 'viewed_item',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'last_viewed',
                        name: 'last_viewed',
                        orderable: true,
                        searchable: false
                    },
                ],
                drawCallback: function(settings) {
                    // Check if the DataTable has no data
                    if (settings.json.recordsTotal === 0) {
                        // Show the additional div
                        $('#recently-view-empty').show();
                        $('#recently-view-responsive').hide();
                    } else {
                        // Hide the additional div if there is data
                        $('#recently-view-empty').hide();
                        $('#recently-view-responsive').show();
                    }
                },
            });
        }

        var leadDataCount = 1;
        $(document).on('click', '.add_lead_data_tr', function() {
            let _html = `<tr id="lead_data_tr_${leadDataCount}">
                                <td>
                                    <input type="text" class="form-control" name="data[${leadDataCount}][key]" placeholder="Enter Data Key" required>
                                </td>
                                <td>
                                    <input type="text" class="form-control" name="data[${leadDataCount}][value]" placeholder="Enter Data Value" required>
                                </td>
                                <td>
                                    <a href="javascript:void(0);" class="btn btn-danger delete_lead_data_tr" data-id="${leadDataCount}"><i class="fa-solid fa-trash" style="margin-left: 0px; vertical-align: initial;"></i></a>
                                </td>
                            </tr>`;
            leadDataCount++
            $("#leadData_body").append(_html);
        });

        $(document).on('click', '.delete_lead_data_tr', function() {
            let id = $(this).data('id');
            $('#lead_data_tr_' + id).remove();
            leadDataCount--;
        });
    });
</script>
@endsection