@extends('layouts.front')

@section('content')
<div class="user-profile-page">
    <div class="card radius-15">
        <div class="card-body">
            <div class="row p-5">
                <div class="col-12 col-lg-12 border-right">
                    <div class="text-center">
                        <a class="btn btn-white border-dark radius-30" href="{{ route('user.google.integrate') }}">
                            <span class="d-flex justify-content-center align-items-center">
                                <img class="me-2" src="{{ asset('front') }}/assets/images/icons/search.svg" width="16"
                                    alt="">
                                <span>Connect Google Account</span>
                            </span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <!--end row-->
    </div>
</div>
@endsection
@section('page-scripts')
<script>
    $('#ajaxForm').submit(function(e) {
        e.preventDefault();
        var url = $(this).attr('action');
        var param = new FormData(this);
        my_ajax(url, param, 'post', function(res) {

        },true);
    });
</script>
@endsection
