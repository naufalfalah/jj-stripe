@extends('layouts.admin')
@section('content')
    <div class="container-fluid py-4">

        <div class="row">
            <div class="col-xl-12 mx-auto">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-center">
                            <h5 class="">{{ $title }}</h5>
                        </div>
                        <hr>
                        <div class=  "alert border-0 bg-light-success alert-dismissible fade show" role="alert"
                            style="display: none;">
                            URL copied to clipboard!
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <div class="table-responsive">
                            <table id="ebook-table" class="table table-striped table-bordered" style="width:100%">
                                <thead>
                                    <tr>
                                        <th
                                            class="text-uppercase ps-2 text-secondary text-xxs font-weight-bolder opacity-7">
                                            S NO:</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Name</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Description</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Last Modified</th>

                                        <th
                                            class="text-uppercase ps-2 text-secondary text-xxs font-weight-bolder opacity-7">
                                            Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- @foreach ($ebooks as $key => $ebook)
                                        <tr>
                                            <td>
                                                <p class="text-sm font-weight-bold mb-0">{{ $key + 1 }}</p>
                                            </td>
                                            <td>
                                                <p class="text-sm font-weight-bold mb-0">{{ $ebook->name }}</p>
                                            </td>
                                            <td>
                                                <a href="javascript:void(0);"
                                                    class="text-sm text-dark font-weight-bold mb-0" data-bs-toggle="tooltip"
                                                    data-bs-placement="bottom"
                                                    title="{{ $ebook->description }}">{{ Str::limit($ebook->description, 30, '...') ?? 'No Ebook Description Found' }}</a>
                                            </td>

                                            <td>{{ $ebook->updated_at->format('M-d-Y - h:i a') ?? '' }}</td>

                                            <td>
                                                <div class="table-actions d-flex align-items-center gap-3 fs-6">

                                                    <a href="{{ route('admin.ebook.edit', $ebook->hashid) }}"
                                                        class="text-warning" data-bs-toggle="tooltip"
                                                        data-bs-placement="bottom" title="Edit"><i
                                                            class="bi bi-pencil-fill"></i></a>
                                                    <a href="javascript:void(0)" class="text-danger"
                                                        onclick="ajaxRequest(this)"
                                                        data-url="{{ route('admin.ebook.delete', $ebook->hashid) }}"
                                                        data-toggle="tooltip" data-placement="top" title=""
                                                        data-original-title="Delete"><i class="bi bi-trash-fill"></i></a>
                                                    <a href="javascript:void(0)" class="text-success copy-button fs-5"
                                                        data-clipboard-text="{{ route('ebook_file_view', ['web', $ebook->hashid]) }}"
                                                        data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                        title="Copy File Url">
                                                        <i class="fadeIn animated bx bx-copy"></i>
                                                    </a>
                                                    <a href="{{ route('admin.ebook.details', $ebook->hashid) }}"
                                                        class="text-primary fs-5" data-bs-toggle="tooltip"
                                                        data-bs-placement="bottom" title="View File Detail">
                                                        <i class="bi bi-eye-fill"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach --}}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection
    @section('page-scripts')
        <script src="{{ asset('front') }}assets/plugins/datatable/js/jquery.dataTables.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.8/clipboard.min.js"></script>

        <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>



        <script>
            // $(document).ready(function() {
            //     $('#example').DataTable();
            // });

            $(document).ready(function() {
                getAllEbooks();

            });

            $(document).ready(function() {
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

            function getAllEbooks() {

                if ($.fn.DataTable.isDataTable('#ebook-table')) {
                    $('#ebook-table').DataTable().destroy();
                }
                $('#ebook-table').DataTable({
                    processing: true,
                    serverSide: true,
                    "order": [
                        [0, "desc"]
                    ],
                    "pageLength": 10,
                    "lengthMenu": [10, 50, 100, 150, 500],
                    ajax: {
                        url: "{{ route('admin.ebook.all') }}",
                        data: function(d) {
                            d.search = $('input[type="search"]').val();
                        },
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
                            data: 'name',
                            name: 'name',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'description',
                            name: 'description',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'updated_at',
                            name: 'updated_at',
                            orderable: true,
                            searchable: false
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: true,
                            searchable: false
                        },
                    ],
                });
            }
        </script>

        <script>
            $(function() {
                $('[data-bs-toggle="tooltip"]').tooltip();
            })


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
        </script>
    @endsection
