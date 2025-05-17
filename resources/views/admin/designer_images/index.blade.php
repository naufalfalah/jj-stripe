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
                                <h5 class="mb-0">Upload Images</h5>
                            </div>
                            <hr />
                            <form method="POST" action="{{ route('admin.assign_task.save_designer_images') }}"
                                class="row g-3 ajaxFormTopup" enctype="multipart/form-data">
                                @csrf

                                <div class="col-md-6">
                                    <label for="designer_id">Designer Name <span class="text-danger">*</span></label>
                                    <select name="designer_id" id="designer_id" class="form-control" required>
                                        <option value="">Select Designer</option>
                                        @foreach ($designers as $designer)
                                            <option value="{{ $designer->id }}"
                                                {{ isset($edit) && $edit->designer_id == $designer->id ? 'selected' : '' }}>
                                                {{ $designer->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>


                                <div class="col-md-6">
                                    <label for="image">Images <span class="text-danger">*</span></label>
                                    <input type="file" name="images[]" id="image" class="form-control" multiple>
                                </div>

                                <div class="form-group mb-3 text-right">
                                    <input type="hidden" name="id" value="{{ $edit->hashid ?? null }}">
                                    <button type="submit"
                                        class="btn btn-primary px-5 form-submit-btn-topup">{{ isset($edit) ? 'Update' : 'Save' }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>


                <!-- new  -->
                {{-- <div class="row">
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
                </div> --}}


                <div class="row">
                    <div class="col-xl-12 mx-auto">
                        <div class="card">
                            <div class="card-body">
                                {{-- <div class="card-title d-flex align-items-center">
                                    <h5 class="">Daily Ads Spent</h5>
                                </div> --}}
                                {{-- <hr> --}}
                                <div class="row">
                                    <div class="col-12 col-lg-6 col-xl-4">
                                        <div class="form-group my-2">
                                            <label for="ads_filter">Select A designer:</label>
                                            <select id="ads_filter" class="form-select">
                                                <option value="">Select..</option>
                                                @foreach ($designers as $design)
                                                    <option value="{{ $design->id }}">{{ $design->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table id="daily_ads_spent-template-table" class="table table-striped table-bordered"
                                        style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Image</th>
                                                <th>Created_At</th>
                                                <th>Action</th>

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

            getDailyAdsSpent();

            $('#ads_filter').on('change', function() {
                getDailyAdsSpent();
            });

            function getDailyAdsSpent() {
                if ($.fn.DataTable.isDataTable('#daily_ads_spent-template-table')) {
                    $('#daily_ads_spent-template-table').DataTable().destroy();
                }
                $('#daily_ads_spent-template-table').DataTable({
                    processing: true,
                    serverSide: true,
                    "order": [
                        [0, "desc"]
                    ],
                    "pageLength": 10,
                    "lengthMenu": [10, 50, 100, 150, 500],
                    ajax: {
                        url: "{{ route('admin.assign_task.designer_images') }}",
                        data: function(d) {
                            d.ads_id = $('#ads_filter').val();
                            d.search = $('#daily_ads_spent-template-table').DataTable().search();
                        }
                    },
                    columns: [{
                            data: null,
                            render: function(data, type, row, meta) {
                                return meta.row + 1;
                            },
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'image_path',
                            name: 'image_path',
                            orderable: true,
                            searchable: true
                        },
                        {
                            data: 'created_at',
                            name: 'created_at',
                            orderable: true,
                            searchable: true
                        },
                        {
                            data: 'action',
                            name: 'action'
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

                var btn = $('.form-submit-btn-topup');
                btn.prop('disabled', true);
                btn.html(
                    '<span class="d-flex align-items-center"><div class="spinner-border text-dark" role="status"><span class="sr-only">Loading...</span> </div> Saving...</span>'
                );

                var url = $(this).attr('action');
                validations = $(".ajaxFormTopup").validate();
                if (validations.errorList.length != 0) {
                    return false;
                }
                var formData = new FormData(this);
                my_ajax(url, formData, 'post', '', false, function(res) {

                }, true);
            })
        });
    </script>
@endsection
