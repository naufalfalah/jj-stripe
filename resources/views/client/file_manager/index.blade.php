@extends('layouts.front')
@section('page-css')
<link href="{{ asset('front') }}/assets/plugins/datatable/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
@endsection
@section('content')
<style>
    .modal-footer-centered {
        display: flex;
        justify-content: center;
    }

    /* style="width:450px; margin-left:20px; margin-right:10px; */

.adjust{
    width:450px;
    margin-left:20px;
    margin-right:10px;
}

.labler{
    margin-left:20px;
}
</style>
    {{-- add your code --}}

    <div class="row">
        <div class="col">
        </div>

        <div class="col-lg-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs nav-dark" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link main-tabs active" data-bs-toggle="tab" href="#files" role="tab"
                                aria-selected="true" data-type="files">
                                <div class="d-flex align-items-center">
                                    <div class="tab-title">Files</div>
                                </div>
                            </a>
                        </li>
                        {{-- <li class="nav-item" role="presentation">
                            <a class="nav-link main-tabs" data-bs-toggle="tab" href="#pages" role="tab"
                                aria-selected="false" data-type="pages">
                                <div class="d-flex align-items-center">
                                    <div class="tab-title">Pages</div>
                                </div>
                            </a>
                        </li> --}}
                    </ul>
                    <div class="tab-content py-3">
                        <div class="tab-pane fade active show" id="files" role="tabpanel">
                            <div class="row">
                                <div class="col-12 col-xl-3">
                                    <div class="card">
                                        <div class="card-body border-bottom">
                                            <div class="d-grid"> <a href="javascript:void(0);" class="btn btn-primary"
                                                    id="add_folder"><i class="bi bi-plus-lg"></i> Add Folder</a>
                                            </div>
                                        </div>
                                        <div class="fm-menu">
                                            <div class="list-group list-group-flush m-3">
                                                <a href="{{ route('user.file_manager.view') }}"
                                                    class="list-group-item py-1 all_files"><i
                                                        class='bx bx-folder me-2 text-primary'></i><span>All
                                                        Files</span></a>
                                                @foreach ($all_folders as $folder)
                                                    <a href="javascript:void(0);" class="list-group-item py-1 get_files"
                                                        data-id="{{ $folder->id }}"
                                                        style="display: flex; align-items: center;">
                                                        <i class='bi bi-folder-fill me-2 text-primary'></i>
                                                        <input type="hidden" name="get_folder_files"
                                                            value="{{ $folder->id }}">
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
                                                </div>
                                                <div class="ms-auto"><a href="javascript:void(0);"
                                                        class="btn btn-sm btn-success add_files"><i
                                                            class="bi bi-upload"></i> Upload New File</a>
                                                </div>
                                            </div>
                                            <div class=  "alert border-0 bg-light-success alert-dismissible fade show"
                                                role="alert" style="display: none;">
                                                URL copied to clipboard!
                                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                    aria-label="Close"></button>
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
                                                    <input type="hidden" value="{{ request()->input('id') }}"
                                                        id="request_id_input">
                                                    <tbody id="client_files">

                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="pages" role="tabpanel">

                            <div class="row">
                                <div class="col-12 col-lg-12 mx-auto pb-2" id="btn-align">
                                    <a href="javascript:void(0);" class="btn btn-primary float-end page-opt">+ Page</a>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col">
                                    <input type="hidden" id="pages_count" value="{{ $pages_count }}">
                                    @if ($pages_count > 0)
                                        <div class="table-responsive" id="Page-template-responsive">
                                            <table class="table table-hover mb-0" id="Page-template-table">
                                                <thead>
                                                    <tr>
                                                        <th scope="col">Title</th>
                                                        <th scope="col">Description</th>
                                                        <th scope="col">Sent</th>
                                                        <th scope="col">Last Sent</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center p-5" id="Page-template-responsive">
                                            <i class="fa-solid fa-user-slash"
                                                style="font-size: 25px;color: #14a2b8;"></i>
                                            <h5>No Pages Found</h5>
                                        </div>
                                    @endif
                                </div>
                            </div>

                        </div>

                    </div>
                </div>
            </div>
        </div>

        {{-- add your code --}}

        {{-- event Modal --}}
        <div class="modal fade" id="choose" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">

                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-footer modal-footer-centered">
                        {{-- <a href="javascript:void(0);" class="btn btn-primary open-add-page">+ Product
                            Or Event Page</a> --}}
                        <a href="{{ route('user.page.add_page', 'event_page') }}" class="btn btn-primary">+ Product Or Event Page</a>
                        <a href="{{ route('user.page.add_page', 'image_gallery') }}" class="btn btn-primary">+ Image Gallery</a>
                    </div>
                </div>
            </div>
        </div>
        {{-- event Modal --}}

        <!-- Add Folder Modal Start-->
        <div class="modal fade" id="addFolderModal" tabindex="-1" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Add New Folder</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('user.file_manager.save_folder') }}" method="post" class="ajaxFolderForm">
                        @csrf
                        <div class="modal-body">
                            <div class="col-12">
                                <label class="form-label">Folder Name <span class="text-danger fw-bold">*</span></label>
                                <input type="text" name="folder_name" id="folder_name"
                                    placeholder="Enter Folder Name" class="form-control" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary form-submit-btn">Save</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Add Folder Modal End-->

        <!-- Add File Modal Start-->
        <div class="modal fade" id="addFilesModal" tabindex="-1" aria-labelledby="addFilesModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addFilesModalLabel">Add New File</h5>

                    </div>
                    <form action="{{ route('user.file_manager.save_file') }}" method="post" class="ajaxFolderFile"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">

                            <div class="col-12">
                                <label class="form-label">Add File Name <small>(optional)</small></label>
                                <input type="text" name="new_file_name" id="new_file_name" class="form-control" placeholder="Enter File Name">
                            </div>
                            <br>
                            <div class="col-12">
                                <label class="form-label">Add File <span class="text-danger fw-bold">*</span></label>
                                <input type="file" name="file_name" id="file_name" class="form-control"
                                    onchange="validateFileSize(this)" required>
                            </div>
                            <div class="col-12 mt-3">
                                <div class="progress" style=" height: 30px; ">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-success"
                                        role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"
                                        style="width: 0%"></div>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="folder_id" value="" id="folder_id">
                        <input type="hidden" name="main_folder_id" value="" id="main_folder_id">
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
    <script src="{{ asset('front/assets/plugins/fileUpload/fileUpload.js') }}"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.8/clipboard.min.js"></script>
        <script src="{{ asset('front') }}/assets/plugins/datatable/js/jquery.dataTables.min.js"></script>
        <script src="{{ asset('front') }}/assets/plugins/datatable/js/dataTables.bootstrap5.min.js"></script>
        <script src="{{ asset('front') }}/assets/js/table-datatable.js"></script>
        <script>
            load_client_files_list();
            $(document).on('click', '#add_folder', function() {
                $('#folder_name').val(null);
                $('#addFolderModal').modal('show');
            });

            function validateFileSize(input) {
                const maxFileSizeMB = 25;
                const maxFileSizeBytes = maxFileSizeMB * 1024 * 1024; // Convert MB to Bytes

                if (input.files.length > 0) {
                    const fileSize = input.files[0].size;

                    if (fileSize > maxFileSizeBytes) {
                        toast('File size exceeds the maximum limit of 25MB. Please choose a smaller file.', "warning!",
                            'warning');
                        input.value = ''; // Clear the input to prevent submitting the invalid file
                    }
                }
            }

            $('.ajaxFolderForm').submit(function(e) {
                e.preventDefault();
                validations = $(".ajaxFolderForm").validate();
                if (validations.errorList.length != 0) {
                    return false;
                }
                var url = $(this).attr('action');
                var param = new FormData(this);
                my_ajax(url, param, 'post', function(res) {}, true);
                load_client_files_list();
            });

            $(document).on('click', '.add_files', function() {
                $('#file_name').val(null);
                var preID = $('#request_id_input').val();
                if (preID) {
                    $('#folder_id').val(preID);
                    $('#main_folder_id').val(null);
                }
                $('.progress .progress-bar').css("width", "0%");
                $('.progress .progress-bar').attr("aria-valuenow", 0);
                $('.progress .progress-bar').html('0%');
                $(".progress").hide();
                $('#addFilesModal').modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $('#addFilesModal').modal('show');
            });

            $("#hideUploadMdal").click(function() {
                $('body .ajaxFolderFile')[0].reset();
                $('#file_name').val(null);
                var preID = $('#request_id_input').val();
                if (preID) {
                    $('#folder_id').val(preID);
                    $('#main_folder_id').val(null);
                }
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
                var files = $('#file_name')[0].files[0] ?? '';
                param.append('file_name', files);
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
                    xhr: function() {
                        var xhr = new window.XMLHttpRequest();
                        xhr.upload.addEventListener("progress", function(evt) {
                            if (evt.lengthComputable) {
                                var percentage = (evt.loaded / evt.total) * 100;
                                $('.progress .progress-bar').css("width", percentage + '%');
                                $('.progress .progress-bar').attr("aria-valuenow", percentage);
                                $('.progress .progress-bar').html(percentage.toFixed(0) + '%');
                            }
                        }, false);
                        return xhr;
                    },
                    beforeSend: function() {
                        if (fileUploadRequest !== null) {
                            fileUploadRequest.abort();
                        }
                        $('.progress .progress-bar').css("width", "0%");
                        $('.progress .progress-bar').attr("aria-valuenow", 0);
                        $('.progress .progress-bar').html('0%');
                        $(".progress").show();
                    },
                    complete: function() {
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
                            setTimeout(function() {
                                window.location.reload(true);
                            }, 600);
                            return false;
                        }

                        if (data['redirect'] !== undefined) {
                            toast(data['success'], "Success!", 'success', timer);
                            setTimeout(function() {
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
                    error: function(jqXHR, textStatus, errorThrown) {
                        page_loader('hide');
                        btnloader('hide');
                        ajaxErrorHandling(jqXHR, errorThrown);
                    },
                });
            });

            $(document).on('click', '.get_files', function() {
                $('#request_id_input').val(null)
                $('#folder_id').val(null);
                let folder_id = $(this).data('id');
                var data = {
                    '_token': "{{ csrf_token() }}",
                    folder_id: folder_id
                };
                $.ajax({
                    url: "{{ route('user.file_manager.get_client_files') }}",
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

            function load_client_files_list() {
                var request_id_input = $('#request_id_input').val();
                var data = {
                    '_token': "{{ csrf_token() }}",
                    request_id_input: request_id_input
                };
                $.ajax({
                    url: "{{ route('user.file_manager.get_client_files') }}",
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


            // for pages
            $(document).ready(function() {
                var count = $('#pages_count').val();
                if (count > 0) {
                    getPageTemplate();
                }
            });


            $(document).on('click', '.new-message', function() {
                $('#messageTemplateForm')[0].reset();
                $('#messageTemplate').modal('show');
            });


            $(document).on('click', '.page-opt', function() {
                $('#choose').modal('show');
            });




            $(document).ready(function() {
                $('.ajaxForm').submit(function(e) {
                    e.preventDefault();
                    var url = $(this).attr('action');
                    var formData = new FormData(this);
                    my_ajax(url, formData, 'post', function(res) {}, true);
                });
            });


            function getPageTemplate() {
                if ($.fn.DataTable.isDataTable('#Page-template-table')) {
                    $('#Page-template-table').DataTable().destroy();
                }

                $('#Page-template-table').DataTable({
                    processing: true,
                    serverSide: true,
                    "order": [
                        [0, "desc"]
                    ],
                    "pageLength": 10,
                    "lengthMenu": [10, 50, 100, 150, 500],
                    ajax: {
                        url: "{{ route('user.file_manager.view') }}",
                        data: function(d) {
                            d.search = $('input[type="search"]').val();
                        },
                    },
                    columns: [{
                            data: 'title',
                            name: 'title',
                            orderable: false,
                            searchable: true
                        },
                        {
                            data: 'description',
                            name: 'description',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'sent',
                            name: 'sent',
                            orderable: false,
                            searchable: true
                        },
                        {
                            data: 'last_sent',
                            name: 'last_sent',
                            orderable: true,
                            searchable: false
                        },
                    ],
                    drawCallback: function(settings) {

                        if (settings.json.recordsTotal === 0) {

                            $('#Page_temp_empty').show();
                            $('#Page-template-responsive').hide();
                        } else {

                            $('#Page_temp_empty').hide();
                            $('#Page-template-responsive').show();
                        }
                    },
                });
            }

            $(document).on('click', '.main-tabs', function() {
                let type = $(this).data('type');
                if (type == 'pages') {
                    getPageTemplate()
                }
            });

            $(document).on('click', '.upload_file', function() {
                $("#fileUpload-1").val('');
                $("#csvfileupload").html('');
                $("#fileUpload").fileUpload();
                $("#uploadFileModal").modal('show');
            });



        </script>
    @endsection
