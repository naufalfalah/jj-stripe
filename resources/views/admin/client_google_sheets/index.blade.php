@extends('layouts.admin')

@push('styles')
    <link href="{{ asset('front') }}/assets/plugins/datatable/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
    <style>
        .disabled-link {
            pointer-events: none;
            cursor: default;
            color: gray;
        }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-lg-12 mx-auto">
            <div class="card">
                <div class="card-title p-3 text-bold">
                    Clients Google Sheet
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="client_sheets">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Client Name</th>
                                    <th>Client Agency</th>
                                    <th>Client Industry</th>
                                    <th>Client Sheets</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('front') }}/assets/plugins/datatable/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('front') }}/assets/plugins/datatable/js/dataTables.bootstrap5.min.js"></script>
    <script src="{{ asset('front') }}/assets/js/table-datatable.js"></script>
    <script>
        $(document).ready(function() {
            $("#client_sheets").DataTable({
                processing: true,
                serverSide: true,
                "pageLength": 10,
                ajax: {
                    url: "{{ route('admin.client_sheets.index') }}",
                },
                columns: [{
                        name: 'DT_RowIndex',
                        data: 'DT_RowIndex'
                    },
                    {
                        name: 'client_name',
                        data: 'client_name'
                    },
                    {
                        name: 'agency',
                        data: 'agency'
                    },
                    {
                        name: 'industry',
                        data: 'industry'
                    },
                    {
                        name: 'spreadsheet',
                        data: 'spreadsheet'
                    },
                ]
            })
        })
    </script>
@endpush
