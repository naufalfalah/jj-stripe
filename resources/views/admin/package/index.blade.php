@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="border p-4 rounded">
                        <div class="card-title d-flex align-items-center">
                            <h5 class="mb-0">{{ isset($edit) ? "Edit Package" : 'Add Package' }}</h5>
                        </div>
                        <hr/>
                        <form method="POST" action="{{ route('admin.package.store') }}" enctype="multipart/form-data" id="packageForm"
                            class="row g-3 ajaxForm">
                            @csrf
                            <div class="col-md-6">
                                <label for="name">Name<span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" value="{{ $edit->name ?? '' }}" placeholder="Enter Package Name" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label for="price">Price<span class="text-danger">*</span></label>
                                <input type="text" name="price" id="price" value="{{ $edit->price ?? '' }}" class="form-control" required>
                            </div>
                            <div class="col-md-12">
                                <label for="url">URL<span class="text-danger">*</span></label>
                                <input type="text" name="url" id="url" value="{{ $edit->url ?? '' }}" placeholder="Enter Package Url" class="form-control" required>
                            </div>
                            <div class="col-md-12">
                                <label for="description">Description<span class="text-danger">*</span></label>
                                <input type="text" name="description" id="description" value="{{ $edit->description ?? '' }}" placeholder="Enter Package Description" class="form-control" required>
                            </div>
                            <div class="col-md-12">
                                <label for="logo">Logo</label>
                                <input type="file" name="logo" id="logo" class="form-control">
                                @if(isset($edit) && $edit->logo)
                                    <img src="{{ asset($edit->logo) }}" alt="" width="100px">
                                @endif
                            </div>
                            <div class="col-md-6">
                                <label for="duration">Duration<span class="text-danger">*</span></label>
                                <input type="text" name="duration" id="duration" value="{{ $edit->duration ?? '' }}" placeholder="Enter Package Duration" class="form-control" required>
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
    <div class="row">
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="card-title d-flex align-items-center">
                        <h5 class="">{{$title}}</h5>
                    </div>
                        <hr>
                    <div class="table-responsive">
                        <table id="example" class="table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th class="text-uppercase ps-2 text-secondary text-xxs font-weight-bolder opacity-7">
                                        #</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Name</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Description</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Price</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Duration</th>
                                    {{-- <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Status</th> --}}
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Stripe Price</th>
                                    <th class="text-uppercase ps-2 text-secondary text-xxs font-weight-bolder opacity-7">
                                        Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($packages as $key => $val)
                                    <tr>
                                        <td>
                                            <p class="text-sm font-weight-bold mb-0">{{ $key + 1 }}</p>
                                        </td>
                                        <td>
                                            <p class="text-sm font-weight-bold mb-0">{{ $val->name }}</p>
                                        </td>
                                        <td>
                                            <a href="javascript:void(0);" class="text-sm text-dark font-weight-bold mb-0" data-bs-toggle="tooltip" data-bs-placement="bottom" title="{{ $val->description }}">{{ Str::limit($val->description, 30, "...") ?? ""}}</a>
                                        </td>
                                        <td>
                                            <p class="text-sm font-weight-bold mb-0">{{ $val->price }}</p>
                                        </td>
                                        <td>
                                            <p class="text-sm font-weight-bold mb-0">{{ $val->duration }}</p>
                                        </td>
                                        {{-- <td>
                                            <p class="text-sm font-weight-bold mb-0">
                                                @if($val->status == 1)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-danger">Inactive</span>
                                                @endif
                                            </p>
                                        </td> --}}
                                        <td>
                                            <p class="text-sm font-weight-bold mb-0">{{ $val->stripe_price_id }}</p>
                                        </td>
                                        <td>
                                            <div class="table-actions d-flex align-items-center gap-3 fs-6">
                                                <a href="{{ route('admin.package.edit', $val->id) }}" class="text-warning" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Edit"><i class="bi bi-pencil-fill"></i></a>
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
@endsection

@section('page-scripts')
    <script src="{{ asset('front') }}assets/plugins/datatable/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#example').DataTable();
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
    </script>
@endsection
