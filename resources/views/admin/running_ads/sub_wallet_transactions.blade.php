@extends('layouts.admin')
@section('page-css')
<link href="{{ asset('front') }}/assets/plugins/datatable/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
@endsection
@section('content')
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-3 row-cols-xxl-3">
        <div class="col">
            <div class="card radius-10 border-0 border-start border-primary border-3">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="">
                            <p class="mb-1">Main wallet Balance</p>
                            <h4 class="mb-0 text-primary">{{get_price($main_wallet_bls)}}</h4>
                        </div>
                        <div class="ms-auto widget-icon bg-primary text-white">
                            <i class="bi bi-currency-dollar"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card radius-10 border-0 border-start border-primary border-3">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="">
                            <p class="mb-1">Sub wallet Budget</p>
                            <h4 class="mb-0 text-primary">{{get_price($sub_wallet_budget->spend_amount)}}</h4>
                        </div>
                        <div class="ms-auto widget-icon bg-primary text-white">
                            <i class="bi bi-currency-dollar"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card radius-10 border-0 border-start border-primary border-3">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="">
                            <p class="mb-1">Remaining Balance</p>
                            <h4 class="mb-0 text-primary">{{get_price($sub_wallet_remaining)}}</h4>
                        </div>
                        <div class="ms-auto widget-icon bg-primary text-white">
                            <i class="bi bi-currency-dollar"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="row">
    <div class="col-xl-12 mx-auto">
        <div class="card">
            <div class="card-body">
                <div class="card-title d-flex align-items-center">
                    <h5 class="">Transactions Detail</h5>
                </div>
                <div class="table-responsive">
                    <table id="transaction-table" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Amount In</th>
                                <th>Amount Out</th>
                                <th>Description</th>
                                <th>Date & Time</th>
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

{{-- </div> --}}

@endsection
@section('page-scripts')
<script src="{{ asset('front') }}/assets/plugins/datatable/js/jquery.dataTables.min.js"></script>
<script src="{{ asset('front') }}/assets/plugins/datatable/js/dataTables.bootstrap5.min.js"></script>
<script src="{{ asset('front') }}/assets/js/table-datatable.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>
<script>
    $(document).ready(function() {
        getTransactions();
    });


    function getTransactions() {
            if ($.fn.DataTable.isDataTable('#transaction-table')) {
                $('#transaction-table').DataTable().destroy();
            }

            $('#transaction-table').DataTable({
                processing: true,
                serverSide: true,
                "order": [
                    [0, "desc"]
                ],
                "pageLength": 10,
                "lengthMenu": [10, 50, 100, 150, 500],

                ajax: {
                    type: 'POST',
                    url: "{{ route('admin.sub_account.advertisements.sub_wallets_transactions', ['sub_account_id' => session()->get('sub_account_id')]) }}",
                    data: function(d) {
                        d.search = $('#transaction-table').DataTable().search();
                        d.ads_id = '{{$sub_wallet_budget->id}}';
                        d._token = "{{ csrf_token() }}";
                        d.client_id = "{{$client_id}}";
                    },
                },

                columns: [
                    {
                        data: null,
                        render: function (data, type, row, meta) {
                        return meta.row + 1;
                        },
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'amount_in',
                        name: 'amount_in',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'amount_out',
                        name: 'amount_out',
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
                        data: 'created_at',
                        name: 'created_at',
                        orderable: true,
                        searchable: false
                    },

                ],
            });



        }
        
</script>
@endsection
