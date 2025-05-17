@extends('layouts.admin')

@section('content')
    <div class="page-breadcrumb d-flex align-items-center mb-3 mt-5">
        <div class="">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item active" aria-current="page">
                        <h4>{{ $client_file->file_name }}</h4>
                        {{-- <a class="btn btn-secondary mr-3 send-template" href="javascript:void(0);" data-data="{{$client_file}}">Send To Client</a> --}}
                    </li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <h5 class="text-dark"><strong>Sharing History</strong></h5>
        <div class="col-12 col-xl-8">
            <div class="card">
                <div class="card-body">
                    {{-- <a href="javascript:void(0);" class="text-dark total_share_view">
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
                    </a> --}}
                    {{-- <hr> --}}

                    <a href="javascript:void(0);" class="text-dark opend_files">
                        <div class="row g-0 mb-2">
                            <div class="col-12 d-flex justify-content-between align-items-center">
                                <div>
                                    <span>Opened</span>
                                </div>
                                <div class="d-flex align-items-center">
                                    {{-- <h6 class="mb-0"><strong>{{ $file_details->opened ?? 0 }}</strong></h6> --}}
                                    <h6 class="mb-0"><strong>{{ $total_views ?? 0 }}</strong></h6>

                                </div>
                            </div>
                        </div>
                    </a>
                    {{-- <hr> --}}

                    {{-- <a href="javascript:void(0);" class="text-dark un_opend_files">
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
                    </a> --}}

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
                                    {{-- <h6 class="mb-0"><strong> {{ $file_details->viewed_in_last_7_days ?? 0 }} </strong></h6> --}}
                                    <h6 class="mb-0"><strong> {{ $viewed_in_last_7_days_total ?? 0 }} </strong></h6>

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
                                    <h6 class="mb-0"><strong>{{ $viewed_in_last_7_days_total ?? 0 }}</strong></h6>
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
                            <div class="row no-gutters">
                                <div class="col-auto text-center flex-column d-sm-flex">

                                    <h5 class="m-2">
                                        <i class="fa-solid fa-paper-plane edit-activity" data-data="{{ $get_file_details->ebook->name }}" style="cursor: pointer;"></i>
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
                                                <h6 class="mb-0">{{ Str::limit($get_file_details->ebook->name, 17, '...') }}</h6>
                                            </div>

                                            <div class="d-flex w-100 justify-content-between">
                                                <small class="text-muted">{{ $get_file_details->created_at->format('M d, Y h:i a') }}</small>
                                            </div>
                                            @if ($get_file_details->total_views > 0)
                                                <small class="text-muted"><i class="fa-solid fa-envelope-open"></i> Last Open {{ $get_file_details->last_open->diffForHumans() }}</small><br>
                                                <small class="text-muted"><i class="fa-solid fa-glasses"></i> Viewed {{ $get_file_details->total_views }} times</small>
                                            @else
                                                <small class="text-muted"><i class="fa-regular fa-envelope"></i> Unopened</small>
                                            @endif

                                        </div>
                                    </div>
                                </div>
                            </div>
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
                                            <td> {{ $activity->lead->name ?? '' }} </td>
                                            <td> {{ $activity->created_at->format('M-d-Y - h:i a') ?? '' }} </td>
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
                                                <a class="text-dark" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Go To Client Detail" data-bs-original-title="Go To Client Detail" aria-label="Go To Client Detail" href="{{ route('admin.lead-management.client_details', $lead_id) }}"><i class="fa-solid fa-up-right-from-square"></i></a>
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
                                           <td> {{ $activity->created_at->format('M-d-Y - h:i a') ?? '' }} </td>
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
                                               <a class="text-dark" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Go To Client Detail" data-bs-original-title="Go To Client Detail" aria-label="Go To Client Detail" href="{{ route('admin.lead-management.client_details', $lead_id) }}"><i class="fa-solid fa-up-right-from-square"></i></a>
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
                                           <td> {{ $activity->created_at->format('M-d-Y - h:i a') ?? '' }} </td>
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
                                               <a class="text-dark" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Go To Client Detail" data-bs-original-title="Go To Client Detail" aria-label="Go To Client Detail" href="{{ route('admin.lead-management.client_details', $lead_id) }}"><i class="fa-solid fa-up-right-from-square"></i></a>
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
                                            <td> {{ $activity->created_at->format('M-d-Y - h:i a') ?? '' }} </td>
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
                                                <a class="text-dark" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Go To Client Detail" data-bs-original-title="Go To Client Detail" aria-label="Go To Client Detail" href="{{ route('admin.lead-management.client_details', $lead_id) }}"><i class="fa-solid fa-up-right-from-square"></i></a>
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
                                            <td> {{ $activity->created_at->format('M-d-Y - h:i a') ?? '' }} </td>
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
                                                <a class="text-dark" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Go To Client Detail" data-bs-original-title="Go To Client Detail" aria-label="Go To Client Detail" href="{{ route('admin.lead-management.client_details', $lead_id) }}"><i class="fa-solid fa-up-right-from-square"></i></a>
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


    {{-- modal  --}}
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
                        <input type="hidden" value="{{ $client_file->id }}" name="page_id">
                        <input type="hidden" name="lead_id" id="lead_id">
                        <input type="hidden" name="client_num" id="client_num">
                        <input type="hidden" name="client_email" id="client_email">
                        <input type="hidden" name="activity_url" id="activity_url">
                       
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


@endsection
@section('page-scripts')

<script>
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
     $(document).on('click', '.send-template', function (){
        let data = $(this).data('data');
        $("#send_temp_id").val(client_file.id);
        $("#send_title").val(client_file.file_name);
        $("#client_desc").val(client_file.description);
        $("#client_num").val(client_file.mobile_number);
        $("#client_email").val(client_file.email);
        $("#send_templateMessage").val(client_file.description);
        $('#sendTemplateModal').modal({ backdrop: 'static', keyboard: false});
        $('#sendTemplateModal').modal('show');
    });
</script>

@endsection
