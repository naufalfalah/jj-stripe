@extends('layouts.front')
@section('page-css')
<link href="{{ asset('front') }}/assets/plugins/datetimepicker/css/classic.css" rel="stylesheet" />
<link href="{{ asset('front') }}/assets/plugins/datetimepicker/css/classic.time.css" rel="stylesheet" />
<link href="{{ asset('front') }}/assets/plugins/datetimepicker/css/classic.date.css" rel="stylesheet" />
<link rel="stylesheet"
    href="{{ asset('front') }}/assets/plugins/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.min.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
<style>
    .msg_preview {
            overflow-y: auto; /* Ensure vertical scrolling */
            height: 195px; /* Set height for the scrollable area */

        }

        .msg_preview::-webkit-scrollbar {
            width: 8px; /* Width of the scrollbar */
            background-color: #f1f1f1; /* Background color of the scrollbar track */
        }

        .msg_preview::-webkit-scrollbar-thumb {
            background-color: #888; /* Color of the scrollbar thumb */
            border-radius: 4px; /* Rounded corners for the scrollbar thumb */
        }

        .msg_preview::-webkit-scrollbar-thumb:hover {
            background-color: #555; /* Color of the scrollbar thumb on hover */
        }

</style>
@endsection
@section('content')
<input type="hidden" value="{{ @$send_email}}" id="openmodal">
<div class="page-breadcrumb d-flex align-items-center mb-3 mt-5">
    <div class="breadcrumb-title" title="{{@$breadcrumb_main}}">{{Str::limit(@$breadcrumb_main, 25)}}</div>
    <a href="Javascript:void(0);" onclick="goBack()" class="btn btn-dark"><i class="fa-solid fa-angle-left"></i> Back</a>
    <div class="ms-auto">
        <a class="btn btn-secondary mr-3 send-template" href="javascript:void(0);" data-data="{{ $data }}">Send To Client</a>
        <div class="btn-group">
            <button type="button" class="btn btn-primary split-bg-primary dropdown-toggle dropdown-toggle-split"
                data-bs-toggle="dropdown"> <span class="">Options </span>
            </button>
            <div class="dropdown-menu dropdown-menu-right dropdown-menu-lg-end">
                <a class="dropdown-item private_note" href="javascript:void(0);" data-data="{{ $data }}">Edit private note</a>
                <a class="dropdown-item edit-temp" href="javascript:void(0);" data-data="{{ $data }}">Edit email template</a>
                <a class="dropdown-item copy-template" href="javascript:void(0);" data-data="{{ $data }}">Create copy of template</a>
                @if ($data->message_activity()->count() == 0)
                <a class="dropdown-item text-danger" href="javascript:void(0);" onclick="ajaxRequest(this)" data-url="{{route('user.email-template.delete',$data->hashid)}}">Delete email template</a>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12 col-md-6">
        <h5><strong> Preview </strong></h5>
        <div class="card">
            <div class="card-body msg_preview">
                <div class="row g-0">
                    <div class="col-12">
                        <div class="col-md-12 edit-temp p-2" style="cursor: pointer;" data-data="{{ $data }}">
                            <p>{{ $data->description }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-6">
        <h5><strong> Sharing History </strong></h5>
        <div class="card">
            <div class="card-body">
                <div class="row g-0">
                    <div class="col-12">
                        @if ($data->message_activity()->count() > 0)
                        <div class="d-flex bg-white p-2 cursor-pointer select-none justify-content-between" id="all_shares_temp">
                            <div class="text-sm fw-bold">Total Sent</div>
                            <div class="font-medium fw-bold">{{ $data->message_activity()->count() }}</div>
                        </div>
                        @else
                            <div class="col-md-12 text-center p-5">
                                <i class="fa-solid fa-gift" style="font-size: 25px;"></i>
                                <p>You haven't sent this message
                                    Use the 'Quick Response' feature in the Client Portal mobile app to send auto-personalised versions of this message to your leads and clients.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12 col-md-6">
        <h5><strong> Private Notes </strong></h5>
        <div class="card">
            <div class="card-body msg_preview">
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
                @if ($data->message_activity()->count() > 0)
                    @foreach ($data->message_activity as $k => $item)
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
                                                    <small class="">{{ $item->created_at->format('M d, Y h:i a') }}</small>
                                                    <div class="fs-5 ms-auto dropdown">
                                                        <div class="dropdown-toggle dropdown-toggle-nocaret cursor-pointer" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></div>
                                                        <ul class="dropdown-menu">
                                                            <li><a class="dropdown-item text-success edit-activity float-end"  href="{{ route('user.leads-management.client_details', hashids_encode($item->client_id)) }}">Go To Client Detail</a></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                                <div class="d-flex w-100 justify-content-between">

                                                </div>
                                                <small class=" fw-bold">Sent To: </small><span>{{ $item->lead_client->name }}</span>
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
                                            <small class="text-muted">Email Template Created</small>
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


<div class="modal fade" id="templateNoteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Private Notes</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('user.email-template.save') }}" method="post" id="templateNoteForm">
                @csrf
                <div class="modal-body">
                    <p>These notes are private to you and will not be accessible to clients you share this message with.</p>
                    <input type="hidden" name="id" id="temp_note_id">
                    <input type="hidden" name="type" value="note">
                    <textarea name="note" class="form-control" id="temp_note" cols="30"
                        rows="10" required placeholder="Add private note here..."></textarea>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary form-submit-btn">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="messageTemplate" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Email Template</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('user.email-template.save') }}" method="post" id="messageTemplateForm">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="temp_id" name="id">
                    <input type="hidden" name="reopen" id="reopen">
                    <div class="form-group mb-3">
                        <label for="">Title<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="title"
                            placeholder="Enter Template Title" id="title" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="">Template Message<span class="text-danger">*</span></label>
                        <textarea name="template_message" id="templateMessage" cols="30" rows="10" class="form-control" placeholder="Hi @clientName" required>Hi @clientName</textarea>
                    </div>
                    <span class="mt-3 text-dark">
                        @clientName will be replaced with your client's display name when sending <a href="javascritp:void(0);" class="add-clientName">( insert @clientName )</a>
                    </span>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
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
                <h5 class="modal-title">Send Email</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('user.email-template.send') }}" method="post" id="messageSendForm">
                @csrf
                <div class="modal-body">
                    <input type="hidden" value="{{ $data->id }}" name="temp_id">
                    <input type="hidden" name="lead_id" id="lead_id">
                    <textarea name="" hidden id="client_desc" cols="30" rows="10"></textarea>
                    <input type="hidden" name="client_num" id="client_num">
                    <input type="hidden" name="client_email" id="client_email">
                    <div class="form-group mb-3">
                        <label>All Client <span class="text-danger">*</span></label>
                        <select name="client[]" id="client" class="form-control single-select" multiple  required>
                            <option value="" disabled>Select...</option>
                            @foreach ($clients as $val)
                            <option value="{{ $val->hashid }}" data-lead_id="{{ $val->id }}" data-num="{{ $val->mobile_number }}" data-email="{{ $val->email }}" data-name="{{ $val->name }}">{{ $val->name }} ( {{ $val->email }} )</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="">Title<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="title"
                            placeholder="Enter Template Title" id="send_title" readonly required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="">Template Message<span class="text-danger">*</span></label>
                        <textarea name="template_message" id="send_templateMessage" cols="30" rows="10" class="form-control" placeholder="Hi @clientName" readonly required>Hi @clientName</textarea>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <span class="float-end text-dark">
                                <a href="javascritp:void(0);" data-data="{{ $data }}" data-reopenmodal="reopenmodal" class="edit-temp">Edit Message Template</a>
                            </span>
                        </div>
                    </div>


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    {{-- <a href="javascript:void(0);" id="email_send_btn" type="button" class="btn btn-primary form-submit-btn">Send</a> --}}
                    <a id="next_modalopen" type="button" class="btn btn-primary">Next</a>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade" id="sendTemplateModalNext" tabindex="-1" aria-labelledby="sendTemplateModalNext" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Send Message</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <!-- <form action="{{ route('user.message-template.send') }}" method="post" id="messageSendForm"> -->
                @csrf
                <div class="modal-body">
                    <input type="hidden" value="{{ $data->id }}" name="temp_id" id="temp_id_next">
                    <input type="hidden" name="lead_id" id="lead_id">
                    <textarea name="" hidden id="client_desc" cols="30" rows="10"></textarea>
                    <input type="hidden" name="client_num" id="client_num">
                    <input type="hidden" name="client_email" id="client_email">
                    <input type="hidden" class="form-control" name="title" placeholder="Enter Template Title" id="send_title_next" readonly required>

                    <div class="form-group mb-3">
                        <label for="">Template Message<span class="text-danger">*</span></label>
                        <textarea name="template_message" id="send_templateMessage_next" cols="30" rows="6" class="form-control" placeholder="Hi @clientName" readonly required>Hi @clientName</textarea>
                    </div>

                    <div class="row" id="client_div" style="">

                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="back_modal">Back</button>
                    <a href="#" class="btn btn-primary email_send_btn" id="page_reload">Done</a>
                 </div>
            <!-- </form> -->
        </div>
    </div>
</div>

<div class="modal fade" id="allSharesTemp" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Total Sent</h5>
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
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($data->message_activity as $k => $share)
                                    <tr>
                                        <td> {{ $k + 1}} </td>
                                        <td> {{ $share->lead_client->name }} </td>
                                        <td> {{ $share->created_at->format('M-d-Y - h:s' ) }} </td>
                                        <td>
                                            <a href="{{ route('user.leads-management.client_details', hashids_encode($share->client_id)) }}" class="text-success" data-bs-toggle="tooltip" data-bs-placement="bottom" title="View Lead" data-bs-original-title="View Lead" aria-label="View" ><i class="bi bi-eye-fill"></i></a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center">No Sharing History Found</td>
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
    var currentDatetime = moment();

    $(document).ready(function (){
        var openSendModal = $('#openmodal').val().trim();
        if (openSendModal !== null && openSendModal === 'send_email') {
            $('.send-template').click();
        }
    });

    $("#all_shares_temp").click(function () {
        $('#allSharesTemp').modal('show');
    });

    $(function(){
        $('.single-select').select2({
            dropdownParent: $('#sendTemplateModal')
        });
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



    let selectedClients = [];
    $(document).on('change', '.single-select', function () {
        $('#error').hide();
        selectedClients = [];
        // Loop through each selected option
        $(this).find(':selected').each(function() {
            let clientData = {
                value: $(this).val(),
                name: $(this).attr('data-name'),
                email: $(this).attr('data-email'),
                num: $(this).attr('data-num'),
                lead_id: $(this).attr('data-lead_id')
            };

            const isDuplicate = selectedClients.some(client => client.lead_id === clientData.lead_id);

            if (!isDuplicate) {
                selectedClients.push(clientData);
            }
        });

    });


    $('#next_modalopen').click(function() {
        if (selectedClients.length == 0) {
            $('#error').show();
            return false;
        }  
        selectedClients.forEach(function(clientData) {
            if ($(`#file-preview-container-${clientData.lead_id}`).length === 0) {
                let div = `<div id="file-preview-container-${clientData.lead_id}">
                            <div class="card radius-10 border shadow-none mb-3 file-preview" style="width: 467px;">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="">
                                            <p class="mb-1">${clientData.name} (${clientData.email})</p>
                                        </div>
                                        <div class="ms-auto">
                                            <button type="button" class="btn btn-success send_msg" id="snd_btn${clientData.lead_id}" data-id="${clientData.lead_id}" data-num="${clientData.num}" data-email="${clientData.email}" data-name="${clientData.name}">SEND</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>`;
                $('#client_div').append(div); // Append to the client div
            }

        });
        // send_title|send_templateMessage
        $('#send_title_next').val($('#send_title').val());
        $('#send_templateMessage_next').val($('#send_templateMessage').val());
        $('#sendTemplateModal').modal('hide');
        $('#sendTemplateModalNext').modal('show');
    });

    $('body').on('click', '.send_msg', function() {
        id = $(this).data('id');
        num = $(this).data('num');
        clientEmail = $(this).data('email');
        clientName = $(this).data('name');
        temp_id = $('#temp_id_next').val();
        send_title_next = $('#send_title_next').val();
        tempMessage = $('#send_templateMessage_next').val();
        $('#snd_btn'+id).html('<span class="d-flex align-items-center"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div> Sending...</span>');

        if (tempMessage.includes('@clientName')) {
            tempMessage = tempMessage.replace(/@clientName/g, clientName);
        } else {
            tempMessage = "Hi " + clientName + ",\n\n" + tempMessage;
        }
        $.ajax({
            url: '{{ route("user.email-template.send") }}', // URL from your Laravel route
            type: 'POST', // Method
            data: {
                lead_id: id, // Data to send
                title: send_title_next,
                template_message: tempMessage,
                temp_id: temp_id,
                _token: '{{ csrf_token() }}' // CSRF token for security
            },
            success: function(response) {
                let mailtoLink = "mailto:" + clientEmail + "?subject=Your Subject&body=" + encodeURIComponent(tempMessage);
                window.open(mailtoLink, '_blank');
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

    // $('#email_send_btn').click(function () {
    //     $('#messageSendForm').submit();
    // });

    $(".private_note").click(function () {
        let data = $(this).data('data');
        $("#temp_note_id").val(data.id);
        $("#temp_note").val(data.private_note);
        $('#templateNoteModal').modal('show');
    });

    $(".edit-temp").click(function () {
        let data = $(this).data('data');
        let reopen = $(this).data('reopenmodal')
        $('#reopen').val(reopen);
        $("#temp_id").val(data.id);
        $("#title").val(data.title);
        $("#templateMessage").val(data.description);
        $('#sendTemplateModal').modal('hide');
        $('#messageTemplate').modal('show');
    });

    $(".copy-template").click(function () {
        let copy_data = $(this).data('data');
        var url = "{{ route('user.email-template.copy_temp') }}";
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        $.ajax({
            url: url,
            type: 'POST',
            data: {_token: CSRF_TOKEN, copy_data: copy_data},
            dataType: 'JSON',
            success: function (response) {
                toast(response.success, "Success!", 'success');
                setTimeout(function () {
                    window.location = response.redirect;
                }, 600);
                return false;

            }
        });
    });

    $('#messageTemplateForm,#templateNoteForm,#messageSendForm').submit(function(e) {
        e.preventDefault();
        var url = $(this).attr('action');
        var param = new FormData(this);
        my_ajax(url, param, 'post', function(res) {
        },true);
    });

    $(document).on('click', '.add-clientName', function(e){
        e.preventDefault();
        var textarea = document.getElementById("templateMessage");
        textarea.value += " @clientName";
    });

    function goBack() {
        // Navigate to the previous page in the browser history
        window.history.back();
    }
</script>
@endsection
