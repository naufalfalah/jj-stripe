@extends('layouts.front')
@section('page-css')
    <link href="{{ asset('front') }}/assets/plugins/datatable/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
@endsection
@section('content')
    <div class="row">
        <div class="col-12 col-xl-12">
            <div class="card shadow radius-10 w-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Leads</h6>
                    </div>
                    <div class="table-responsive mt-2" id="table_ppc_lead">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th id="date_column">Date</th>
                                    <th id="name_column">Name</th>
                                    <th id="email_column">Email</th>
                                    <th id="phone_number_column">Phone Number</th>
                                    <th id="qualifying_column">Qualifying Questions</th>
                                    <th id="status_column">Status</th>
                                    <th id="action_column">Action</th>
                                </tr>
                            </thead>
                            <tbody id="leads-table">
                                <tr class="loader-row">
                                    <td colspan="8" class="text-center">
                                        <div class="spinner-border" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="view_lead_details" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Lead Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-1">
                        <div class="col-6 col-md-6 col-lg-6">
                            <h5>Name</h5>
                            <p id="client_name"></p>
                        </div>
                        <div class="col-6 col-md-6 col-lg-6">
                            <h5>Email</h5>
                            <p id="client_email"></p>
                        </div>
                        <div class="col-6 col-md-6 col-lg-6">
                            <h5>Phone Number</h5>
                            <p id="client_mobile_number"></p>
                        </div>
                        <div class="col-6 col-md-6 col-lg-6">
                            <h5>Status</h5>
                            <p id="lead_status"></p>
                        </div>
                    </div>
                    <h5>Qualifying Questions</h5>

                    <div class="row">
                        <div class="col-6 col-md-12 col-lg-12">
                            <div id="leadData_body"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
                </form>
            </div>
        </div>
    </div>

    {{-- <div class="modal fade" id="editContactDetailsModal" tabindex="-1" aria-hidden="true">
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
                            <input type="text" class="form-control" name="client_name" id="client_name" placeholder="Enter Client NAME" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="">Email</label>
                            <input type="email" class="form-control" name="email" id="client_email" placeholder="Enter Email" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="">Mobile Number</label>
                            <div class="input-group mb-3">
                                <span class="input-group-text bg-white p-0" id="basic-addon1">
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="30" zoomAndPan="magnify" viewBox="0 0 30 30.000001" height="40" preserveAspectRatio="xMidYMid meet" version="1.0"><defs><clipPath id="id1"><path d="M 2.675781 6.132812 L 27.355469 6.132812 L 27.355469 24.277344 L 2.675781 24.277344 Z M 2.675781 6.132812 " clip-rule="nonzero"/></clipPath><clipPath id="id2"><path d="M 2.675781 6.132812 L 27.355469 6.132812 L 27.355469 16 L 2.675781 16 Z M 2.675781 6.132812 " clip-rule="nonzero"/></clipPath><clipPath id="id3"><path d="M 4 6.132812 L 10 6.132812 L 10 15 L 4 15 Z M 4 6.132812 " clip-rule="nonzero"/></clipPath></defs><g clip-path="url(#id1)"><path fill="rgb(93.328857%, 93.328857%, 93.328857%)" d="M 27.347656 21.488281 C 27.347656 23.027344 26.121094 24.277344 24.609375 24.277344 L 5.421875 24.277344 C 3.910156 24.277344 2.683594 23.027344 2.683594 21.488281 L 2.683594 8.925781 C 2.683594 7.382812 3.910156 6.132812 5.421875 6.132812 L 24.609375 6.132812 C 26.121094 6.132812 27.347656 7.382812 27.347656 8.925781 Z M 27.347656 21.488281 " fill-opacity="1" fill-rule="nonzero"/></g><g clip-path="url(#id2)"><path fill="rgb(92.939758%, 16.079712%, 22.349548%)" d="M 27.347656 15.207031 L 27.347656 8.925781 C 27.347656 7.382812 26.121094 6.132812 24.609375 6.132812 L 5.421875 6.132812 C 3.910156 6.132812 2.683594 7.382812 2.683594 8.925781 L 2.683594 15.207031 Z M 27.347656 15.207031 " fill-opacity="1" fill-rule="nonzero"/></g><g clip-path="url(#id3)"><path fill="rgb(100%, 100%, 100%)" d="M 6.792969 10.671875 C 6.792969 8.867188 7.90625 7.355469 9.402344 6.945312 C 9.117188 6.875 8.816406 6.832031 8.507812 6.832031 C 6.425781 6.832031 4.738281 8.550781 4.738281 10.671875 C 4.738281 12.789062 6.425781 14.507812 8.507812 14.507812 C 8.816406 14.507812 9.117188 14.464844 9.402344 14.394531 C 7.90625 13.984375 6.792969 12.472656 6.792969 10.671875 Z M 6.792969 10.671875 " fill-opacity="1" fill-rule="nonzero"/></g><path fill="rgb(93.328857%, 93.328857%, 93.328857%)" d="M 10.90625 7.53125 L 11.058594 8.011719 L 11.554688 8.011719 L 11.152344 8.308594 L 11.308594 8.792969 L 10.90625 8.492188 L 10.5 8.792969 L 10.65625 8.308594 L 10.253906 8.011719 L 10.75 8.011719 Z M 9.535156 12.414062 L 9.6875 12.898438 L 10.1875 12.898438 L 9.78125 13.195312 L 9.9375 13.675781 L 9.535156 13.378906 L 9.132812 13.675781 L 9.285156 13.195312 L 8.882812 12.898438 L 9.378906 12.898438 Z M 12.273438 12.414062 L 12.429688 12.898438 L 12.925781 12.898438 L 12.523438 13.195312 L 12.675781 13.675781 L 12.273438 13.378906 L 11.871094 13.675781 L 12.027344 13.195312 L 11.625 12.898438 L 12.121094 12.898438 Z M 8.847656 9.625 L 9.003906 10.105469 L 9.5 10.105469 L 9.097656 10.402344 L 9.253906 10.886719 L 8.847656 10.585938 L 8.445312 10.886719 L 8.601562 10.402344 L 8.199219 10.105469 L 8.695312 10.105469 Z M 12.960938 9.625 L 13.113281 10.105469 L 13.613281 10.105469 L 13.207031 10.402344 L 13.363281 10.886719 L 12.960938 10.585938 L 12.558594 10.886719 L 12.710938 10.402344 L 12.308594 10.105469 L 12.804688 10.105469 Z M 12.960938 9.625 " fill-opacity="1" fill-rule="nonzero"/></svg>
                                    +65
                                </span>
                                <input type="text" oninput="this.value = this.value.replace(/[^0-9]/g, '');" maxlength="8" class="form-control" id="client_mobile_number" name="mobile_number" placeholder="Enter Mobile Number">
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="lead_status">Status</label>
                            <select name="lead_status" id="lead_status" class="form-control" required>
                                <option value="">Select Status</option>
                                <option value="contacted">Contacted</option>
                                <option value="appointment_set">Appointment Set</option>
                                <option value="burst">Burst</option>
                                <option value="follow_up">Follow Up</option>
                                <option value="call_back">Call Back</option>
                            </select>
                        </div>

                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <td>Key</td>
                                        <td>Value</td>
                                        <td>Action</td>
                                    </tr>
                                </thead>
                                <tbody id="leadData_body">
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
    </div> --}}
@endsection
@section('page-scripts')
    <script src="{{ asset('front') }}/assets/plugins/datatable/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('front') }}/assets/plugins/datatable/js/dataTables.bootstrap5.min.js"></script>
    <script src="{{ asset('front') }}/assets/js/table-datatable.js"></script>
    <script>
        $(document).ready(function() {
            $.ajax({
                type: 'GET',
                url: "{{ route('user.product-tour-user.step') }}",
                data: {
                    menu: 'leads'
                },
                success: function(response) {
                    response.forEach(function(step) {
                        tour.addStep({
                            id: step.id,
                            title: step.title,
                            text: step.desc,
                            attachTo: {
                                element: step.element,
                                on: step.position
                            },
                            buttons: [{
                                text: step.is_last_step ? 'Complete' : 'Next',
                                action: step.is_last_step ? tour.complete : tour
                                    .next,
                            }]
                        });
                    });
                    tour.start();
                },
                error: function(err) {
                    console.log('error tour ' + err);
                    alert('Failed to fetch data product tour');
                }
            });
            tour.on('cancel', function() {
                completeOrCancelStep('leads');
            });
            tour.on('complete', function() {
                completeOrCancelStep('leads');
            });
        });
    </script>
    <script>
        var leadDataCount = 0;
        $(document).ready(function() {
            get_ppc_leads();
        });

        $(document).on('click', ".edit-contact", function() {
            let data = $(this).data('data');
            $("#client_id").val(data.id);
            $("#client_name").val(data.name);
            $("#client_email").val(data.email);
            $("#client_mobile_number").val(data.mobile_number);
            $("#lead_status").val(data.admin_status);
            $("#leadData_body").html('');
            leadDataCount = data.lead_data ? data.lead_data.length : 0;
            for (let index = 0; index < leadDataCount; index++) {
                if (index == 0) {
                    let leadData = data.lead_data[index];
                    let _html = `<tr id="lead_data_tr_${index}">
                                <td>
                                    <input type="text" class="form-control" name="data[${index}][key]" value="${leadData.key}" placeholder="Enter Data Key" required>
                                </td>

                                <td>
                                    <input type="text" class="form-control" name="data[${index}][value]" value="${leadData.value}" placeholder="Enter Data Value" required>
                                </td>
                                <td>
                                    <a href="javascript:void(0);" class="btn btn-primary add_lead_data_tr"><i class="fa-solid fa-circle-plus" style="margin-left: 0px; vertical-align: initial;"></i></a>
                                </td>
                            </tr>`;
                    $("#leadData_body").append(_html);
                } else {
                    let leadData = data.lead_data[index];
                    let _html = `<tr id="lead_data_tr_${index}">
                                <td>
                                    <input type="text" class="form-control" name="data[${index}][key]" value="${leadData.key}" placeholder="Enter Data Key" required>
                                </td>

                                <td>
                                    <input type="text" class="form-control" name="data[${index}][value]" value="${leadData.value}" placeholder="Enter Data Value" required>
                                </td>
                                <td>
                                    <a href="javascript:void(0);" class="btn btn-danger delete_lead_data_tr" data-id="${index}"><i class="fa-solid fa-trash" style="margin-left: 0px; vertical-align: initial;"></i></a>
                                </td>
                            </tr>`;
                    $("#leadData_body").append(_html);
                }
            }
            $('#editContactDetailsModal').modal('show');
        });

        $(document).on('click', ".view_lead_detail", function() {
            let data = $(this).data('data');
            $("#client_name").html(data.name);
            $("#client_email").html(data.email);
            $("#client_mobile_number").html(data.mobile_number);
            if (data.admin_status != null) {
                $("#lead_status").html(data.admin_status.toUpperCase());
            }
            $("#leadData_body").html('');
            leadDataCount = data.lead_data ? data.lead_data.length : 0;
            for (let index = 0; index < leadDataCount; index++) {
                let leadData = data.lead_data[index];
                let _html = `
                                <p>${leadData.key}: ${leadData.value}</p>`;
                $("#leadData_body").append(_html);
            }
            $('#view_lead_details').modal('show');
        });

        $('#leadForm').submit(function(e) {
            e.preventDefault();
            var url = $(this).attr('action');
            var param = new FormData(this);
            my_ajax(url, param, 'post', function(res) {}, true);
        });
        $(function() {
            $('[data-bs-toggle="tooltip"]').tooltip();
        });

        function get_ppc_leads() {
            var ppc_leads = "ppc_leads";
            var data = {
                '_token': "{{ csrf_token() }}",
                ppc_leads: ppc_leads,
                num: 'show',
            };
            $('#task-overview-table').html(
                '<tr class="loader-row"><td colspan="3" class="text-center"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>'
                );
            $.ajax({
                url: "{{ route('user.get_leads') }}",
                type: 'POST',
                data: data,
                success: function(response) {
                    if (response) {
                        $('#leads-table').html(response.body);
                    }

                },
                error: function(xhr, status, error) {
                    $('.loader-row').remove();
                }
            });
        }

        $(document).on('change', '.change_lead_status', function() {
            let id = $(this).data('id');
            let status = $(this).val();
            var data = {
                '_token': "{{ csrf_token() }}",
                lead_id: id,
                admin_status: status
            };

            getAjaxRequests("{{ route('user.leads-management.lead_status') }}", data, 'POST', true, function(res) {
                if (res.success) {
                    toast(res.success, "Success!", 'success', 1200);
                }
            });

        });

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
            leadDataCount++;
            $("#leadData_body").append(_html);
        });

        $(document).on('click', '.delete_lead_data_tr', function() {
            let id = $(this).data('id');
            $('#lead_data_tr_' + id).remove();
            leadDataCount--;
        });
    </script>
@endsection
