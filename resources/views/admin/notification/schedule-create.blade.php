@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="border p-4 rounded">
                        <div class="card-title d-flex align-items-center">
                            <h5 class="mb-0">Add Notification Schedule</h5>
                        </div>
                        <hr/>
                        <div class="col-md-12">
                            <label for="title">Title<span class="text-danger">*</span></label>
                            <input type="text" name="title" id="title" placeholder="Enter Title" class="form-control" disabled>
                        </div>
                        <div class="col-md-12">
                            <label for="body">Schedule<span class="text-danger">*</span></label>
                            <input type="text" name="body" id="body" placeholder="Enter Body" class="form-control" disabled>
                        </div>
                        <div class="col-md-12">
                            <label for="type" class="form-label">Type</label>
                            <select name="type" id="type" class="form-select" disabled>
                                @foreach ($types as $key => $type)
                                    <option value="{{ $key }}">{{ $type['label'] }}</option>
                                @endforeach
                            </select>
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
                    <div class="border p-4 rounded">
                        <div class="card-title d-flex align-items-center">
                            <h5 class="mb-0">Add Notification Schedule</h5>
                        </div>
                        <hr/>
                        <form method="POST" action="{{ route('admin.notification.schedule-store', ['id' => $notification->id]) }}"
                            class="row g-3 ajaxForm">
                            @csrf
                            <div class="col-md-12">
                                <label for="client_id" class="form-label">Receiver</label>
                                <select name="client_id" id="client_id" class="form-select">
                                    <option value="0">All</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->client_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label for="published_at">Published At<span class="text-danger">*</span></label>
                                <input type="datetime-local" name="published_at" id="published_at" class="form-control" required>
                            </div>
                            <div class="form-group mb-3 text-right">
                                <input type="hidden" name="id" value="{{ $edit->id ?? null }}">
                                <button type="submit" class="btn btn-primary px-5 form-submit-btn">Save</button>
                            </div>
                        </form>
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
