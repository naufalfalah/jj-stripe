@extends('layouts.admin')
@section('page-css')
<link href="{{ asset('front/assets/plugins/fileUpload/fileUpload.css') }}" rel="stylesheet" />
<link href="{{ asset('front') }}/assets/plugins/datatable/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
@endsection
@section('content')
<div class="row">
    {{-- <div class="col-lg-12 mx-auto pb-2" id="btn-align">
        <a href="javascript:void(0);" class="btn btn-primary float-end new-message">+ Add New Template</a>
    </div> --}}

    {{-- filter --}}
    <form method="get" action="" class="row g-3 ajaxFormClient">
        <div class="row">
            <div class="form-group col-sm-12 col-lg-6 my-2">
                <label>All Clients <span class="text-danger">*</span></label>
                <input type="hidden" value="{{ request()->input('client') }}" id="client_id">
                <select name="client" id="client_dropdown" class="form-control single-select" required>
                    <option value="all">All Clients</option>
                    @foreach ($clients as $val)
                    <option value="{{ $val->hashid }}" {{ (request()->input('client') == $val->hashid) ? 'selected' : ''
                        }}>
                        {{ $val->client_name }} ( {{ $val->email }} )
                    </option>
                    @endforeach
                </select>
            </div>

        </div>
    </form>
    {{-- filter --}}



    <div class="col-lg-12 mx-auto">
        <div class="card">
            <div class="card-body">
                <ul class="nav nav-tabs nav-dark" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link main-tabs active" data-bs-toggle="tab" href="#message_template" role="tab"
                            aria-selected="true" data-type="message_template">
                            <div class="d-flex align-items-center">
                                <div class="tab-title">Email Template</div>
                            </div>
                        </a>
                    </li>
                </ul>
                <div class="tab-content py-3">
                    <div class="tab-pane fade active show" id="message_template" role="tabpanel">
                        <input type="hidden" id="template_count" value="{{ $template_count }}">
                        @if ($template_count > 0)
                        <div class="table-responsive" id="message-template-responsive">
                            <table class="table table-hover mb-0" id="message-template-table">
                                <thead>
                                    <tr>
                                        <th scope="col">Client Name</th>
                                        <th scope="col">Title</th>
                                        <th scope="col">Email Preview</th>
                                        <th scope="col">Sent</th>
                                        <th scope="col">Last Sent</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                        @else
                        <br>
                        <div class="text-center p-5" id="message_temp_empty">
                            <h5>No Email Template Found</h5>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="messageTemplate" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">New Email Template</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.email-template.save') }}" method="post" id="messageTemplateForm">
                @csrf
                <div class="modal-body">

                    <div class="form-group mb-3">
                        <label for="">Title<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="title" placeholder="Enter Template Title"
                            required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="">Template Message<span class="text-danger">*</span></label>
                        <textarea name="template_message" id="templateMessage" cols="30" rows="10" class="form-control"
                            placeholder="Hi @clientName" required>Hi @clientName</textarea>
                    </div>
                    <span class="mt-3 text-dark">
                        @clientName will be replaced with your client's display name when sending <a
                            href="javascritp:void(0);" class="add-clientName">( insert @clientName )</a>
                    </span>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary form-submit-btn">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@section('page-scripts')
<script src="{{ asset('front') }}/assets/plugins/datatable/js/jquery.dataTables.min.js"></script>
<script src="{{ asset('front') }}/assets/plugins/datatable/js/dataTables.bootstrap5.min.js"></script>
<script src="{{ asset('front') }}/assets/js/table-datatable.js"></script>
<script>
    $(document).ready(function(){
            var count = $('#template_count').val();
            if(count > 0){
                getMessageTemplate();
            }
        });

        $(document).on('click', '.new-message', function(){
            $('#messageTemplateForm')[0].reset();
            $('#messageTemplate').modal('show');
        });

        $(document).on('click', '.add-clientName', function(e){
            e.preventDefault();
            var textarea = document.getElementById("templateMessage");
            textarea.value += " @clientName";
        });

        $('#messageTemplateForm').submit(function(e) {
            e.preventDefault();
            var url = $(this).attr('action');
            var param = new FormData(this);
            my_ajax(url, param, 'post', function(res) {}, true);
        });

        $('#client_dropdown').change(function() {
            var selectedValue = $(this).val();  // Get the selected dropdown value
            getMessageTemplate(selectedValue);  // Call the function with the selected value
        });

        function getMessageTemplate(dropdownValue = 'all') {
            if ($.fn.DataTable.isDataTable('#message-template-table')) {
                $('#message-template-table').DataTable().destroy();
            }
            $('#message-template-table').DataTable({
                processing: true,
                serverSide: true,
                "order": [
                    [0, "desc"]
                ],
                "pageLength": 10,
                "lengthMenu": [10, 50, 100, 150, 500],
                ajax: {
                    url: "{{ route('admin.email-template.all') }}",
                    data: function(d) {
                        d.search = $('input[type="search"]').val();
                        d.dropdown_value = dropdownValue;
                    },
                },
                columns: [{
                        data: 'client_name',
                        name: 'client_name',
                        orderable: false,
                        searchable: true
                    },
                    {
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
                    // Check if the DataTable has no data
                    if (settings.json.recordsTotal === 0) {
                        // Show the additional div
                        $('#message_temp_empty').show();
                        $('#message-template-responsive').hide();
                    } else {
                        // Hide the additional div if there is data
                        $('#message_temp_empty').hide();
                        $('#message-template-responsive').show();
                    }
                },
            });
        }
</script>
@endsection
