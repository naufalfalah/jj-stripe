@extends('layouts.admin')

@section('content')


<div class="row">
    <div class="col-xl-12 mx-auto">
        <div class="card">
            <div class="card-body">  
                <div class="border p-4 rounded">
                    <div class="card-title d-flex align-items-center">
                        <h5 class="mb-0">{{$title}}</h5>
                    </div>
                    <hr/>
                    <form method="POST" action="{{route('admin.ebook.save')}}" class="row g-3 ajaxForm" enctype="multipart/form-data" id="ajaxForm">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="inputName" class="col-form-label">Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="inputName" value="{{ $edit->name ?? '' }}" name="name" placeholder="Enter Full Name" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="text" class="col-form-label">Description <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="" value="{{ $edit->description ?? '' }}" name="description" placeholder="Enter description" required>
                            </div>

                            <div class="col-6 mt-2">
                                <label class="form-label">Image <span class="text-danger">(jpeg, jpg, png.)</span></label>
                                <input type="file" id="profile_image" name="image" class="form-control">
                            </div>

                            <div class="col-6 mt-2">
                                <label class="form-label">PDF <span class="text-danger">*</span></label>
                                <input type="file" id="pdf" name="pdf" class="form-control">
                            </div>


                        </div>

                        <div class="row">
                            <label class="col-sm-12 col-form-label"></label>
                            <input type="hidden" name="id" value="{{ $edit->id ?? null }}">
                        </div>
                        <div class="col-sm-9 mt-3">
                            <button type="submit" class="btn btn-primary px-5">{{ isset($edit) && $edit->id ? 'Update' : 'Save' }}</button>
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
            validations = $(".ajaxForm").validate();
            if (validations.errorList.length != 0) {
                return false;
            }
            var url = $(this).attr('action');
            var param = new FormData(this);
            var files = $('#profile_image')[0].files[0] ?? '';
            param.append('profile_image', files);
            my_ajax(url, param, 'post', function(res) {

            },true);
        });


        validations2 = $("#ajaxForm").validate();
        $('#ajaxForm').submit(function(e) {
            e.preventDefault();
            validations2 = $("#ajaxForm").validate();
            if (validations2.errorList.length != 0) {
                return false;
            }
            var url = $(this).attr('action');
            var param = new FormData(this);
            my_ajax(url, param, 'post', function(res) {

            },true);
        })
    });
</script>
@endsection
