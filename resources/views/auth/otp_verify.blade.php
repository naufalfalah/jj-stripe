@extends('layouts.user_auth')
@section('title', 'Verify OTP')
@php
$title = 'Verify OTP';
@endphp
@section('page-css')
<style>
    .error{
        color:red;
    }
</style>
@endsection
@section('content')
<div class="container-fluid d-flex justify-content-center align-items-center min-vh-100">
    <div class="email-screen">
        <div class="card shadow rounded-0 overflow-hidden">
            <div class="row g-0">
                <div class="col-lg-12">
                    <div class="card-body p-4 p-sm-5">
                        <h5 class="card-title">Email Verification</h5>
                        <p class="card-text mb-3">
                            Enter the OTP sent to your registered email to verify your account.
                        </p>
                        <p class="text-muted" id="timer-text">Resend OTP in: <span id="timer">15:00</span></p>
                        <form id="verifyOtp" method="POST" novalidate="novalidate" action="{{ route('auth.otp.verify.post') }}">
                            @csrf
                            <input type="hidden" name="id" id="user_id" value="{{ $id }}">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="inputOtp" class="form-label">OTP</label>
                                    <input type="text" class="form-control form-control-lg radius-30" name="otp" id="inputOtp" placeholder="298487" required>
                                </div>
                                <div class="col-12">
                                    <div class="d-grid gap-3">
                                        <button type="submit" id="verify-otp-btn" class="btn btn-lg btn-primary radius-30">Verify</button>
                                        <button id="resend-otp-btn" type="button" class="btn btn-lg btn-light radius-30" disabled>Resend</button>
                                    </div>
                                </div>
                            </div>
                        </form>


                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="login-image-div-monkey">
    <img src="{{asset('front')}}/assets/css/images/login_mbl_image2.png" class="monkey_image" alt="">
</div>

<div class="login-image-div">
    <img src="{{asset('front')}}/assets/css/images/login_mbl_image1.png" class="footer_image" alt="">
</div>
@endsection
@section('page-script')


<script>
    $(document).ready(function () {
        const TIMER_DURATION = 15 * 60 * 1000; // Timer duration (12 seconds for testing)
        const TIMER_KEY = "timerStartTime";
        const RESEND_BUTTON = $('#resend-otp-btn');
        const TIMER_ELEMENT = $('#timer');

        let timerInterval; // For managing the timer interval

        function startTimer() {
            clearInterval(timerInterval); // Clear any existing interval

            let timerStartTime = localStorage.getItem(TIMER_KEY);

            if (!timerStartTime) {
                // If no start time exists, initialize a new one
                timerStartTime = new Date().getTime();
                localStorage.setItem(TIMER_KEY, timerStartTime);
            }

            const endTime = parseInt(timerStartTime) + TIMER_DURATION;

            function updateTimer() {
                const currentTime = new Date().getTime();
                const remainingTime = endTime - currentTime;

                if (remainingTime <= 0) {
                    // Timer finished
                    clearInterval(timerInterval);
                    TIMER_ELEMENT.text("00:00");
                    localStorage.removeItem(TIMER_KEY); // Clear stored start time
                    $('#verify-otp-btn').prop('disabled', true);
                    RESEND_BUTTON.prop('disabled', false);
                } else {
                    // Update the timer display
                    const minutes = Math.floor(remainingTime / (60 * 1000));
                    const seconds = Math.floor((remainingTime % (60 * 1000)) / 1000);
                    TIMER_ELEMENT.text(
                        `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`
                    );
                }
            }

            updateTimer(); // Initial update
            timerInterval = setInterval(updateTimer, 1000); // Update every second
        }

        // Disable the resend button initially
        RESEND_BUTTON.prop('disabled', true);

        // Start or resume the timer
        startTimer();

        $('#resend-otp-btn').click(function(e) {
            e.preventDefault();
            var submitBtn = $('#resend-otp-btn');
            submitBtn.prop('disabled',true);
            submitBtn.html('Sending...');
            userId = $('#user_id').val();
            csrfToken = '{{ csrf_token() }}';
            const formData = new FormData();
            formData.append('_token', csrfToken); // Append CSRF token
            formData.append('user_id', userId);   // Append user ID
            var url = "{{ route('auth.resend_otp') }}";
            my_ajax(url, formData, 'post', function(res) {
                $("#verify-otp-btn").prop('disabled',false);
                submitBtn.html('Resend');
                startTimer(); 
            }, true);
        })
    });
</script>


<script>
    $(document).ready(function () {
        
        validations = $("#verifyOtp").validate();
        $('#verifyOtp').submit(function(e) { 
            e.preventDefault();
            var submitBtn = $('#verify-otp-btn');
            submitBtn.prop('disabled',true);
            submitBtn.html('Verifying...');
            var url = $(this).attr('action');
            validations = $("#verifyOtp").validate();
            if (validations.errorList.length != 0) {
                submitBtn.prop('disabled',false);
                submitBtn.html('Verify');
                return false;
            }
            var formData = new FormData(this);
            my_ajax(url, formData, 'post', function(res) {
                submitBtn.prop('disabled',false);
                submitBtn.html('Verify');
            }, true);
        })   
        
        
    });
</script>





@endsection
