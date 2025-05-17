@extends('layouts.front')

@section('page-css')
<link href="{{ asset('front') }}/assets/plugins/datetimepicker/css/classic.css" rel="stylesheet" />
<link href="{{ asset('front') }}/assets/plugins/datetimepicker/css/classic.time.css" rel="stylesheet" />
<link href="{{ asset('front') }}/assets/plugins/datetimepicker/css/classic.date.css" rel="stylesheet" />
<link rel="stylesheet" href="{{ asset('front') }}/assets/plugins/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.min.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endsection

@section('content')

@php
function getCurrentDateTime() {
    return date('d-m-y h:i A');
}
@endphp


<div class="row">
    <div class="col">
        <a href="Javascript:void(0);" onclick="goBack()" class="btn btn-dark"><i class="fa-solid fa-angle-left"></i> Back</a>
    </div>
</div>

<div class="page-breadcrumb d-flex align-items-center mb-3 mt-5">
    <div class="breadcrumb-title">{{@$breadcrumb_main}}</div>
    <div class="">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item active" aria-current="page">
                    <h4>{{@$breadcrumb}}</h4>
                </li>
            </ol>
        </nav>
    </div>
    <div class="ms-auto">
        <div class="btn-group">
            <button type="button" class="btn btn-primary split-bg-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown"> <span class="">Options</span></button>
            <div class="dropdown-menu dropdown-menu-right dropdown-menu-lg-end">
                <a class="dropdown-item edit-contact" href="javascript:void(0);" data-data="{{ $data }}">Edit Contact Details</a>
                <a class="dropdown-item client-note" href="javascript:void(0);" data-data="{{ $data }}">Edit Client Notes</a>
                <a class="dropdown-item change-status" href="javascript:void(0);">Mark Lead As</a>
                <a class="dropdown-item text-danger" href="javascript:void(0);" onclick="ajaxRequest(this)" data-url="{{route('user.leads-management.delete',$data->hashid)}}">Delete Client</a>
            </div>
        </div>
    </div>
</div>


<div class="d-flex align-items-center">
    <form action="{{ route('user.leads-management.set_follow_up') }}" method="POST" id="followUpForm">
        @csrf
        <div class="mb-3">
            <input type="hidden" name="id" value="{{ $data->id }}">
            @if(!empty($data->follow_up_date_time))
                <a href="javascript:;" onclick="ajaxRequest(this)" data-url="{{route('user.leads-management.unset_follow_up',$data->id)}}" class="text-danger" style="float: right;position: relative;top: 30px;left: 20px;"><i class="fa fa-trash"></i></a>
            @endif

            <!-- Input field for the follow-up date -->
            <input type="text" name="follow_up_date_time"  class="result form-control" id="date-time" value="{{ isset($data->follow_up_date_time) ? \Carbon\Carbon::parse($data->follow_up_date_time)->format('d-m-y h:i A') : '' }}" placeholder="<?php echo getCurrentDateTime(); ?>" autocomplete="off"/>
            <!-- <input class="result form-control" type="text" name="follow_up_date_time" id="date-time" value="{{ $data->follow_up_date_time }}" placeholder="<?php echo getCurrentDateTime(); ?>"> -->
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <button type="submit" class="btn btn-secondary btn mt-2">Set Follow Up</button>
            </div>
        </div>
    </form>
</div>


<div class="row mt-4">
    <div class="col-12 col-md-6 col-lg-4">
        <h5><strong> Client Info </strong></h5>
        <div class="card">
            <div class="card-body">
                <div class="row g-0">
                    <div class="col-12">
                        <div class="col-12 col-md-12">
                            <span><strong>DISPLAY NAME</strong></span>
                            <p>{{ $data->name }}</p>
                        </div>
                        <div class="col-12 col-md-12">
                            <div class="row">
                                <div class="col-8">
                                    <span><strong>MOBILE NUMBER</strong></span>
                                    <p>+65{{ $data->mobile_number }}</p>
                                </div>
                                <div class="col-4">
                                    <a href="tel:65{{ $data->mobile_number }}" class="save_timeline" data-type="phone">
                                        <i class="fa-solid fa-phone-flip" style="color: #17a2b8;font-size: 1.5em;
                                        line-height: .75em;"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-8">
                                    <span><strong>WHATSAPP NUMBER</strong></span>
                                    <p>+65{{ $data->mobile_number }}</p>
                                </div>
                                <div class="col-4">
                                    <a href="https://api.whatsapp.com/send?phone=65{{ $data->mobile_number }}" target="_blank" class="save_timeline" data-type="message"> <i
                                            class="fa-brands fa-whatsapp" style="color: #17a2b8;font-size: 1.5em;
                                    line-height: .75em;"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-8">
                                    <span><strong>EMAIL ADDRESS</strong></span>
                                    <p>{{ $data->email }}</p>
                                </div>
                                <div class="col-4">
                                    <a href="mailto:{{ $data->email }}" class="save_timeline" data-type="email">
                                        <i class="fa-solid fa-envelope" style="color: #17a2b8;font-size: 1.5em;
                                        line-height: .75em;"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-8">
                                    <span><strong>LEAD SOURCE</strong></span>
                                    <p>{{ucfirst($data->lead_type)}} {{ $data->lead_source->name ?? '' }}</p>
                                </div>
                                <div class="col-4">

                                </div>
                            </div>
                        </div>
                        <hr>
                        @if ($data->lead_data()->count() > 0)
                        @foreach ($data->lead_data as $k => $item)
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-8">
                                    <span><strong>{{ $item->key }}</strong></span>
                                    <p>{{ $item->value }}</p>
                                </div>
                                <div class="col-4">
                                    {{-- <a href="mailto:{{ $data->email }}" class="save_timeline" data-type="email">
                                        <i class="fa-solid fa-envelope" style="color: #17a2b8;font-size: 1.5em;
                                        line-height: .75em;"></i>
                                    </a> --}}
                                </div>
                            </div>
                        </div>
                        @endforeach
                        @endif
                    </div>
                    <div class="col-12">
                        <div class="col-md-12 mb-3">
                            <label class="form-label"><strong>GROUPS</strong></label>
                            <input type="hidden" name="group_lead_id" id="group_lead_id" value="{{ $data->hashid }}">
                            <select class="form-control multiple-select2 add_group_lead" multiple="multiple">
                                @foreach ($client_groups as $group)
                                <option value="{{ $group->id }}" {{ in_array($group->id, $data->lead_groups()->pluck('group_id')->toArray()) ? 'selected' : '' }}> {{ $group->group_title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12 client-note" style="cursor: pointer;" data-data="{{ $data }}">
                            <span><strong>NOTES</strong></span>
                            <p>{{ $data->note ?? 'Click to add notes about your client...' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-6 col-lg-4">
        <h5><strong> Whatsapp </strong></h5>
        <x-lead_management.chat_container :toPhoneNumber="$to_phone_number" :role="'client'"/>
    </div>

    <div class="col-12 col-md-6 col-lg-4">
        <h5><strong> Timeline </strong></h5>
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <!-- timeline item 1 left dot -->
                    <div class="col-auto text-center flex-column d-sm-flex">
                        <div class="row h-50">
                            <div class="col">&nbsp;</div>
                            <div class="col">&nbsp;</div>
                        </div>
                        <h5 class="">
                            <i class="fa-solid fa-circle-plus add-activity" style="cursor: pointer;"></i>
                        </h5>
                        <div class="row h-50">
                            <div class="col border-end">&nbsp;</div>
                            <div class="col">&nbsp;</div>
                        </div>
                    </div>
                </div>
                @foreach ($data->activity()->orderBy('created_at', 'desc')->get() as $k => $item) 
                    <div class="row">
                        @if ($item->type == 'add')
                        <div class="col-auto text-center flex-column d-sm-flex">
                            <div class="row h-50">
                                <div class="col border-end">&nbsp;</div>
                                <div class="col">&nbsp;</div>
                            </div>
                            <h5 class="">
                                <i class="fa-solid fa-user-plus" style=" font-size: 16px; "></i>
                            </h5>
                            <div class="row h-50">
                                <div class="col">&nbsp;</div>
                                <div class="col">&nbsp;</div>
                            </div>
                        </div>
                        @elseif ($item->type == 'message')
                        <div class="col-auto text-center flex-column d-sm-flex">
                            <div class="row h-50">
                                <div class="col border-end">&nbsp;</div>
                                <div class="col">&nbsp;</div>
                            </div>
                                @if ((isset($item->message_template_id) && !empty($item->message_template_id)))
                                    <h5 class="">
                                        <i class="fa-solid fa-paper-plane" style=" font-size: 16px;"></i>
                                    </h5>
                                @else
                                    <h5 class="">
                                        <i class="fa-regular fa-comment edit-activity" data-data="{{ $item }}" style=" font-size: 16px; cursor: pointer;"></i>
                                    </h5>
                                @endif
                            <div class="row h-50">
                                <div class="col border-end">&nbsp;</div>
                                <div class="col">&nbsp;</div>
                            </div>
                        </div>
                        @elseif ($item->type == 'attachment')
                        <div class="col-auto text-center flex-column d-sm-flex">
                            <div class="row h-50">
                                <div class="col border-end">&nbsp;</div>
                                <div class="col">&nbsp;</div>
                            </div>
                                    <h5 class="">
                                        <i class="fa fa-paperclip edit-activity" data-data="{{ $item }}" style=" font-size: 16px; cursor: pointer;"></i>
                                    </h5>
                            <div class="row h-50">
                                <div class="col border-end">&nbsp;</div>
                                <div class="col">&nbsp;</div>
                            </div>
                        </div>
                        @elseif ($item->type == 'email')
                        <div class="col-auto text-center flex-column d-sm-flex">
                            <div class="row h-50">
                                <div class="col border-end">&nbsp;</div>
                                <div class="col">&nbsp;</div>
                            </div>
                                @if ((isset($item->email_template_id) && !empty($item->email_template_id)))
                                    <h5 class="">
                                        <i class="fa-solid fa-paper-plane" style=" font-size: 16px;"></i>
                                    </h5>
                                @else
                                    <h5 class="">
                                        <i class="fa-regular fa-comment edit-activity" data-data="{{ $item }}" style=" font-size: 16px; cursor: pointer;"></i>
                                    </h5>
                                @endif
                            <div class="row h-50">
                                <div class="col border-end">&nbsp;</div>
                                <div class="col">&nbsp;</div>
                            </div>
                        </div>
                        @elseif ($item->type == 'phone')
                        <div class="col-auto text-center flex-column d-sm-flex">
                            <div class="row h-50">
                                <div class="col border-end">&nbsp;</div>
                                <div class="col">&nbsp;</div>
                            </div>
                            <h5 class="">
                                <i class="fa-solid fa-phone-flip edit-activity" data-data="{{ $item }}" style="cursor: pointer;"></i>
                            </h5>
                            <div class="row h-50">
                                <div class="col border-end">&nbsp;</div>
                                <div class="col">&nbsp;</div>
                            </div>
                        </div>
                        @elseif ($item->type == 'meeting')
                        <div class="col-auto text-center flex-column d-sm-flex">
                            <div class="row h-50">
                                <div class="col border-end">&nbsp;</div>
                                <div class="col">&nbsp;</div>
                            </div>
                            <h5 class="">
                                <i class="fa-solid fa-calendar-day edit-activity" data-data="{{ $item }}" style="cursor: pointer;"></i>
                            </h5>
                            <div class="row h-50">
                                <div class="col border-end">&nbsp;</div>
                                <div class="col">&nbsp;</div>
                            </div>
                        </div>
                        @elseif ($item->type == 'note')
                        <div class="col-auto text-center flex-column d-sm-flex">
                            <div class="row h-50">
                                <div class="col border-end">&nbsp;</div>
                                <div class="col">&nbsp;</div>
                            </div>
                            <h5 class="">
                                <i class="fa-solid fa-note-sticky edit-activity" data-data="{{ $item }}" style="cursor: pointer;"></i>
                            </h5>
                            <div class="row h-50">
                                <div class="col border-end">&nbsp;</div>
                                <div class="col">&nbsp;</div>
                            </div>
                        </div>
                        @elseif ($item->type == 'file')
                        <div class="col-auto text-center flex-column d-sm-flex">
                            <div class="row h-50">
                                <div class="col border-end">&nbsp;</div>
                                <div class="col">&nbsp;</div>
                            </div>
                            <h5 class="">
                                <i class="fa-solid fa-paper-plane edit-activity" data-data="{{ $item }}" style="cursor: pointer;"></i>
                            </h5>
                            <div class="row h-50">
                                <div class="col border-end">&nbsp;</div>
                                <div class="col">&nbsp;</div>
                            </div>
                        </div>
                        @endif
                        <div class="col py-2">
                            <div class="row">
                                <div class="col-12 col-lg-12 d-flex">
                                    <div class="card shadow radius-10 w-100">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <h6 class="mb-0">{{ Str::limit($item->title, 17, '...') }}</h6>
                                                @if ($item->type !== 'add')
                                                <div class="fs-5 ms-auto dropdown">
                                                    <div class="dropdown-toggle dropdown-toggle-nocaret cursor-pointer" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></div>
                                                    <ul class="dropdown-menu">
                                                        @if (isset($item->message_template_id) && !empty($item->message_template_id))
                                                            <li><a class="dropdown-item text-success float-end" href="{{ route('user.message-template.temp_details', hashids_encode($item->message_template_id)) }}">Go To Message Detail</a></li>
                                                            <li><a class="dropdown-item text-danger float-end" onclick="ajaxRequest(this)" data-url="{{route('user.leads-management.activity_delete',$item->id)}}" href="Javascript:void(0);">Delete</a></li>
                                                        @elseif(isset($item->email_template_id) && !empty($item->email_template_id))
                                                            <li><a class="dropdown-item text-success float-end" href="{{ route('user.email-template.temp_details', hashids_encode($item->email_template_id)) }}">Go To Email Detail</a></li>
                                                            <li><a class="dropdown-item text-danger float-end" onclick="ajaxRequest(this)" data-url="{{route('user.leads-management.activity_delete',$item->id)}}" href="Javascript:void(0);">Delete</a></li>
                                                        @elseif($item->type == "file")
                                                        @php
                                                            $file_id = hashids_encode($item->file_id)
                                                        @endphp
                                                            <li><a class="dropdown-item text-success" href="{{ route('user.file_manager.file_detail', $file_id) }}">Go To File Detail</a></li>
                                                            <li><a class="dropdown-item text-danger float-end" onclick="ajaxRequest(this)" data-url="{{route('user.leads-management.activity_delete',$item->id)}}" href="Javascript:void(0);">Delete</a></li>
                                                        @else
                                                            <li><a class="dropdown-item text-success edit-activity float-end" data-data="{{ $item }}" data-attachments="{{$item->attachments}}" href="Javascript:void(0);">Edit</a></li>
                                                            <li><a class="dropdown-item text-danger float-end" onclick="ajaxRequest(this)" data-url="{{route('user.leads-management.activity_delete',$item->id)}}" href="Javascript:void(0);">Delete</a></li>
                                                        @endif
                                                    </ul>
                                                </div>
                                                @endif
                                            </div>
                                            @if ($item->type == 'file')
                                                <div class="d-flex w-100 justify-content-between">
                                                    <small class="text-muted">{{ date('M d, Y h:i a',strtotime($item->date_time)) }}</small>
                                                </div>
                                                @if ($item->total_views > 0)
                                                    <small class="text-muted"><i class="fa-solid fa-envelope-open"></i> Last Open {{ \Carbon\Carbon::parse($item->last_open)->diffForHumans() }}</small><br>
                                                    <small class="text-muted"><i class="fa-solid fa-glasses"></i></i> Viewed {{ $item->total_views }} times</small>
                                                @else
                                                    <small class="text-muted"><i class="fa-regular fa-envelope"></i> Unopened</small>
                                                @endif

                                            @else
                                                <div class="d-flex w-100 justify-content-between">
                                                    <small class="text-muted">{{ date('M d, Y h:i a',strtotime($item->date_time)) }}</small>
                                                </div>
                                                @foreach($item->attachments as $attachment)
                                                    <a href="{{ asset('/') }}{{$attachment->file_url}}" target="_blank">{{$attachment->file_name}}</a> <br>
                                                @endforeach
                                                <small class="text-muted">{{ Str::limit($item->description, 100, '...') }}</small>
                                            @endif

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="clientNoteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Client Notes</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('user.leads-management.save') }}" method="post" id="leadForm">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="id" id="note_client_id">
                    <input type="hidden" name="type" value="note">
                    <textarea name="note" class="form-control" id="client_note" cols="30"
                        rows="10" required></textarea>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary form-submit-btn">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editContactDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Contact Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('user.leads-management.save') }}" method="post" id="leadForm">
                @csrf
                <input type="hidden" name="id" id="client_id">
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="">Client Name</label>
                        <input type="text" class="form-control" name="client_name" id="client_name" value="{{ $data->name }}" placeholder="Enter Client NAME" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="">Email</label>
                        <input type="email" class="form-control" name="email" id="client_email" value="{{ $data->email }}" placeholder="Enter Email" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="">Mobile Number</label>
                        <div class="input-group mb-3">
                            <span class="input-group-text bg-white p-0" id="basic-addon1">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="30" zoomAndPan="magnify" viewBox="0 0 30 30.000001" height="40" preserveAspectRatio="xMidYMid meet" version="1.0"><defs><clipPath id="id1"><path d="M 2.675781 6.132812 L 27.355469 6.132812 L 27.355469 24.277344 L 2.675781 24.277344 Z M 2.675781 6.132812 " clip-rule="nonzero"/></clipPath><clipPath id="id2"><path d="M 2.675781 6.132812 L 27.355469 6.132812 L 27.355469 16 L 2.675781 16 Z M 2.675781 6.132812 " clip-rule="nonzero"/></clipPath><clipPath id="id3"><path d="M 4 6.132812 L 10 6.132812 L 10 15 L 4 15 Z M 4 6.132812 " clip-rule="nonzero"/></clipPath></defs><g clip-path="url(#id1)"><path fill="rgb(93.328857%, 93.328857%, 93.328857%)" d="M 27.347656 21.488281 C 27.347656 23.027344 26.121094 24.277344 24.609375 24.277344 L 5.421875 24.277344 C 3.910156 24.277344 2.683594 23.027344 2.683594 21.488281 L 2.683594 8.925781 C 2.683594 7.382812 3.910156 6.132812 5.421875 6.132812 L 24.609375 6.132812 C 26.121094 6.132812 27.347656 7.382812 27.347656 8.925781 Z M 27.347656 21.488281 " fill-opacity="1" fill-rule="nonzero"/></g><g clip-path="url(#id2)"><path fill="rgb(92.939758%, 16.079712%, 22.349548%)" d="M 27.347656 15.207031 L 27.347656 8.925781 C 27.347656 7.382812 26.121094 6.132812 24.609375 6.132812 L 5.421875 6.132812 C 3.910156 6.132812 2.683594 7.382812 2.683594 8.925781 L 2.683594 15.207031 Z M 27.347656 15.207031 " fill-opacity="1" fill-rule="nonzero"/></g><g clip-path="url(#id3)"><path fill="rgb(100%, 100%, 100%)" d="M 6.792969 10.671875 C 6.792969 8.867188 7.90625 7.355469 9.402344 6.945312 C 9.117188 6.875 8.816406 6.832031 8.507812 6.832031 C 6.425781 6.832031 4.738281 8.550781 4.738281 10.671875 C 4.738281 12.789062 6.425781 14.507812 8.507812 14.507812 C 8.816406 14.507812 9.117188 14.464844 9.402344 14.394531 C 7.90625 13.984375 6.792969 12.472656 6.792969 10.671875 Z M 6.792969 10.671875 " fill-opacity="1" fill-rule="nonzero"/></g><path fill="rgb(93.328857%, 93.328857%, 93.328857%)" d="M 10.90625 7.53125 L 11.058594 8.011719 L 11.554688 8.011719 L 11.152344 8.308594 L 11.308594 8.792969 L 10.90625 8.492188 L 10.5 8.792969 L 10.65625 8.308594 L 10.253906 8.011719 L 10.75 8.011719 Z M 9.535156 12.414062 L 9.6875 12.898438 L 10.1875 12.898438 L 9.78125 13.195312 L 9.9375 13.675781 L 9.535156 13.378906 L 9.132812 13.675781 L 9.285156 13.195312 L 8.882812 12.898438 L 9.378906 12.898438 Z M 12.273438 12.414062 L 12.429688 12.898438 L 12.925781 12.898438 L 12.523438 13.195312 L 12.675781 13.675781 L 12.273438 13.378906 L 11.871094 13.675781 L 12.027344 13.195312 L 11.625 12.898438 L 12.121094 12.898438 Z M 8.847656 9.625 L 9.003906 10.105469 L 9.5 10.105469 L 9.097656 10.402344 L 9.253906 10.886719 L 8.847656 10.585938 L 8.445312 10.886719 L 8.601562 10.402344 L 8.199219 10.105469 L 8.695312 10.105469 Z M 12.960938 9.625 L 13.113281 10.105469 L 13.613281 10.105469 L 13.207031 10.402344 L 13.363281 10.886719 L 12.960938 10.585938 L 12.558594 10.886719 L 12.710938 10.402344 L 12.308594 10.105469 L 12.804688 10.105469 Z M 12.960938 9.625 " fill-opacity="1" fill-rule="nonzero"/></svg>
                                +65
                            </span>
                            <input type="text" oninput="this.value = this.value.replace(/[^0-9]/g, '');" value="{{ $data->mobile_number }}" maxlength="8" class="form-control" id="client_mobile_number" name="mobile_number" placeholder="Enter Mobile Number">
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="">Lead Source</label>
                        <select name="lead_source" class="form-control" required>
                            <option value="manual" {{ $data->lead_source == null ? 'selected' : '' }}>Manual</option>
                            <option value="1" {{ $data->source_type_id == '1' ? 'selected' : '' }}>MiniZapier Webhook</option>
                            <option value="2" {{ $data->source_type_id == '2' ? 'selected' : '' }}>WPForms Webhook</option>
                            <option value="3" {{ $data->source_type_id == '3' ? 'selected' : '' }}>WordPress Webhook</option>
                            <option value="4" {{ $data->source_type_id == '4' ? 'selected' : '' }}>MetaLead Webhook</option>
                            <option value="5" {{ $data->source_type_id == '5' ? 'selected' : '' }}>PPC Webhook</option>
                            <option value="6" {{ $data->source_type_id == '6' ? 'selected' : '' }}>RR (Round Robin) Webhook</option>
                            <option value="7" {{ $data->source_type_id == '7' ? 'selected' : '' }}>Zapier Webhook</option>
                            <option value="8" {{ $data->source_type_id == '8' ? 'selected' : '' }}>Unknown Webhook</option>
                        </select>
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
                                @if ($data->lead_data()->count() > 0)
                                    @foreach ($data->lead_data as $k => $item)
                                            <tr id="lead_data_tr_{{$k}}">
                                                <td>
                                                    <input type="text" class="form-control" name="data[{{$k}}][key]" placeholder="Enter Data Key" value="{{ $item->key }}" required>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control" name="data[{{$k}}][value]" placeholder="Enter Data Value" value="{{ $item->value }}" required>
                                                </td>
                                                @if ($k == 0)
                                                    <td>
                                                        <a href="javascript:void(0);" class="btn btn-primary add_lead_data_tr"><i class="fa-solid fa-circle-plus" style="margin-left: 0px; vertical-align: initial;"></i></a>
                                                    </td>
                                                @else
                                                <td>
                                                    <a href="javascript:void(0);" class="btn btn-danger delete_lead_data_tr" data-id="{{$k}}"><i class="fa-solid fa-trash" style="margin-left: 0px; vertical-align: initial;"></i></a>
                                                </td>
                                                @endif
                                            </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td>
                                            <input type="text" class="form-control" name="data[0][key]" placeholder="Enter Data Key" required>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" name="data[0][value]" placeholder="Enter Data Value" required>
                                        </td>
                                        <td>
                                            <a href="javascript:void(0);" class="btn btn-primary add_lead_data_tr"><i class="fa-solid fa-circle-plus" style="margin-left: 0px; vertical-align: initial;"></i></a>
                                        </td>
                                    </tr>
                                @endif

                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary form-submit-btn">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="activityModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Activity</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('user.leads-management.activity_save') }}" method="post" id="activityForm">
                @csrf
                <input type="hidden" name="lead_client_id" value="{{ $data->id }}">
                <input type="hidden" name="id" id="activity_id" value="">
                <div class="modal-body">
                    <div class="row save_activity_row" style="display: none;" >
                        <div class="col"></div>
                    </div>
                    <div class="row activity_model_row1" style="display: none;" >
                        <div class="col mb-2">
                            <select name="activity_type" id="activity_type" class="form-select" required>
                                <option value="">Choose Activity</option>
                                <option value="phone">Phone Call</option>
                                <option value="message">Message</option>
                                <option value="meeting">Meeting</option>
                                <option value="note">Note</option>
                                <option value="attachment">Attachments</option>
                            </select>
                        </div>
                        <div class="col mb-2">
                            <input type="text" name="title" id="activity_title" class="form-control" placeholder="Title" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-2">
                            <input class="result form-control" type="text" name="date_time" id="activity-date-time" placeholder="<?php echo getCurrentDateTime(); ?>" autocomplete="off" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-2">
                            <textarea name="description" id="activity_description" class="form-control" cols="30" rows="10" placeholder="Add optional details here…"></textarea>
                        </div>
                    </div>
                    <div class="row" id="attachment_div">
                        <!-- Add Attachment Card -->
                        <div class="card radius-10 border shadow-none mb-3" style="width: 467px; cursor: pointer; margin-left: 12px;" id="upload-card">
                            <div class="card-body" style="position: relative;">
                                <div class="d-flex align-items-center">
                                    <div class="">
                                        <p class="mb-1"><i class="fa-solid fa-circle-plus add-activity"></i> Add Attachment</p>
                                    </div>
                                </div>
                            </div>
                            <!-- File input (hidden) -->
                            <input type="file" id="file-upload" name="attachments[]" multiple style="display:none"/>
                        </div>

                        <!-- File Preview Container -->
                        <div id="file-preview-container"></div>

                        <!-- Optional max file count message -->
                        <div id="file-count" style="color: red; display: none;"></div>
                    </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary form-submit-btn">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="changeStatusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Change Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('user.leads-management.update_status') }}" method="post" id="UpdateStatusForm">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="id" value="{{ $data->id }}">
                    <div class="form-group">
                        <select name="status" class="form-control" id="leadstatus" required>
                            <option value="">Choose Status</option>
                            @foreach (['unmarked' => 'Unmarked', 'spam' => 'Spam', 'uncontacted' => 'Uncontacted', 'contacted' => 'Contacted'] as $k => $val)
                                <option value="{{$k}}" {{ $data->status == $k ? 'selected' : '' }}>{{ $val }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary form-submit-btn">Save</button>
                </div>
            </form>
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
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script>
    $('#activity_type').click(function() {
        activity_type = $('#activity_type').val();
        if(activity_type == 'attachment'){
            $('#attachment_div').show();
            $('#file-preview-container').html('');
            $('#file-count').hide();
            return;
        }
        // $('#attachment_div').hide();
    })
</script>
<script>
    uploadedFiles = [];
$(document).ready(function () {
    let maxFiles = 5;
    // Trigger file input when the card is clicked
    $('#upload-card').click(function (e) { 
        if(uploadedFiles.length == maxFiles){
            $('#file-count').show().text(`You can upload only ${maxFiles} files!`);
            return;
        }
        
        if (uploadedFiles.length < maxFiles && e.target.id !== 'file-upload') { 
            $('#file-upload').click(); // Open file selection dialog
        }
    });

    // Handle file input change event
    $('#file-upload').change(function (event) {
        let files = event.target.files; // Get selected files
        let fileCount = uploadedFiles.length + files.length;
    
        // Ensure file limit is respected
        if (fileCount > maxFiles) {
            $('#file-count').show().text(`You can upload only ${maxFiles} files!`);
            return;
        } else {
            $('#file-count').hide(); // Hide error if within limit
        }

        // Loop through the files
        $.each(files, function (index, file) {
            uploadedFiles.push(file); // Track uploaded files

            let reader = new FileReader();
            let fileType = file.type;

            // Create preview or download link depending on file type
            if (fileType.startsWith('image/')) {
                // If file is an image, display a preview
                reader.onload = function (e) {
                    let imgPreview = `<div class="card radius-10 border shadow-none mb-3 file-preview" style="width: 467px;">
                        <div class="card-body" style="position: relative;">
                            <div class="d-flex align-items-center">
                                <div class="">
                                    <p class="mb-1"><i class="fa fa-paperclip" aria-hidden="true"></i> ${file.name}</p>
                                </div>
                                <div class="ms-auto">
                                    <i class="fa fa-trash cursor-pointer text-danger remove-file" data-filename="${file.name}" style="font-size: 20px;"></i>
                                </div>
                            </div>
                        </div>
                    </div>`;
                    $('#file-preview-container').append(imgPreview);
                };
                reader.readAsDataURL(file);
            } else {
                // For non-image files, create a download link
                let tempUrl = URL.createObjectURL(file);
                let fileDownload = `<div class="card radius-10 border shadow-none mb-3 file-preview" style="width: 467px;">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="">
                                    <p class="mb-1"><i class="fa fa-paperclip" aria-hidden="true"></i> ${file.name}</p>
                                </div>
                                <div class="ms-auto">
                                    <i class="fa fa-trash cursor-pointer text-danger remove-file" data-filename="${file.name}" style="font-size: 20px;"></i>
                                </div>
                            </div>
                        </div>
                    </div>`;
                $('#file-preview-container').append(fileDownload); 
                
            }
        });

    });

    // Handle remove file action
    $(document).on('click', '.remove-file', function () {
        let fileName = $(this).data('filename');
        // Remove the corresponding file from uploadedFiles array
        uploadedFiles = uploadedFiles.filter(file => file.name !== fileName);
        uploadedFiles = uploadedFiles.filter(file => file.file_name !== fileName);


        // Remove the file preview/download link from the DOM
        $(this).closest('.file-preview').remove();
        // Hide error message if total files are less than maxFiles again
        if (uploadedFiles.length < maxFiles) {
            $('#file-count').hide();
        }
    });

    // Optional: Preview attachment from dropdown action
    $('#preview-attachment').click(function () {
        if (uploadedFiles.length > 0) {
            alert("Files are ready for upload, check the preview or download.");
        } else {
            alert("No file uploaded to preview!");
        }
    });
});

</script>
<script>
    $(function() {
        // Initialize the date picker
        $('input[name="follow_up_date_time"]').daterangepicker({
            singleDatePicker: true,  // Enable single date selection
            timePicker: true,        // Enable time picker
            autoUpdateInput: false,  // Prevent auto-update when date picker opens
            locale: {
                format: 'DD-M-Y hh:mm A'  // Display format of the date/time
            }
        });

        // Get the value of the input field (initial date if it exists)
        var initialDate = $('#follow_up_date_time').val();

        // If there's an existing date in the input, set it in the date picker
        if (initialDate) {
            $('input[name="follow_up_date_time"]').data('daterangepicker').setStartDate(moment(initialDate, 'M/DD hh:mm A'));
        }

        // Update the input value when a date is selected
        $('input[name="follow_up_date_time"]').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD-M-Y hh:mm A'));
        });

        // Clear the input field if the picker is canceled
        $('input[name="follow_up_date_time"]').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });
    });
</script>

<script>

    $('.multiple-select2').select2({
        // theme: 'bootstrap4',
        // width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
        // placeholder: $(this).data('placeholder'),
        allowClear: Boolean($(this).data('allow-clear')),
        tags: true
    });



    $(document).on('change', '.add_group_lead', function () {
        var groups = $(this).val();
        var group_lead_id = $('#group_lead_id').val();
        var data = {'_token': "{{ csrf_token() }}", groups: groups, group_lead_id: group_lead_id};
        $.ajax({
            url: "{{ route('user.leads-management.group_lead_save') }}",
            type: 'POST',
            data: data,
            success: function(response) {

            },
            error: function(xhr, status, error) {

            }
        });
    });


    $(".client-note").click(function () {
        let data = $(this).data('data');
        $("#note_client_id").val(data.id);
        $("#client_note").val(data.note);
        $('#clientNoteModal').modal('show');
    });

    $(".edit-contact").click(function () {
        let data = $(this).data('data');
        $("#client_id").val(data.id);
        $("#client_name").val(data.name);
        $("#client_email").val(data.email);
        $("#client_mobile_number").val(data.mobile_number);
        $('#editContactDetailsModal').modal('show');
    });

    $(function() {
        // Initialize the datetime picker for the input field with id 'activity-date-time'
        $('#activity-date-time').daterangepicker({
            singleDatePicker: true,  // Enable single date selection
            timePicker: true,        // Enable time picker
            locale: {
                format: 'DD-M-Y hh:mm A'  // Format of the date/time
            }
        });

        // When the 'Add Activity' button is clicked
        $(".add-activity").click(function () { 
            $(".activity_model_row1").show();
            $(".save_activity_row").hide();
            // $('#attachment_div').hide();
            $('#activityModal').find('.modal-title').html('Add Activity');
            $('#activity_id').val('');
            $('#activity_type').val('');
            $('#activity_title').val('');
            $('#activity_description').val('');
            // Set the current date and time to the datetime picker input
            $('#activity-date-time').data('daterangepicker').setStartDate(moment().startOf('hour'));
            
            $('#activityModal').modal('show');
        });
    });

    // $(".add-activity").click(function () { 
    //     $(".activity_model_row1").show();
    //     $(".save_activity_row").hide();

    //     $('#activityModal').find('.modal-title').html('Add Activity')
    //     $('#activity_id').val('');
    //     $('#activity_type').val('');
    //     $('#activity_title').val('');
    //     $('#activity_description').val('');
    //     $('#activity-date-time').val()''
    //     $('#activityModal').modal('show');
    // });

    // $(".edit-activity").click(function () {
    //     $(".activity_model_row1").show();
    //     $(".save_activity_row").hide();

    //     let data = $(this).data('data');
    //     if (data.type == 'add') {
    //         return false;
    //     }
    //     $('#activityModal').find('.modal-title').html('Activity');
    //     $('#activity_id').val(data.id);
    //     $('#activity_type').val(data.type);
    //     $('#activity_title').val(data.title);
    //     $('#activity_description').val(data.description);
    //     $('#activity-date-time').val(data.date_time);
    //     $('#activityModal').modal('show');
    // });
    
    $(function() {
        // Initialize the datetime picker for the input field with id 'activity-date-time'
        $('#activity-date-time').daterangepicker({
            singleDatePicker: true,  // Enable single date selection
            timePicker: true,        // Enable time picker
            locale: {
                format: 'DD-M-Y hh:mm A'  // Display format for the date/time
            }
        });

        // Edit Activity Button Click Handler
        $(".edit-activity").click(function () {
            $(".activity_model_row1").show();
            $(".save_activity_row").hide();
            $('#file-preview-container').html('');
            // $('#attachment_div').hide();
            // Fetch the data associated with the clicked button
            let data = $(this).data('data');
            let attachments = $(this).data('attachments'); 
            
            if (data.type == 'add') {
                return false; // Exit if type is 'add'
            }

            // Set modal title
            $('#activityModal').find('.modal-title').html('Activity');

            // Set the values from the data object into the modal fields
            $('#activity_id').val(data.id);
            $('#activity_type').val(data.type);
            $('#activity_title').val(data.title);
            $('#activity_description').val(data.description);

            // If the date exists in the data, use it; otherwise, use the current date/time
            if (data.date_time) {
                $('#activity-date-time').data('daterangepicker').setStartDate(moment(data.date_time, 'DD-M-Y hh:mm A'));
            } else {
                $('#activity-date-time').data('daterangepicker').setStartDate(moment().startOf('hour'));
            }

            // if(data.type == 'attachment'){
                $('#attachment_div').show();
                $.each(attachments, function (index, file) {
                    // Create HTML for each file
                    uploadedFiles.push(file);
                    const fileHtml = `
                        <div class="card radius-10 border shadow-none mb-3 file-preview" style="width: 467px;">
                        <input type="hidden" name="old_file_id[]" value="${file.id}">
                            <div class="card-body" style="position: relative;">
                                <div class="d-flex align-items-center">
                                    <div class="">
                                        <p class="mb-1">
                                            <i class="fa fa-paperclip" aria-hidden="true"></i> ${file.file_name}
                                        </p>
                                    </div>
                                    <div class="ms-auto">
                                        <i class="fa fa-trash cursor-pointer text-danger remove-file" data-filename="${file.file_name}" style="font-size: 20px;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>`;

                    // Append the HTML to a specific div with id 'fileContainer'
                    $('#file-preview-container').append(fileHtml);
                });
            // }

            // Show the modal
            $('#activityModal').modal('show');
        });
    });


    $(".save_timeline").click(function () {
        $(".activity_model_row1").hide();
        $(".save_activity_row").show();
        let type = $(this).data('type');
        if (type == 'phone') {
            $(".save_activity_row > .col").html('<h3>Phone Call</h3>');
            $('#activity_type').val('phone');
            $('#activity_title').val('Phone Call');
        }
        if (type == 'message') {
            $(".save_activity_row > .col").html('<h3>Message via WhatsApp</h3>');
            $('#activity_type').val('message');
            $('#activity_title').val('Message via WhatsApp');
        }
        if (type == 'email') {
            $(".save_activity_row > .col").html('<h3>Message via Email</h3>');
            $('#activity_type').val('message');
            $('#activity_title').val('Message via Email');
        }

        $('#activityModal').find('.modal-title').html('Save to Timeline?');
        $('#activity_id').val('');
        $('#activity_description').val('');
        $('#activity-date-time').bootstrapMaterialDatePicker('setDate');

        setTimeout(() => {
            $('#activityModal').modal('show');
        }, 2000);

    });

    $(document).on('click','.change-status',function(){
        $('#changeStatusModal').modal('show');
    });

    $('#leadForm,#activityForm,#followUpForm,#UpdateStatusForm,#add_group, #edit_group').submit(function(e) {
        e.preventDefault();
        date1 = $('#date-time').val();
        date2 = '{{ $data->follow_up_date_time }}';
        activity_type = $('#activity_type').val();

         // Function to parse the first custom date format
         function parseCustomDate(dateStr, fallbackDate) {
            var parts = dateStr.split(' ');
            var day = new Date(fallbackDate).getDate(); // Extract day from the second date
            var month = parts[1]; // "Oct"
            var year = parts[2]; // "2024"
            var time = parts[4] + " " + parts[5]; // "4:26 pm"

            // Create a valid date string in format "Month Day, Year Time"
            var formattedDateStr = month + " " + day + ", " + year + " " + time;

            // Return the parsed date
            return new Date(formattedDateStr);
        }

        // Parse both dates
        var parsedDate1 = parseCustomDate(date1, date2); // Parse date1 using date2 as fallback
        var parsedDate2 = new Date(date2); // Parse ISO format date2

        if(activity_type == 'attachment'){
            if (uploadedFiles.length == 0){
                $('#file-count').show().text(`The attachmentss field is required.`);
                return false;
            }
        }
        

        var url = $(this).attr('action');
        var param = new FormData(this);

        uploadedFiles.forEach(file => {
            param.append('attachments[]', file);
        });

        my_ajax(url, param, 'post', function(res) {
        },true);
    });

    function goBack() {
        window.history.back();
    }

    var leadDataCount = '{{ $data->lead_data()->count() > 0 ? $data->lead_data()->count() : 1 }}';
    $(document).on('click','.add_lead_data_tr',function(){
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
        $("#leadData_body").append(_html);
    });

    $(document).on('click','.delete_lead_data_tr',function(){
        let id = $(this).data('id');
        $('#lead_data_tr_'+id).remove();
    });




    // code for follow up
    // Function to format date in the required format
    function formatSelectedDate(inputDate) {
        var dateObj = new Date(inputDate);

        // Get day of the week
        var days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        var dayOfWeek = days[dateObj.getDay()];

        // Get month
        var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        var month = months[dateObj.getMonth()];

        // Get year, hours, minutes
        var year = dateObj.getFullYear();
        var hours = dateObj.getHours();
        var minutes = String(dateObj.getMinutes()).padStart(2, '0');
        var ampm = hours >= 12 ? 'pm' : 'am';
        hours = hours % 12;
        hours = hours ? hours : 12; // Adjust 12-hour format

        // Return formatted date string
        return `${dayOfWeek} ${month} ${year} - ${hours}:${minutes} ${ampm}`;
    }

    // Date picker initialization
    document.addEventListener('DOMContentLoaded', function() {
        var dateInputField = document.getElementById('date-time');

        // Listen for calendar date change
        dateInputField.addEventListener('change', function() {
            var selectedDate = this.value;

            // If a date is selected, format it before displaying
            if (selectedDate) {
                var formattedDate = formatSelectedDate(selectedDate);
                this.value = formattedDate;
            }
        });

        // If an existing follow-up date is present, format and display it
        // var initialDate = "{{ $data->follow_up_date_time }}";
        // if (initialDate) {
        //     dateInputField.value = formatSelectedDate(initialDate);
        // }
    });

</script>

@endsection
