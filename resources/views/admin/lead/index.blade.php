@extends('layouts.admin')

@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-xl-12 mx-auto">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-center">
                            <h5 class="card-title">{{ $title }}</h5>
                        </div>
                        <hr>
                        <div class="table-responsive">
                            <table id="link-email-table" class="table table-striped table-bordered" style="width:100%">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase ps-2 text-secondary text-xxs font-weight-bolder opacity-7">
                                            No</th>
                                        </th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Linked Email</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Name</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Email</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Mobile</th>
                                        <th class="text-uppercase ps-2 text-secondary text-xxs font-weight-bolder opacity-7">
                                            Lead Type</th>
                                        <th
                                            class="text-uppercase ps-2 text-secondary text-xxs font-weight-bolder opacity-7">
                                            Verified</th>
                                        <th
                                            class="text-uppercase ps-2 text-secondary text-xxs font-weight-bolder opacity-7">
                                            Address</th>
                                        <th
                                            class="text-uppercase ps-2 text-secondary text-xxs font-weight-bolder opacity-7">
                                            Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($users as $key => $val)
                                        <tr>
                                            <td class="col-6">
                                                <p class="text-sm font-weight-bold mb-0">{{ $key + 1 }}</p>
                                            </td>
                                            <td>
                                                <p class="text-sm font-weight-bold mb-0">{{ $val->client_email }}</p>
                                            </td>
                                            <td>
                                                <p class="text-sm font-weight-bold mb-0">{{ $val->client_name }}</p>
                                            </td>
                                            <td>
                                                <p class="text-sm font-weight-bold mb-0">{{ $val->email }}</p>
                                            </td>
                                            <td>
                                                <p class="text-sm font-weight-bold mb-0">{{ $val->phone_number }}</p>
                                            </td>
                                            <td>
                                                <p class="text-sm font-weight-bold mb-0">{{ $val->package }}</p>
                                            </td>
                                            <td>
                                                <p class="text-sm font-weight-bold mb-0">{{ $val->email_verified_at }}</p>
                                            </td>
                                            <td>
                                                <p class="text-sm font-weight-bold mb-0" data-bs-toggle="tooltip"
                                                data-bs-placement="bottom" title="{{ $val->address }}"
                                                aria-label="{{ $val->address }}">{{ Str::limit($val->address, 12) }}</p>
                                            </td> 
                                            <td>
                                                <div class="table-actions d-flex align-items-center gap-3 fs-6">
                                                    <a href="javascript:void(0);" class="text-primary change-linked-email" data-bs-toggle="tooltip"
                                                        data-bs-placement="bottom" title="Edit" data-bs-original-title="Edit"
                                                        aria-label="Edit" data-id="{{ $val->id }}" data-email="{{ $val->client_email }}"><i class="bi bi-pencil-fill"></i></a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>              
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="changeLinkedEmail" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Linked Client Email</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.lead.update') }}"
                    method="post" id="ajaxForm">
                    @csrf
                    @method('put')
                    <input type="hidden" id="lead_id" name="lead_id">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="email">Linked Email</label>
                            <input name="email" id="email" class="form-control" required />
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary form-submit-btn">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('page-scripts')
    <script>
        $(document).ready(function() {
            $('[data-bs-toggle="tooltip"]').tooltip();
        });

        $(document).on('click', '.change-linked-email', function() {
            let id = $(this).data('id');
            let email = $(this).data('email');

            $('#lead_id').val(id);
            $('#email').val(email);
            $('#changeLinkedEmail').modal('show');
        });

        $(document).ready(function() {
            validations = $("#addEventForm").validate();
            $('#ajaxForm').submit(function(e) {
                e.preventDefault();
                validations = $("#ajaxForm").validate();
                if (validations.errorList.length != 0) {
                    return false;
                }
                var url = $(this).attr('action');
                var param = new FormData(this);
                my_ajax(url, param, 'post', function(res) {}, true);
            });
        });
    </script>
@endsection
