@extends('layouts.admin')
@section('content')
<div class="row">
    <form method="get" action="" class="row g-3 ajaxFormClient">
        <div class="row">
            <div class="form-group col-sm-12 col-lg-6 my-2">
                <label>All Clients <span class="text-danger">*</span></label>
                <input type="hidden" value="{{ request()->input('client') }}" id="client_id">
                <select name="client" id="client" class="form-control single-select" required>
                    <option value="">Select Client</option>
                    @foreach ($clients as $val)
                    <option value="{{ $val->hashid }}" {{ (request()->input('client') == $val->hashid) ? 'selected' : '' }}>
                        {{ $val->client_name }} ( {{ $val->email }} )
                    </option>
                    @endforeach
                </select>
            </div>

        </div>
    </form>
</div>


<div class="user-profile-page">
    <div class="card radius-15">
        <div class="card-body">
            <div class="row p-5">
                <div class="col-12 col-lg-12 border-right">
                    <div class="text-center">
                        <a class="btn btn-white border-dark radius-30" href="javascript:void(0)">
                            <span class="d-flex justify-content-center align-items-center">
                                <img class="me-2" src="{{ asset('front') }}/assets/images/icons/search.svg" width="16"
                                    alt="">
                                <span>Please select client</span>
                            </span>
                        </a>
                        @if($error)
                            <p>The calendar you have selected is not connected to Google Calendar. Please go to settings to connect it.</p>
                        @endif
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
    $(document).ready(function() {
        $(function(){
        $('.single-select').select2({
        });
    });
    })
    $('#client').change(function() { 
        $('.ajaxFormClient').submit();
    })
    // $('.ajaxFormClient').submit(function(e) {
    //     e.preventDefault();
    //     var url = $(this).attr('action');
    //     var param = new FormData(this);
    //     my_ajax(url, param, 'post', function(res) {

    //     },true);
    // });
</script>
@endsection