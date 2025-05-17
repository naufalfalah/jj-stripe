@extends('layouts.admin')

@section('page-css')
    <style>
        .list-group {
            max-height: 500px !important;
            overflow-y: auto !important;
        }

        /* Styling for WebKit browsers (Chrome, Safari) */
        .list-group::-webkit-scrollbar {
            width: 5px !important;
        }

        .list-group::-webkit-scrollbar-thumb {
            background-color: #888 !important;
            border-radius: 4px !important;
        }

        .list-group::-webkit-scrollbar-thumb:hover {
            background-color: #555 !important;
        }

        /* Styling for Firefox */
        .list-group {
            scrollbar-width: thin !important;
        }

        .list-group::-moz-scrollbar-thumb {
            background-color: #888 !important;
            border-radius: 4px !important;
        }

        .list-group::-moz-scrollbar-thumb:hover {
            background-color: #555 !important;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <form method="get" action="" class="row g-3 ajaxFormClient">
            <div class="row">
                <div class="form-group col-sm-12 col-lg-6 my-2">
                    <label>All Clients <span class="text-danger">*</span></label>
                    <input type="hidden" value="{{ request()->input('client') }}" id="client_id">
                    <select name="client" id="client" class="form-control single-select" required>
                        <option value="">All Clients</option>
                        @foreach ($clients as $val)
                        <option value="{{ $val->hashid }}" {{ (request()->input('client') == $val->hashid) ? 'selected' : '' }}>
                            {{ $val->client_name }} ( {{ $val->email }} )
                        </option>
                        @endforeach
                    </select>
                </div>

            </div>
        </form>

        <div class="col-12 col-xl-3">
            <div class="card">
                <div class="card-header">
                    <a href="javascript:void(0);" class="btn btn-primary add_files">Upload File</a>
                </div>
                <div class="card-body">
                    <div class="fm-menu">
                        <div class="list-group list-group-flush m-3" >
                            <a href="{{route('admin.file_manager.view')}}" class="list-group-item py-1 all_files"><i class='bx bx-folder me-2 text-primary'></i><span>All Folders & Files</span></a>
                            @foreach ($all_folders as $folder)
                            <a href="javascript:void(0);" class="list-group-item py-1 get_files" data-id="{{$folder->id}}" style="display: flex; align-items: center;">
                                <i class='bi bi-folder-fill me-2 text-primary'></i>
                                <input type="hidden" name="get_folder_files" value="{{@$folder->id}}">
                                <span>{{ $folder->folder_name }}</span>
                            </a>
                            @endforeach
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="col-12 col-xl-9">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <h5 class="mb-0 folder_name"></h5>
                        </div>
                    </div>
                    <div class=  "alert border-0 bg-light-success alert-dismissible fade show" role="alert" style="display: none;">
                        URL copied to clipboard!
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
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
                            <input type="hidden" value="{{request()->input('client')}}" id="request_id_input">
                            <tbody id="client_files">

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


     <!-- Add File Modal Start-->
     <div class="modal fade" id="addFilesModal" tabindex="-1" aria-labelledby="addFilesModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addFilesModalLabel">Add New File</h5>
                </div>
                <form action="{{ route('admin.file_manager.save_file') }}" method="post" class="ajaxFolderFile" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <label class="form-label">Select Send To <span
                                        class="text-danger fw-bold">*</span></label>
                                <select name="send_to" id="fileSendTo" class="form-select" required>
                                    <option value="all_users">All Clients</option>
                                    <option value="single_multiple_users">Single or Mutiple Client</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mt-2" style="display: none;" id="users_dropdown">
                            <div class="col-12">
                                <label class="form-label">Select Clients <span
                                        class="text-danger fw-bold">*</span></label>
                                <select name="clients[]" class="multiple-select" id="multiple-select" data-placeholder="Choose anything" multiple="multiple">
                                    @foreach ($clients as $user)
                                        <option value="{{ $user->id }}">{{ $user->client_name }} ({{  ( $user->email ) }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-12">
                                <label class="form-label">Add File <span class="text-danger fw-bold">*</span></label>
                                <input type="file" name="file" id="file_name" class="form-control" onchange="validateFileSize(this)" required>
                            </div>

                            <div class="col-12 mt-3">
                                <div class="progress" style=" height: 30px; ">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary form-submit-btn">Save</button>
                        <button type="button" class="btn btn-secondary" id="hideUploadMdal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Add File Modal End-->
@endsection

@section('page-scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.8/clipboard.min.js"></script>
    <script>

        load_client_files_list();

        $(document).on('click', '.get_files', function (){
            $('#request_id_input').val(null)
            $('#folder_id').val(null);
            let folder_id = $(this).data('id');
            var data = {
            '_token' : "{{ csrf_token() }}",
            folder_id: folder_id
            };
            $.ajax({
                url: "{{ route('admin.file_manager.get_client_files') }}",
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
                error: function(xhr, status, error) {
                }
            });
        });

        $('.single-select').select2({
            theme: 'bootstrap4',
            width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
            placeholder: $(this).data('placeholder'),
            allowClear: Boolean($(this).data('allow-clear')),
        });

        function load_client_files_list(){
            var request_id_input = $('#request_id_input').val();
            var data = {
            '_token' : "{{ csrf_token() }}",
            request_id_input: request_id_input
            };
            $.ajax({
                url: "{{ route('admin.file_manager.get_client_files') }}",
                type: 'POST',
                data: data,
                success: function(response) {
                    $('#folder_id').val(null);
                    $('#main_folder_id').val(null);
                    if(response.folder_name.folder_name){
                        $('.folder_name').text(response.folder_name.folder_name);
                        $('#folder_id').val(response.folder_name.id);
                    }else{
                        $('.folder_name').text(response.folder_name);
                        $('#main_folder_id').val(response.folder_id);
                    }
                    $('#client_files').html(response.body);
                },
                error: function(xhr, status, error) {
                }
            });
        }

        $(document).ready(function() {
            var clipboard = new ClipboardJS('.copy-button');

            clipboard.on('success', function(e) {
                e.clearSelection();

                var alertElement = $('.alert');
                alertElement.show();
                
                setTimeout(function() {
                    alertElement.hide();
                }, 3000);

                var $button = $(e.trigger);
                $button.attr('title', 'Copied!').tooltip('show');

                setTimeout(function() {
                    $button.attr('title', 'Copy File Url').tooltip('hide');
                }, 2000);
            });

            clipboard.on('error', function(e) {
                console.error('Copy failed: ', e);
            });
        });

        $(document).on('change', '#client', function (){
            $('.ajaxFormClient').submit();
        })

        $(document).on('click', '.add_files', function(){

            $('.progress .progress-bar').css("width", "0%");
            $('.progress .progress-bar').attr("aria-valuenow", 0);
            $('.progress .progress-bar').html('0%');
            $(".progress").hide();
            $('#addFilesModal').modal({ backdrop: 'static', keyboard: false});
            $('#addFilesModal').modal('show');
        });

        $("#hideUploadMdal").click(function(){
            $('body .ajaxFolderFile')[0].reset();

            $('.progress .progress-bar').css("width", "0%");
            $('.progress .progress-bar').attr("aria-valuenow", 0);
            $('.progress .progress-bar').html('0%');
            $(".progress").hide();
            if (fileUploadRequest) {
                fileUploadRequest.abort();
            }
            btnloader('hide');
            $('#addFilesModal').modal('hide');
        });

        let fileUploadRequest = null;
        $('.ajaxFolderFile').submit(function(e) {
            e.preventDefault();
            validations = $(".ajaxFolderFile").validate();
            if (validations.errorList.length != 0) {
                return false;
            }
            var url = $(this).attr('action');
            var param = new FormData(this);
            // var files = $('#file_name')[0].files[0] ?? '';
            // param.append('file_name', files);
            btnloader('show');
            fileUploadRequest = $.ajax({
                url: url,
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: param,
                contentType: false,
                processData: false,
                xhr: function () {
                    var xhr = new window.XMLHttpRequest();
                    xhr.upload.addEventListener("progress", function (evt) {
                        if (evt.lengthComputable) {
                            var percentage = (evt.loaded / evt.total) * 100;
                            $('.progress .progress-bar').css("width", percentage + '%');
                            $('.progress .progress-bar').attr("aria-valuenow", percentage);
                            $('.progress .progress-bar').html(percentage.toFixed(0) + '%');
                        }
                    }, false);
                    return xhr;
                },
                beforeSend: function () {
                    if(fileUploadRequest !== null){
                        fileUploadRequest.abort();
                    }
                    $('.progress .progress-bar').css("width", "0%");
                    $('.progress .progress-bar').attr("aria-valuenow", 0);
                    $('.progress .progress-bar').html('0%');
                    $(".progress").show();
                },
                complete: function () {
                    $('.progress .progress-bar').css("width", "0%");
                    $('.progress .progress-bar').attr("aria-valuenow", 0);
                    $('.progress .progress-bar').html('0%');
                    page_loader('hide');
                    btnloader('hide');
                    $(".progress").hide();
                },
                success: function(data) {
                    $('.progress .progress-bar').css("width", "0%");
                    $('.progress .progress-bar').attr("aria-valuenow", 0);
                    $('.progress .progress-bar').html('0%');
                    $(".progress").hide();
                    var timer = 1200;
                    if (data['reload'] !== undefined) {
                        toast(data['success'], "Success!", 'success', timer);
                        setTimeout(function () {
                            window.location.reload(true);
                        }, 600);
                        return false;
                    }

                    if (data['redirect'] !== undefined) {
                        toast(data['success'], "Success!", 'success', timer);
                        setTimeout(function () {
                            window.location = data['redirect'];
                        }, 600);
                        return false;
                    }

                    if (data['error'] !== undefined) {
                        toast(data['error'], "Error!", 'error');
                        return false;
                    }

                    if (data['errors'] !== undefined) {
                        multiple_errors_ajax_handling(data['errors']);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    page_loader('hide');
                    btnloader('hide');
                    ajaxErrorHandling(jqXHR, errorThrown);
                },
            });
        });


        $(document).on('change', '#fileSendTo', function() {
            var sendTo = $(this).val();
            if(sendTo === 'single_multiple_users'){
                $('#users_dropdown').show();
            }else{
                $('#users_dropdown').hide();
            }
        });

        $('#multiple-select').select2({
            dropdownParent: $('#addFilesModal'),
            theme: 'bootstrap4',
            width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
            placeholder: $(this).data('placeholder'),
            allowClear: Boolean($(this).data('allow-clear')),
        });

        function validateFileSize(input) {
            const maxFileSizeMB = 25;
            const maxFileSizeBytes = maxFileSizeMB * 1024 * 1024; // Convert MB to Bytes

            if (input.files.length > 0) {
                const fileSize = input.files[0].size;

                if (fileSize > maxFileSizeBytes) {
                    toast('File size exceeds the maximum limit of 25MB. Please choose a smaller file.', "warning!", 'warning');
                    input.value = ''; // Clear the input to prevent submitting the invalid file
                }
            }
        }
    </script>
@endsection
