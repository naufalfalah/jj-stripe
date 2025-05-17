@extends('layouts.admin')
@section('page-css')
    <link href="{{ asset('front') }}/assets/plugins/datatable/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
    <style>
        .error{
            color:red;
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
                            <h5 class="mb-0">Send Lead</h5>
                        </div>
                        <hr />
                        <form method="POST" action="{{ route('admin.send_lead.save_lead') }}" class="row g-3 ajaxForm">
                            @csrf
                            <div class="col-md-6">
                                <label for="">Sub Account<span class="text-danger">*</span></label>
                                <select name="sub_account" id="sub_account" class="form-control single-select" required>
                                    <option value="">select....</option>
                                    @foreach($sub_accounts as $sub_account)
                                    <option value="{{$sub_account->hashid}}">{{$sub_account->sub_account_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="">Client<span class="text-danger">*</span></label>
                                <select name="client" id="client" class="form-control single-select" required>
                                    <option value="">select....</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="">Name</label>
                                <input type="text" name="user_name" class="form-control" placeholder="enter user name">
                            </div>
                            <div class="col-md-4">
                                <label for="">Email</label>
                                <input type="email" name="email" class="form-control" placeholder="enter email">
                            </div>
                            <div class="col-md-4">
                                <label for="">Phone Number</label>
                                <input type="text" pattern="\d*" maxlength="8" oninput="this.value = this.value.replace(/[^0-9]/g, '');" class="form-control" name="mobile_number" placeholder="Enter Mobile Number">
                            </div>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <td>Data Key</td>
                                            <td>Data Value</td>
                                            <td>Action</td>
                                        </tr>
                                    </thead>
                                    <tbody id="leadData_body">
                                        <tr>
                                            <td>
                                                <input type="text" class="form-control" name="data[0][key]"
                                                    placeholder="Enter Data Key">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" name="data[0][value]"
                                                    placeholder="Enter Data Value">
                                            </td>
                                            <td>
                                                <a href="javascript:void(0);" class="btn btn-primary add_lead_data_tr"><i
                                                        class="fa-solid fa-circle-plus"
                                                        style="margin-left: 0px; vertical-align: initial;"></i></a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="form-group mb-3 text-right">
                                <button type="submit" class="btn btn-primary px-5 form-submit-btn">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('page-scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>
<script>
    $(document).ready(function() {
        $('.single-select').select2({
            theme: 'bootstrap4',
            width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
            placeholder: $(this).data('placeholder'),
            allowClear: Boolean($(this).data('allow-clear')),
        });
        $("#sub_account").change(function() {
            sub_account = $(this).val(); 

            $.ajax({
                url: '{{ route("admin.send_lead.get_clients") }}',
                method: 'POST',
                data: {
                    '_token': '{{ csrf_token() }}',
                    'subaccount_id': sub_account
                },
                success: function(response) {
                    $("#client").html(response);
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        })

        var leadDataCount = 1;
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
            leadDataCount++
            $("#leadData_body").append(_html);
        });

        $(document).on('click', '.delete_lead_data_tr', function() {
            let id = $(this).data('id');
            $('#lead_data_tr_' + id).remove();
            leadDataCount--;
        });

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
</script>
@endsection
