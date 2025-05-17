@extends('layouts.front')

@section('content')
    <div class="page-breadcrumb d-flex align-items-center mb-3 mt-5">
        <div class="">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item active" aria-current="page">
                        <h4>{{ Str::limit($client_file->file_name,25) }}</h4>
                    </li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">
        <a class="btn btn-secondary mr-3 send-template" href="javascript:void(0);" data-data="{{ $client_file }}">Send To Client</a>

            <div class="btn-group">
                <button type="button" class="btn btn-primary split-bg-primary dropdown-toggle dropdown-toggle-split"
                    data-bs-toggle="dropdown"> <span class="">Options </span>
                </button>
                <div class="dropdown-menu dropdown-menu-right dropdown-menu-lg-end">
                    <a class="dropdown-item rename_file" href="javascript:void(0);">Rename File</a>
                </div>
            </div>
        </div>
    </div>




    <div class="row">
        <h5 class="text-dark"><strong>Sharing History</strong></h5>
        <div class="col-12 col-xl-8">
            <div class="card">
                <div class="card-body">
                    <a href="javascript:void(0);" class="text-dark total_share_view">
                        <div class="row g-0 mb-2">
                            <div class="col-12 d-flex justify-content-between align-items-center">
                                <div>
                                    <span>Total Share</span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <h6 class="mb-0"><strong> {{ $file_details->Total_Shared ?? 0 }} </strong></h6>
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
                                    <h6 class="mb-0"><strong>{{ $file_details->opened ?? 0 }}</strong></h6>
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
                                    <h6 class="mb-0"><strong>{{ $file_details->unopend ?? 0 }}</strong></h6>
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
                                    <h6 class="mb-0"><strong> {{ $file_details->viewed_in_last_7_days ?? 0 }} </strong></h6>
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
                                    <h6 class="mb-0"><strong>{{ $file_details->viewed_multiple_times ?? 0 }}</strong></h6>
                                </div>
                            </div>
                        </div>
                    </a>

                </div>
            </div>

        </div>

        <div class="col-12 col-xl-4">
            <h5 class="text-dark"><strong>Timeline</strong></h5>
            <div class="card">
                <div class="card-body">
                    @if ($get_file_activity->count() > 0)
                        @foreach ($get_file_activity as $k => $item)
                            <div class="row no-gutters">
                                <div class="col-auto text-center flex-column d-sm-flex">

                                    <h5 class="m-2">
                                        <i class="fa-solid fa-paper-plane edit-activity" data-data="{{ $item }}" style="cursor: pointer;"></i>
                                    </h5>
                                    <div class="row h-50">
                                        <div class="col border-end">&nbsp;</div>
                                        <div class="col">&nbsp;</div>
                                    </div>
                                </div>

                                <div class="col py-2">
                                    <div class="card shadow radius-10 w-100">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <h6 class="mb-0">{{ Str::limit($item->title, 17, '...') }}</h6>
                                                @if ($item->type !== 'add')
                                                    <div class="fs-5 ms-auto dropdown">
                                                        <div class="dropdown-toggle dropdown-toggle-nocaret cursor-pointer" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></div>
                                                        <ul class="dropdown-menu">
                                                            @php
                                                                $lead_id = hashids_encode($item->lead_client_id);
                                                            @endphp
                                                            <li><a class="dropdown-item text-success float-end" href="{{route('user.leads-management.client_details',$lead_id)}}">Go To Client Detail</a></li>
                                                        </ul>
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="d-flex w-100 justify-content-between">
                                                <small class="text-muted">{{ $item->created_at->format('M d, Y h:i a') }}</small>
                                            </div>
                                            @if ($item->total_views > 0)
                                                <small class="text-muted"><i class="fa-solid fa-envelope-open"></i> Last Open {{ \Carbon\Carbon::parse($item->last_open)->diffForHumans() }} </small><br>
                                                <small class="text-muted"><i class="fa-solid fa-glasses"></i> Viewed {{ $item->total_views }} times</small>
                                            @else
                                                <small class="text-muted"><i class="fa-regular fa-envelope"></i> Unopened</small>
                                            @endif

                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <h6 class="text-center">No Record Found</h6>
                    @endif

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="TotalFileShared" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Total Shared ({{ $file_details->Total_Shared ?? 0 }})</h5>
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
                                    @forelse ($get_file_activity as $k => $activity)
                                        <tr>
                                            <td> {{ $k + 1}} </td>
                                            <td> {{ $activity->file_lead->name }} </td>
                                            <td> {{ $activity->created_at->format('M-d-Y - h:i a') }} </td>
                                            @if ($activity->last_open != null)
                                                <td> {{ $activity->last_open->format('M-d-Y - h:i a') }} </td>
                                            @else
                                                <td> <small class="text-dark"><i class="fa-regular fa-envelope"></i> Unopened</small> </td>
                                            @endif

                                            @if ($activity->last_open != null && $activity->total_views > 0)
                                                <td> {{ $activity->total_views }} times</td>
                                            @else
                                                <td> - </td>
                                            @endif
                                            @php
                                                $lead_id = hashids_encode($activity->lead_client_id);
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
                    <h5 class="modal-title">Total Opened ({{ $file_details->opened ?? 0 }})</h5>
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
                                    @forelse ($get_file_activity as $k => $activity)
                                      @if ($activity->total_views > 0)
                                        <tr>
                                            <td> {{ $counter }} </td>
                                            <td> {{ $activity->file_lead->name }} </td>
                                            <td> {{ $activity->created_at->format('M-d-Y - h:i a') }} </td>
                                            @if ($activity->last_open != null)
                                                <td> {{ $activity->last_open->format('M-d-Y - h:i a') }} </td>
                                            @else
                                                <td> <small class="text-dark"><i class="fa-regular fa-envelope"></i> Unopened</small> </td>
                                            @endif

                                            @if ($activity->last_open != null && $activity->total_views > 0)
                                                <td> {{ $activity->total_views }} times</td>
                                            @else
                                                <td> - </td>
                                            @endif
                                            @php
                                                $lead_id = hashids_encode($activity->lead_client_id);
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
                    <h5 class="modal-title">Total Unopened ({{ $file_details->unopend ?? 0 }})</h5>
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
                                    @forelse ($get_file_activity as $k => $activity)
                                      @if ($activity->total_views == 0 && $activity->last_open == null)
                                        <tr>
                                            <td> {{ $counter }} </td>
                                            <td> {{ $activity->file_lead->name }} </td>
                                            <td> {{ $activity->created_at->format('M-d-Y - h:i a') }} </td>
                                            @if ($activity->last_open != null)
                                                <td> {{ $activity->last_open->format('M-d-Y - h:i a') }} </td>
                                            @else
                                                <td> <small class="text-dark"><i class="fa-regular fa-envelope"></i> Unopened</small> </td>
                                            @endif

                                            @if ($activity->last_open != null && $activity->total_views > 0)
                                                <td> {{ $activity->total_views }} times</td>
                                            @else
                                                <td> - </td>
                                            @endif
                                            @php
                                                $lead_id = hashids_encode($activity->lead_client_id);
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
                    <h5 class="modal-title">Viewed In Last 7 Days ({{ $file_details->viewed_in_last_7_days ?? 0 }})</h5>
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
                                      {{-- @if ($activity->total_views == 0 && $activity->last_open == null) --}}
                                        <tr>
                                            <td> {{ $counter }} </td>
                                            <td> {{ $activity->file_lead->name }} </td>
                                            <td> {{ $activity->created_at->format('M-d-Y - h:i a') }} </td>
                                            @if ($activity->last_open != null)
                                                <td> {{ $activity->last_open->format('M-d-Y - h:i a') }} </td>
                                            @else
                                                <td> <small class="text-dark"><i class="fa-regular fa-envelope"></i> Unopened</small> </td>
                                            @endif

                                            @if ($activity->last_open != null && $activity->total_views > 0)
                                                <td> {{ $activity->total_views }} times</td>
                                            @else
                                                <td> - </td>
                                            @endif
                                            @php
                                                $lead_id = hashids_encode($activity->lead_client_id);
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
                    <h5 class="modal-title">Viewed Miltiple Times ({{ $file_details->viewed_multiple_times ?? 0 }})</h5>
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
                                    @forelse ($get_file_activity as $k => $activity)
                                      @if ($activity->total_views > 1 && $activity->last_open != null)
                                        <tr>
                                            <td> {{ $counter }} </td>
                                            <td> {{ $activity->file_lead->name }} </td>
                                            <td> {{ $activity->created_at->format('M-d-Y - h:i a') }} </td>
                                            @if ($activity->last_open != null)
                                                <td> {{ $activity->last_open->format('M-d-Y - h:i a') }} </td>
                                            @else
                                                <td> <small class="text-dark"><i class="fa-regular fa-envelope"></i> Unopened</small> </td>
                                            @endif

                                            @if ($activity->last_open != null && $activity->total_views > 0)
                                                <td> {{ $activity->total_views }} times</td>
                                            @else
                                                <td> - </td>
                                            @endif
                                            @php
                                                $lead_id = hashids_encode($activity->lead_client_id);
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

    <div class="modal fade" id="renameFileModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Rename File</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('user.file_manager.update_file_name') }}" method="post" id="UpdateFileName">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="id" value="{{ $client_file->id }}">
                        <div class="form-group">
                            <label for="">Enter File Name</label>
                            <input type="text" name="file_name" class="form-control" value="{{ $client_file->file_name }}" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary form-submit-btn">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>



      {{-- modal  --}}
      <div class="modal fade" id="sendTemplateModal" tabindex="-1" aria-labelledby="sendTemplateModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Send File</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <!-- <form action="{{ route('user.file_manager.send_file') }}" method="post" id="pageSendForm"> -->
                    @csrf
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label>All Client <span class="text-danger">*</span></label>
                            <select name="client[]" id="client" class="form-control single-select" multiple required>
                                <option value="" disabled>Select...</option>
                                @foreach ($clients as $val)
                                <option value="{{ $val->hashid }}" data-client_id="{{ $val->hashid }}" data-lead_id="{{ $val->id }}" data-page_id="{{ $client_file->hashid }}" data-num="{{ $val->mobile_number }}" data-email="{{ $val->email }}" data-name="{{ $val->name }}">{{ $val->name }} ( {{ $val->email }} )</option>
                                @endforeach
                            </select>
                            <span id="error" style="display:none; color:red;">Please select a client</span>
                        </div>

                        <div class="form-group mb-3">
                            <label for="">File Title<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="title"
                                placeholder="Enter Template Title" id="send_title" readonly required>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="next_modal">Next</button>
                    </div>
                <!-- </form> -->
            </div>
        </div>
    </div>

    {{-- modal  --}}

    <div class="modal fade" id="sendTemplateModalNext" tabindex="-1" aria-labelledby="sendTemplateModalNext" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Send File</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <!-- <form action="{{ route('user.file_manager.send_file') }}" method="post" id="pageSendForm"> -->
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" value="{{ $client_file->id }}" name="file_id" id="file_id">
                        <input type="hidden" name="lead_id" id="lead_id">
                        <input type="hidden" name="client_num" id="client_num">
                        <input type="hidden" name="client_email" id="client_email">
                        <input type="hidden" name="activity_url" id="activity_url">
                        <input type="hidden" id="whatsapp_link">
                        
                        <div class="form-group mb-3">
                            <label for="">File Title<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="title"
                                placeholder="Enter Template Title" id="send_title_next" readonly required>
                        </div>
                        
                        <div class="row" id="client_div" style="">
                
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" id="back_modal">Back</button>
                        <a href="#" class="btn btn-primary" id="page_reload">Done</a>
                    </div>
                <!-- </form> -->
            </div>
        </div>
    </div>


@endsection

@section('page-scripts')
<script>

$(function(){
    $('.single-select').select2({
            dropdownParent: $('#sendTemplateModal')
        });
    });


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

    $('.rename_file').click(function (){
        $('#renameFileModal').modal('show')
    });

    $('#UpdateFileName').submit(function(e) {
        e.preventDefault();
        var url = $(this).attr('action');
        var param = new FormData(this);
        my_ajax(url, param, 'post', function(res) {
        },true);
    });






</script>



<script>

$('#pageNoteForm, #pageSendForm').submit(function(e) {
        e.preventDefault();
        var url = $(this).attr('action');
        var param = new FormData(this);
        window.open($('#whatsapp_link').val(), '_blank');
        my_ajax(url, param, 'post', function(res) {
        },true);
    });

    $(function(){
        $('.single-select').select2({
            dropdownParent: $('#sendTemplateModal')
        });
    });
    let selectedClients = [];
    $(document).on('change', '.single-select', function () {
        $('#error').hide();

        let clientName = $(this).find(':selected').attr('data-name');
        let clientNum = $(this).find(':selected').attr('data-num');
        let clientEmail = $(this).find(':selected').attr('data-email');
        let leadId = $(this).find(':selected').attr('data-lead_id');
        let pageID = $(this).find(':selected').attr('data-page_id');
        let clientID = $(this).find(':selected').attr('data-client_id');
        let url = '{{ route("client.file_view", [":pageID", ":clientID"]) }}';
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
        $("#whatsapp_link").val(whatsappLink);


        $(this).find(':selected').each(function() {
            let clientData = {
                value: $(this).val(),
                clientName: $(this).attr('data-name'),
                clientNum: $(this).attr('data-num'),
                clientEmail: $(this).attr('data-email'),
                leadId: $(this).attr('data-lead_id'),
                pageID: $(this).attr('data-page_id'),
                clientID: $(this).attr('data-client_id'),
                tempMessage: tempMessage,
                whatsappLink: whatsappLink
            };

            // Check if the leadId already exists in the selectedClients array
            const isDuplicate = selectedClients.some(client => client.leadId === clientData.leadId);

            if (!isDuplicate) {
                // If no duplicate, add clientData to selectedClients
                selectedClients.push(clientData);
            } 
        });

    });

    $('#next_modal').click(function() { 
        if (selectedClients.length == 0) {
            $('#error').show(); 
            return false;
        }  
        selectedClients.forEach(function(clientData) {
            if ($(`#file-preview-container-${clientData.leadId}`).length === 0) {
                let div = `<div id="file-preview-container-${clientData.leadId}">
                            <div class="card radius-10 border shadow-none mb-3 file-preview" style="width: 467px;">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="">
                                            <p class="mb-1">${clientData.clientName} (${clientData.clientNum})</p>
                                        </div>
                                        <div class="ms-auto">
                                            <button type="button" class="btn btn-success send_msg" id="snd_btn${clientData.leadId}" data-client_id="${clientData.value}" data-id="${clientData.leadId}" data-num="${clientData.clientNum}" data-name="${clientData.clientName}" data-message="${clientData.tempMessage}" data-whatsapplink="${clientData.whatsappLink}">Send</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>`;
                $('#client_div').append(div); // Append to the client div
            }
            
        });
        $('#send_title_next').val($('#send_title').val());
        $('#sendTemplateModal').modal('hide');
        $('#sendTemplateModalNext').modal('show');
    })

    $('body').on('click', '.send_msg', function() {
       
        id = $(this).data('id'); 
        client_id = $(this).data('client_id'); 
        num = $(this).data('num'); 
        clientName = $(this).data('name');
        message = $(this).data('message');
        whatsappLink = $(this).data('whatsapplink'); 
        
        file_id = $('#file_id').val(); 
        activity_url = $('#activity_url').val();
        send_title = $('#send_title_next').val(); 
        $('#snd_btn'+id).html('<span class="d-flex align-items-center"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div> Sending...</span>');

        $.ajax({
            url: '{{ route("user.file_manager.send_file") }}', 
            type: 'POST', 
            data: {
                lead_id: client_id, 
                title: send_title_next,
                file_id: file_id,
                file_id: file_id,
                title: send_title,
                _token: '{{ csrf_token() }}' 
            },
            success: function(response) {
                window.open(whatsappLink, '_blank');
                $('#snd_btn'+id).html('Sent');
                $('#snd_btn'+id).prop('disabled', true);
            },
            error: function(xhr, status, error) {
                // Handle error
                console.error('Error sending message template:', error);
                // You can add additional logic here, e.g., displaying an error message to the user
            }
        });
      
    })

    $('#back_modal').click(function() {
        $('#sendTemplateModalNext').modal('hide');
        $('#sendTemplateModal').modal('show');
    })
    $('#page_reload').click(function() {
        location.reload();
    })

     $(document).on('click', '.send-template', function (){
        let data = $(this).data('data');
        // console.log(data);

        $("#send_temp_id").val(data.id);
        $("#send_title").val(data.file_name);
        // $("#client_desc").val(data.description);
        // $("#client_num").val(data.mobile_number);
        // $("#client_email").val(data.email);
        // $("#send_templateMessage").val(data.description);
        $('#sendTemplateModal').modal({ backdrop: 'static', keyboard: false});
        $('#sendTemplateModal').modal('show');
    });


    $('#whatsapp_link').click(function () {
        $('#pageSendForm').submit();
    });
</script>


@endsection
