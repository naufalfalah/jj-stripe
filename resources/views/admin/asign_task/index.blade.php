@extends('layouts.admin')
@section('page-css')
    <link href="{{ asset('front') }}/assets/plugins/datatable/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
    <style>
        .error {
            color: red;
        }
    </style>
@endsection
@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-xl-12 mx-auto">

                <div class="card shadow">
                    <div class="card-body">
                        <div class="border p-4 rounded">
                            <div class="card-title d-flex align-items-center">
                                <h5 class="mb-0">Create Designer</h5>
                            </div>
                            <hr />
                            <form method="POST" action="{{ route('admin.assign_task.save_designer') }}"
                                class="row g-3 ajaxFormTopup" enctype="multipart/form-data">
                                @csrf

                                <div class="col-md-4">
                                    <label for="min_topup">Name<span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="" value="{{ $edit->name ?? '' }}"
                                        placeholder="Enter Minimum Topup Value" class="form-control" required>

                                </div>

                                <div class="col-md-4">
                                    <label for="default_topup">Image<span class="text-danger">*</span></label>
                                    <input type="file" name="image" id="" value="" class="form-control">
                                </div>

                                <div class="col-md-4">
                                    <label for="default_topup">Description<span class="text-danger">*</span></label>
                                    <textarea name="description" id="" class="form-control" required>{{ $edit->description ?? '' }}</textarea>
                                </div>



                                <div class="form-group mb-3 text-right">
                                    <input type="hidden" name="id" value="{{ $edit->hashid ?? null }}">
                                    <button type="submit" class="btn btn-primary px-5 form-submit-btn">{{ isset($edit) ? 'Update' : 'Save' }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>


                <!-- new  -->
                <div class="row">
                    <div class="col-lg-12 mx-auto">
                        <div class="card">
                            <div class="card-body">

                                <div class="tab-content py-3">
                                    <div class="tab-pane fade active show" id="allClients" role="tabpanel">

                                        <div class="row">
                                            <div class="col">
                                                <div class="table-responsive" id="all-leads-responsive">
                                                    <table class="table table-hover mb-0" id="allLeads-table">
                                                        <thead>
                                                            <tr>
                                                                <th>S.No</th>
                                                                <th scope="col">NAME</th>
                                                                <th scope="col">Description</th>
                                                                <th scope="col">Image</th>
                                                                <th scope="col">Action</th>

                                                            </tr>
                                                        </thead>
                                                        <tbody>

                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- neew end  -->

            </div>
        </div>
    </div>
@endsection
@section('page-scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>
    <script src="{{ asset('front') }}/assets/plugins/datatable/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('front') }}/assets/plugins/datatable/js/dataTables.bootstrap5.min.js"></script>
    <script src="{{ asset('front') }}/assets/js/table-datatable.js"></script>
    <script>
        $(document).ready(function() {

            allLeads();

            function allLeads() {
                if ($.fn.DataTable.isDataTable('#allLeads-table')) {
                    $('#allLeads-table').DataTable().destroy();
                }

                $('#allLeads-table').DataTable({
                    processing: true,
                    serverSide: true,
                    "order": [
                        [0, "desc"]
                    ],
                    "pageLength": 10,
                    "lengthMenu": [10, 50, 100, 150, 500],
                    ajax: {
                        url: "{{ route('admin.assign_task.create_design') }}",
                        data: function(d) {
                            d.search = $('input[type="search"]').val()
                        },
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'name',
                            name: 'name',
                            orderable: false,
                            searchable: true
                        },
                        {
                            data: 'description',
                            name: 'description',
                            orderable: false,
                            searchable: true
                        },

                        {
                            data: 'image',
                            name: 'image',
                            orderable: false,
                            searchable: true
                        },

                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: true
                        },

                    ],

                });
            }

            validations = $(".ajaxForm").validate();
            $('.ajaxForm').submit(function(e) {

                e.preventDefault();
                var url = $(this).attr('action');
                validations = $(".ajaxForm").validate();
                if (validations.errorList.length != 0) {
                    return false;
                }
                var formData = new FormData(this);
                my_ajax(url, formData, 'post', function(res) {

                }, true);
            })
        });

        $(document).ready(function() {
            validations = $(".ajaxFormTopup").validate();
            $('.ajaxFormTopup').submit(function(e) {
                e.preventDefault();

                var url = $(this).attr('action');
                validations = $(".ajaxFormTopup").validate();
                if (validations.errorList.length != 0) {
                    return false;
                }
                var formData = new FormData(this);
                my_ajax(url, formData, 'post', function(res) {
                    
                }, true);
            })
        });
    </script>
@endsection
