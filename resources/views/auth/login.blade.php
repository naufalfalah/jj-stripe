@extends('layouts.user_auth')
@section('title', 'Login')

@php
    $title = 'login';
@endphp

@section('page-css')
<style>
    @media only screen and (max-width: 1100px) {
        .desktop-image {
            display: none;
        }
        .mobile-image {
            display: block;
        }
        .coverr {
            width: 95% !important;
            margin: auto;
            margin-top: 40px;
        }
        /* .card {
            height: 500px !important;
        } */
        .sdsd {
            background-image: url('{{ asset('front') }}/assets/images/login-mobile-logo.png');
            background-position: 100% 100%;
            background-size: contain;
            background-repeat: no-repeat;
        }
        .main-logo {
            display: none !important;
        }
    }
</style>
@endsection

@section('content')
<div class="sdsd h-100 w-100">
    <img src="{{ asset('front') }}/assets/images/login-logo-hd.png" class="main-logo position-absolute w-100 h-100" alt="">
    <div class="coverr d-flex justify-content-center align-items-center w-75 h-75">
        <div class="card shadow rounded-0 overflow-hidden" style="width: 700px;">
            <div class="row g-0">
                <div class="col-lg-12">
                    <div class="card-body p-4 p-sm-4">
                        <div class="row">
                            <div class="col-sm-7">
                                <h5 class="card-title">Sign In</h5>
                                <p class="card-text mb-4">Recognized Google Partner.</p>
                            </div>
                            <div class="col-sm-5">
                                <img src="{{ asset('front') }}/assets/images/google-login.jpg" alt="" style="width: 250px;">
                            </div>
                        </div>
                        <form class="form-body" method="POST" action="{{ route('auth.login.submit') }}">
                            @csrf
                            <div class="row g-3">
                                <div class="col-6 col-lg-12">
                                    <label for="inputEmailAddress" class="form-label">Email Address</label>
                                    <div class="ms-auto position-relative">
                                        <div class="position-absolute top-50 translate-middle-y search-icon px-3">
                                            <i class="bi bi-envelope-fill"></i>
                                        </div>
                                        <input type="email" class="form-control radius-30 ps-5" name="email"
                                            autocomplete="email" value="{{ old('email') }}" id="inputEmailAddress"
                                            placeholder="Email">
                                    </div>
                                    @error('email')
                                        <div class="text-danger fw-bold small">{!! $message !!}</div>
                                    @enderror
                                </div>
                                <div class="col-6 col-lg-12">
                                    <label for="inputChoosePassword" class="form-label">Enter Password</label>
                                    <div class="ms-auto position-relative">
                                        <div class="position-absolute top-50 translate-middle-y search-icon px-3">
                                            <i class="bi bi-lock-fill"></i>
                                        </div>
                                        <input type="password" class="form-control radius-30 ps-5 pe-5" name="password" id="inputChoosePassword" placeholder="Password">
                                        <button type="button" class="btn position-absolute top-50 end-0 translate-middle-y p-0 me-3" id="togglePassword" aria-label="Toggle Password">
                                            <i class="bi bi-eye-slash" id="eyeIcon"></i>
                                        </button>
                                    </div>
                                    @error('password')
                                        <div class="text-danger fw-bold small">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" name="remember" type="checkbox" id="flexSwitchCheckChecked">
                                        <label class="form-check-label sm-txt-log-reg" for="flexSwitchCheckChecked">Remember Me</label>
                                    </div>
                                </div>
                                <div class="col-6 text-end sm-txt-log-reg">
                                    <a href="{{ route('password.request') }}">Forgot Password?</a>
                                </div>
                                <div class="col-6">
                                    <div class="d-grid">
                                        <p class="mb-0 f sm-txt-log-reg">Don't have an account yet?
                                            <a href="{{ route('register') }}">Sign up here</a>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <button type="submit" class="btn btn-primary float-end btn-md radius-30">Sign In</button>
                                </div>
                            </div>
                        </form>
                        <hr>
                        <div class="mt-4">
                            <div class="d-flex justify-content-center flex-column flex-lg-row">
                                <a href="{{ route('auth.social', ['provider' => 'google', 'redirect' => url()->current()]) }}" class="btn btn-danger m-2 btn-md radius-30">
                                    <i class="bi bi-google me-2"></i>Login with Google
                                </a>
                                <a href="{{ route('auth.social', ['provider' => 'facebook']) }}" class="btn btn-primary m-2 btn-md radius-30">
                                    <i class="bi bi-facebook me-2"></i>Login with Facebook
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-script')
<script>
    $(document).ready(function() {
        var email = localStorage.getItem('rememberedEmail');
        if (email) {
            $('#inputEmailAddress').val(email);
            $('#flexSwitchCheckChecked').prop('checked', true);
        } else {
            $('#flexSwitchCheckChecked').prop('checked', false);
        }

        $('#flexSwitchCheckChecked').change(function() {
            if ($(this).is(':checked')) {
                localStorage.setItem('rememberedEmail', $('#inputEmailAddress').val());
            } else {
                localStorage.removeItem('rememberedEmail');
            }
        });

        $('#togglePassword').on('click', function() {
            const passwordField = $('#inputChoosePassword');
            const type = passwordField.attr('type') === 'password' ? 'text' : 'password';
            passwordField.attr('type', type);

            $(this).find('i').toggleClass('bi-eye').toggleClass('bi-eye-slash');
        });
    });
</script>
@endsection
