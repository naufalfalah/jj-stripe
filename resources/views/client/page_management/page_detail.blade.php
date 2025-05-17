@extends('layouts.front')
@section('page-css')
<link href="{{ asset('front') }}/assets/plugins/datetimepicker/css/classic.css" rel="stylesheet" />
<link href="{{ asset('front') }}/assets/plugins/datetimepicker/css/classic.time.css" rel="stylesheet" />
<link href="{{ asset('front') }}/assets/plugins/datetimepicker/css/classic.date.css" rel="stylesheet" />
<link rel="stylesheet"
    href="{{ asset('front') }}/assets/plugins/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.min.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
@endsection
@section('content')
<div class="page-breadcrumb d-flex align-items-center mb-3 mt-5">
    <div class="breadcrumb-title">{{@$breadcrumb_main}}</div>
    <a href="Javascript:void(0);" onclick="goBack()" class="btn btn-dark"><i class="fa-solid fa-angle-left"></i> Back</a>
    <div class="ms-auto">
        <a class="btn btn-secondary mr-3 send-template" href="javascript:void(0);" data-data="{{ $data }}">Send To Client</a>
        <div class="btn-group">
            <button type="button" class="btn btn-primary split-bg-primary dropdown-toggle dropdown-toggle-split"
                data-bs-toggle="dropdown"> <span class="">Options </span>
            </button>
            <div class="dropdown-menu dropdown-menu-right dropdown-menu-lg-end">
                <a class="dropdown-item private_note" href="javascript:void(0);" data-data="{{ $data }}">Edit private note</a>
                <a class="dropdown-item edit-temp" href="{{ route('user.page.edit_page', $data->hashid) }}">Edit Page</a>
                <a class="dropdown-item text-danger" href="javascript:void(0);" onclick="ajaxRequest(this)" data-url="{{route('user.page.delete_page',$data->hashid)}}">Delete Page</a>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12 col-md-6">
        <h5><strong> Preview </strong></h5>

        <div class="col">
            <div class="card">
              <img src="{{ asset($data->cover_image) }}" height="200px" class="card-img-top" alt="...">
              <div class="card-body border-bottom">
                <h5 class="card-title">{{ $data->title }}</h5>
                <p class="card-text">{{ Str::limit($data->description ?? "", 130, '...') }}</p>
                <a href="{{ route('user.page.page_preview', $data->hashid) }}" target="_blank" class="text-dark"><i class="bi bi-eye-fill"></i> Page Preview</a>
              </div>
            </div>
        </div>

    </div>

    <div class="col-12 col-md-6">
        <h5><strong> Sharing History </strong></h5>

        <div class="card">
            <div class="card-body">
                <a href="javascript:void(0);" class="text-dark total_share_view">
                    <div class="row g-0 mb-2">
                        <div class="col-12 d-flex justify-content-between align-items-center">
                            <div>
                                <span>Total Share</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <h6 class="mb-0"><strong> {{ $page_details->Total_Shared ?? 0 }} </strong></h6>
                            </div>
                        </div>
                    </div>
                </a>
                <hr>

                <a href="javascript:void(0);" class="text-dark opend_files">
                    <div class="row g-0 mb-2">
                        <div class="col-12 d-flex justify-content-between align-items-center">
                            <div>
                                <span>Opened</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <h6 class="mb-0"><strong>{{ $page_details->opened ?? 0 }}</strong></h6>
                            </div>
                        </div>
                    </div>
                </a>
                <hr>

                <a href="javascript:void(0);" class="text-dark un_opend_files">
                    <div class="row g-0">
                        <div class="col-12 d-flex justify-content-between align-items-center">
                            <div>
                                <span>Unopened</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <h6 class="mb-0"><strong>{{ $page_details->unopend ?? 0 }}</strong></h6>
                            </div>
                        </div>
                    </div>
                </a>

            </div>
        </div>

        <h5 class="text-dark"><strong>High Potential Clients</strong></h5>

        <div class="card">
            <div class="card-body">
                <a href="javascript:void(0);" class="text-dark viewed_in_last_7_days">
                    <div class="row g-0 mb-2">
                        <div class="col-12 d-flex justify-content-between align-items-center">
                            <div>
                                <span>Viewed In Last 7 Days</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <h6 class="mb-0"><strong> {{ $page_details->viewed_in_last_7_days ?? 0 }} </strong></h6>
                            </div>
                        </div>
                    </div>
                </a>
                <hr>

                <a href="javascript:void(0);" class="text-dark view_multiple_time">
                    <div class="row g-0">
                        <div class="col-12 d-flex justify-content-between align-items-center">
                            <div>
                                <span>Viewed Multiple Times</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <h6 class="mb-0"><strong>{{ $page_details->viewed_multiple_times ?? 0 }}</strong></h6>
                            </div>
                        </div>
                    </div>
                </a>

            </div>
        </div>

    </div>
</div>

<div class="row mt-4">
    <div class="col-12 col-md-6">
        <h5><strong> Private Notes </strong></h5>
        <div class="card">
            <div class="card-body">
                <div class="row g-0">
                    <div class="col-12">
                        <div class="col-md-12 private_note p-2" style="cursor: pointer;" data-data="{{ $data }}">
                            <p>{{ $data->private_note ?? 'Add private note here...' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-6">
        <h5><strong> Timeline </strong></h5>
        <div class="card">
            <div class="card-body">
                @if ($data->page_activity()->count() > 0)
                    @foreach ($data->page_activity as $k => $item)
                        <div class="row">
                            <div class="col-auto text-center flex-column d-sm-flex">
                                <div class="row h-50">
                                    <div class="col border-end">&nbsp;</div>
                                    <div class="col">&nbsp;</div>
                                </div>
                                <h5 class="">
                                    <i class="fa-solid fa-paper-plane" style=" font-size: 16px; cursor: pointer;"></i>
                                </h5>
                                <div class="row h-50">
                                    <div class="col border-end">&nbsp;</div>
                                    <div class="col">&nbsp;</div>
                                </div>
                            </div>
                            <div class="col py-2">
                                <div class="row">
                                    <div class="col-12 col-lg-12 d-flex">
                                        <div class="card shadow radius-10 w-100">

                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <h6 class="mb-0">{{ Str::limit($data->title, 25, '...') }}</h6>
                                                    <div class="fs-5 ms-auto dropdown">
                                                        <div class="dropdown-toggle dropdown-toggle-nocaret cursor-pointer" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></div>
                                                        <ul class="dropdown-menu">
                                                            <li><a class="dropdown-item text-success edit-activity float-end"  href="{{ route('user.leads-management.client_details', hashids_encode($item->client_id)) }}">Go To Client Detail</a></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                                <small class="">{{ $item->created_at->format('M d, Y h:i a') }}</small> <br>
                                                <small class=" fw-bold">Sent To: </small><span>{{ $item->lead_client->name }}</span> <br>

                                                @if ($item->total_views > 0)
                                                    <small class="text-muted"><i class="fa-solid fa-envelope-open"></i> Last Open @diffForHumans($item->last_open)</small><br>
                                                    <small class="text-muted"><i class="fa-solid fa-glasses"></i> Viewed {{ $item->total_views }} times</small>
                                                @else
                                                    <small class="text-muted"><i class="fa-regular fa-envelope"></i> Unopened</small>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif

                <div class="col-12">
                    <div class="row">
                        <div class="col-auto text-center flex-column d-sm-flex">
                            <div class="row h-50">
                                <div class="col border-end">&nbsp;</div>
                                <div class="col">&nbsp;</div>
                            </div>
                            <h5 class="">
                                <i class="fa-solid fa-comment-medical" style="font-size: 25px;"></i>
                            </h5>
                        </div>
                        <div class="col py-2">
                            <div class="row">
                                <div class="col-12 col-lg-12 d-flex">
                                    <div class="card shadow radius-10 w-100">
                                        <div class="card-body">
                                            <div class="d-flex w-100 justify-content-between">
                                                <small class="text-muted">{{ $data->created_at->format('M d, Y h:i A' ) }}</small>
                                            </div>
                                            <small class="text-muted">Page Created</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="pageNoteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Private Notes</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('user.page.save') }}" method="post" id="pageNoteForm">
                @csrf
                <div class="modal-body">
                    <p>These notes are private to you and will not be accessible to clients you share this message with.</p>
                    <input type="hidden" name="id" id="page_note_id">
                    <input type="hidden" name="type" value="note">
                    <textarea name="note" class="form-control" id="page_note" cols="30"
                        rows="10" required placeholder="Add private note here..."></textarea>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary form-submit-btn">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="sendTemplateModal" tabindex="-1" aria-labelledby="sendTemplateModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Send Page</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('user.page.send') }}" method="post" id="pageSendForm">
                @csrf
                <div class="modal-body">
                    <input type="hidden" value="{{ $data->id }}" name="page_id">
                    <input type="hidden" name="lead_id" id="lead_id">
                    <input type="hidden" name="client_num" id="client_num">
                    <input type="hidden" name="client_email" id="client_email">
                    <input type="hidden" name="activity_url" id="activity_url">
                    <div class="form-group mb-3">
                        <label>All Client <span class="text-danger">*</span></label>
                        <select name="client" id="client" class="form-control single-select" required>
                            <option value="" disabled selected>All Client</option>
                            @foreach ($clients as $val)
                            <option value="{{ $val->hashid }}" data-client_id="{{ $val->hashid }}" data-lead_id="{{ $val->id }}" data-page_id="{{ $data->hashid }}" data-num="{{ $val->mobile_number }}" data-email="{{ $val->email }}" data-name="{{ $val->name }}">{{ $val->name }} ( {{ $val->email }} )</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="">Page Title<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="title"
                            placeholder="Enter Template Title" id="send_title" readonly required>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <a href="ttps://wa.me/1234567890?text=Hello%20World" target="_blank" id="whatsapp_link" type="submit" class="btn btn-primary form-submit-btn">Send</a>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="TotalFileShared" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Total Shared ({{ $page_details->Total_Shared ?? 0 }})</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="card w-100" style="box-shadow: none;">
                <div class="card-body">
                    <div class="table-responsive mt-2">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Client Name</th>
                                    <th>Date Shared</th>
                                    <th>Last Opened</th>
                                    <th>View Stats</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($get_page_activity as $k => $activity)
                                    <tr>
                                        <td> {{ $k + 1}} </td>
                                        <td> {{ $activity->lead_client->name }} </td>
                                        <td> {{ $activity->created_at->format('M-d-Y - h:i a') }} </td>
                                        @if ($activity->last_open != null)
                                            @php
                                                $lastOpen = \Carbon\Carbon::parse($activity->last_open);
                                            @endphp
                                            <td> {{ $lastOpen->format('M-d-Y - h:i a') }} </td>
                                        @else
                                            <td> <small class="text-dark"><i class="fa-regular fa-envelope"></i> Unopened</small> </td>
                                        @endif

                                        @if ($activity->last_open != null && $activity->total_views > 0)
                                            <td> {{ $activity->total_views }} times</td>
                                        @else
                                            <td> - </td>
                                        @endif
                                        @php
                                            $lead_id = hashids_encode($activity->client_id);
                                        @endphp
                                        <td>
                                            <a class="text-dark" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Go To Client Detail" data-bs-original-title="Go To Client Detail" aria-label="Go To Client Detail" href="{{ route('user.leads-management.client_details', $lead_id) }}"><i class="fa-solid fa-up-right-from-square"></i></a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No Groups Found</td>
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

<div class="modal fade" id="OpenedURL" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Total Opened ({{ $page_details->opened ?? 0 }})</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="card w-100" style="box-shadow: none;">
                <div class="card-body">
                    <div class="table-responsive mt-2">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Client Name</th>
                                    <th>Date Shared</th>
                                    <th>Last Opened</th>
                                    <th>View Stats</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $counter = 1;
                                @endphp
                                @forelse ($get_page_activity as $k => $activity)
                                  @if ($activity->total_views > 0)
                                    <tr>
                                        <td> {{ $counter }} </td>
                                        <td> {{ $activity->lead_client->name }} </td>
                                        <td> {{ $activity->created_at->format('M-d-Y - h:i a') }} </td>
                                        @if ($activity->last_open != null)
                                            @php
                                                $lastOpen = \Carbon\Carbon::parse($activity->last_open);
                                            @endphp
                                            <td> {{ $lastOpen->format('M-d-Y - h:i a') }} </td>
                                        @else
                                            <td> <small class="text-dark"><i class="fa-regular fa-envelope"></i> Unopened</small> </td>
                                        @endif

                                        @if ($activity->last_open != null && $activity->total_views > 0)
                                            <td> {{ $activity->total_views }} times</td>
                                        @else
                                            <td> - </td>
                                        @endif
                                        @php
                                            $lead_id = hashids_encode($activity->client_id);
                                        @endphp
                                        <td>
                                            <a class="text-dark" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Go To Client Detail" data-bs-original-title="Go To Client Detail" aria-label="Go To Client Detail" href="{{ route('user.leads-management.client_details', $lead_id) }}"><i class="fa-solid fa-up-right-from-square"></i></a>
                                        </td>
                                    </tr>
                                    @php
                                        $counter++;
                                    @endphp
                                  @endif

                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No Record Found</td>
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

<div class="modal fade" id="UnOpenedURL" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Total Unopened ({{ $page_details->unopend ?? 0 }})</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="card w-100" style="box-shadow: none;">
                <div class="card-body">
                    <div class="table-responsive mt-2">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Client Name</th>
                                    <th>Date Shared</th>
                                    <th>Last Opened</th>
                                    <th>View Stats</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $counter = 1;
                                @endphp
                                @forelse ($get_page_activity as $k => $activity)
                                  @if ($activity->total_views == 0 && $activity->last_open == null)
                                    <tr>
                                        <td> {{ $counter }} </td>
                                        <td> {{ $activity->lead_client->name }} </td>
                                        <td> {{ $activity->created_at->format('M-d-Y - h:i a') }} </td>
                                        @if ($activity->last_open != null)
                                            @php
                                                $lastOpen = \Carbon\Carbon::parse($activity->last_open);
                                            @endphp
                                            <td> {{ $lastOpen->format('M-d-Y - h:i a') }} </td>
                                        @else
                                            <td> <small class="text-dark"><i class="fa-regular fa-envelope"></i> Unopened</small> </td>
                                        @endif

                                        @if ($activity->last_open != null && $activity->total_views > 0)
                                            <td> {{ $activity->total_views }} times</td>
                                        @else
                                            <td> - </td>
                                        @endif
                                        @php
                                            $lead_id = hashids_encode($activity->client_id);
                                        @endphp
                                        <td>
                                            <a class="text-dark" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Go To Client Detail" data-bs-original-title="Go To Client Detail" aria-label="Go To Client Detail" href="{{ route('user.leads-management.client_details', $lead_id) }}"><i class="fa-solid fa-up-right-from-square"></i></a>
                                        </td>
                                    </tr>
                                    @php
                                        $counter++;
                                    @endphp
                                  @endif

                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No Record Found</td>
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

<div class="modal fade" id="Last7Days" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Viewed In Last 7 Days ({{ $page_details->viewed_in_last_7_days ?? 0 }})</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="card w-100" style="box-shadow: none;">
                <div class="card-body">
                    <div class="table-responsive mt-2">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Client Name</th>
                                    <th>Date Shared</th>
                                    <th>Last Opened</th>
                                    <th>View Stats</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $counter = 1;
                                @endphp
                                @forelse ($viewed_last_7_days as $k => $activity)
                                    <tr>
                                        <td> {{ $counter }} </td>
                                        <td> {{ $activity->lead_client->name }} </td>
                                        <td> {{ $activity->created_at->format('M-d-Y - h:i a') }} </td>
                                        @if ($activity->last_open != null)
                                            @php
                                                $lastOpen = \Carbon\Carbon::parse($activity->last_open);
                                            @endphp
                                            <td> {{ $lastOpen->format('M-d-Y - h:i a') }} </td>
                                        @else
                                            <td> <small class="text-dark"><i class="fa-regular fa-envelope"></i> Unopened</small> </td>
                                        @endif

                                        @if ($activity->last_open != null && $activity->total_views > 0)
                                            <td> {{ $activity->total_views }} times</td>
                                        @else
                                            <td> - </td>
                                        @endif
                                        @php
                                            $lead_id = hashids_encode($activity->client_id);
                                        @endphp
                                        <td>
                                            <a class="text-dark" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Go To Client Detail" data-bs-original-title="Go To Client Detail" aria-label="Go To Client Detail" href="{{ route('user.leads-management.client_details', $lead_id) }}"><i class="fa-solid fa-up-right-from-square"></i></a>
                                        </td>
                                    </tr>
                                    @php
                                        $counter++;
                                    @endphp
                                  {{-- @endif   --}}

                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No Record Found</td>
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

<div class="modal fade" id="ViewedMultipleTimes" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Viewed Miltiple Times ({{ $page_details->viewed_multiple_times ?? 0 }})</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="card w-100" style="box-shadow: none;">
                <div class="card-body">
                    <div class="table-responsive mt-2">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Client Name</th>
                                    <th>Date Shared</th>
                                    <th>Last Opened</th>
                                    <th>View Stats</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $counter = 1;
                                @endphp
                                @forelse ($get_page_activity as $k => $activity)
                                  @if ($activity->total_views > 1 && $activity->last_open != null)
                                    <tr>
                                        <td> {{ $counter }} </td>
                                        <td> {{ $activity->lead_client->name }} </td>
                                        <td> {{ $activity->created_at->format('M-d-Y - h:i a') }} </td>
                                        @if ($activity->last_open != null)
                                            @php
                                                $lastOpen = \Carbon\Carbon::parse($activity->last_open);
                                            @endphp
                                            <td> {{ $lastOpen->format('M-d-Y - h:i a') }} </td>
                                        @else
                                            <td> <small class="text-dark"><i class="fa-regular fa-envelope"></i> Unopened</small> </td>
                                        @endif

                                        @if ($activity->last_open != null && $activity->total_views > 0)
                                            <td> {{ $activity->total_views }} times</td>
                                        @else
                                            <td> - </td>
                                        @endif
                                        @php
                                            $lead_id = hashids_encode($activity->client_id);
                                        @endphp
                                        <td>
                                            <a class="text-dark" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Go To Client Detail" data-bs-original-title="Go To Client Detail" aria-label="Go To Client Detail" href="{{ route('user.leads-management.client_details', $lead_id) }}"><i class="fa-solid fa-up-right-from-square"></i></a>
                                        </td>
                                    </tr>
                                    @php
                                        $counter++;
                                    @endphp
                                  @endif
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No Record Found</td>
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

@endsection
@section('page-scripts')
<script src="{{ asset('front') }}/assets/plugins/datetimepicker/js/legacy.js"></script>
<script src="{{ asset('front') }}/assets/plugins/datetimepicker/js/picker.js"></script>
<script src="{{ asset('front') }}/assets/plugins/datetimepicker/js/picker.time.js"></script>
<script src="{{ asset('front') }}/assets/plugins/datetimepicker/js/picker.date.js"></script>
<script src="{{ asset('front') }}/assets/plugins/bootstrap-material-datetimepicker/js/moment.min.js"></script>
<script src="{{ asset('front') }}/assets/plugins/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.min.js"></script>
<script>

    $(".private_note").click(function () {
        let data = $(this).data('data');
        $("#page_note_id").val(data.id);
        $("#page_note").val(data.private_note);
        $('#pageNoteModal').modal('show');
    });

    $('#pageNoteForm, #pageSendForm').submit(function(e) {
        e.preventDefault();
        var url = $(this).attr('action');
        var param = new FormData(this);
        my_ajax(url, param, 'post', function(res) {
        },true);
    });

    $(function(){
        $('.single-select').select2({
            dropdownParent: $('#sendTemplateModal')
        });
    });

    $(document).on('change', '.single-select', function () {
        let clientName = $(this).find(':selected').attr('data-name');
        let clientNum = $(this).find(':selected').attr('data-num');
        let clientEmail = $(this).find(':selected').attr('data-email');
        let leadId = $(this).find(':selected').attr('data-lead_id');
        let pageID = $(this).find(':selected').attr('data-page_id');
        let clientID = $(this).find(':selected').attr('data-client_id');
        let url = '{{ route("client.page_view", [":pageID", ":clientID"]) }}';
        url = url.replace(':pageID', pageID);
        url = url.replace(':clientID', clientID);
        $("#lead_id").val(leadId);
        $("#client_num").val(clientNum);
        $("#client_email").val(clientEmail);
        $('#activity_url').val(url);
        let tempMessage = "I'd like to share ACME Residences with you, via the link below. Please let me know if I can help answer any questions or arrange a viewing for you, thank you 😊";
        tempMessage = `Hi ${clientName},\n\n${tempMessage}\n\n${url}`;

        $("#send_templateMessage").val(tempMessage);
        let whatsappLink = "https://wa.me/65" + clientNum + "?text=" + encodeURIComponent(tempMessage);
        $("#whatsapp_link").attr("href", whatsappLink);
    });

    $('#whatsapp_link').click(function () {
        $('#pageSendForm').submit();
    });

    $(document).on('click', '.send-template', function (){
        let data = $(this).data('data');
        $("#send_temp_id").val(data.id);
        $("#send_title").val(data.title);
        $("#client_desc").val(data.description);
        $("#client_num").val(data.mobile_number);
        $("#client_email").val(data.email);
        $("#send_templateMessage").val(data.description);
        $('#sendTemplateModal').modal({ backdrop: 'static', keyboard: false});
        $('#sendTemplateModal').modal('show');
    });

    function goBack() {
        window.history.back();
    }

    $('.total_share_view').click(function (){
        $('#TotalFileShared').modal('show')
    });

    $('.opend_files').click(function (){
        $('#OpenedURL').modal('show')
    });

    $('.un_opend_files').click(function (){
        $('#UnOpenedURL').modal('show')
    });

    $('.viewed_in_last_7_days').click(function (){
        $('#Last7Days').modal('show')
    });

    $('.view_multiple_time').click(function (){
        $('#ViewedMultipleTimes').modal('show')
    });
</script>
@endsection
