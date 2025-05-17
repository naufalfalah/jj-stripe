@extends('layouts.front')
@section('page-css')
    <link href="{{ asset('front/assets/plugins/fileUpload/fileUpload.css') }}" rel="stylesheet" />
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

    <div class="row">
            {{-- <div class="col-lg-12 mx-auto pb-2" id="btn-align">
            <a href="javascript:void(0);" class="btn btn-primary float-end new-message">+ Add New Page</a>
        </div> --}}

             <div class="col-lg-12 mx-auto pb-2" id="btn-align">
                <a href="javascript:void(0);" class="btn btn-primary float-end page-opt">+ Page</a>
            </div>


            <div class="col-lg-12 mx-auto">
                <div class="card">
                    <div class="card-body">
                        <ul class="nav nav-tabs nav-dark" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link main-tabs active" data-bs-toggle="tab" href="#message_template" role="tab"
                                    aria-selected="true" data-type="message_template">
                                    <div class="d-flex align-items-center">
                                        <div class="tab-title">Pages</div>
                                    </div>
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content py-3">
                            <div class="tab-pane fade active show" id="message_template" role="tabpanel">
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
    <br>
                                    <div class="text-center p-5" id="Page_temp_empty">
                                        <h5>No Page Found</h5>
                                    </div>
    @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>



        {{-- new  --}}


        <div class="modal fade" id="choose" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        {{-- <h5 class="modal-title"></h5>  --}}
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-footer modal-footer-centered">
                        <a href="{{ route('user.page.add_page','event_page') }}" class="btn btn-primary open-add-page">+ Product Or Event Page</a>
                        <a href="{{ route('user.page.add_page','image_gallery') }}" class="btn btn-primary new-image-gallery">+ Image Gallery</a>
                    </div>
                </div>
            </div>
        </div>
        {{-- new end  --}}
@endsection
@section('page-scripts')
    <script src="{{ asset('front') }}/assets/plugins/datatable/js/jquery.dataTables.min.js"></script>
        <script src="{{ asset('front') }}/assets/plugins/datatable/js/dataTables.bootstrap5.min.js"></script>
        <script src="{{ asset('front') }}/assets/js/table-datatable.js"></script>


        <script>
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
                    url: "{{ route('user.page.view') }}",
                    data: function(d) {
                        d.search = $('input[type="search"]').val();
                    },
                },
                columns: [

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

        </script>
@endsection
