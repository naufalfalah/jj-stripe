<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />


    <title>{{ @$title }}</title>
    <link rel="icon" href="{{ asset('front') }}/assets/images/favicon.png" type="image/png" />
    <!--plugins-->
    <link href="{{ asset('front') }}/assets/plugins/simplebar/css/simplebar.css" rel="stylesheet" />
    <link href="{{ asset('front') }}/assets/plugins/perfect-scrollbar/css/perfect-scrollbar.css" rel="stylesheet" />
    <link href="{{ asset('front') }}/assets/plugins/metismenu/css/metisMenu.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <!-- Bootstrap CSS -->
    <link href="{{ asset('front') }}/assets/css/bootstrap.min.css" rel="stylesheet" />
    <link href="{{ asset('front') }}/assets/css/bootstrap-extended.css" rel="stylesheet" />
    <link href="{{ asset('front') }}/assets/css/style.css" rel="stylesheet" />
    <link href="{{ asset('front') }}/assets/css/icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">

    <style>
        .my-top-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background-color: #f7f8fa;
            border-bottom: 1px solid #e2e3e4;
            background-clip: padding-box;
            height: 60px;
            z-index: 10;
            padding: 0 1.5rem;
            transition: all .2s;
        }

        .my-navbar {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
        }

        .my-navbar-title {
            margin: 0;
            margin-top: 10px;
            text-align: center;
        }

        .client-img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
        }

        .page-content {
            margin-left: 0px !important;
            margin-top: 10px !important;
            padding: 1.5rem 1.5rem 2.5rem 1.5rem;
            transition: all .2s;
        }

        .canvas-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
            width: 100%;
        }

        .image-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
            width: 100%;
        }

        /* .doc-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
            width: 100%;
        } */

        .footer {
            position: relative;
            left: 0;
            right: 0;
            bottom: 0;
            padding: 0.7rem;
            color: #484444;
            background-color: #f7f8fa;
            border-top: 1px solid #e2e3e4;
            text-align: center;
            transition: .3s all;
            z-index: 10 !important;
        }

        a.back-to-top {
            z-index: 20 !important;
        }
    </style>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/shepherd.js@10.0.1/dist/css/shepherd.css" />
    @yield('page-css')
    @stack('styles')
</head>

<body>
    <div id="eq-loader">
        <div class="eq-loader-div">
            <div class="eq-loading dual-loader mx-auto mb-5"></div>
        </div>
    </div>

    <!-- wrapper -->
    <div class="wrapper">
        <!--start top header-->

        <header class="my-top-header">
            <nav class="my-navbar">
                <h4 class="my-navbar-title">File Preview</h4>
            </nav>
        </header>

        <!--end top header-->

        <main class="page-content">
            <h3 class="fw-bold mt-3">{{ $data->name }}</h3>
            @if (isset($data->pdf) && !empty($data->pdf))
                <div class="row">
                    <div class="col-12">
                        @php
                            $fileExtension = pathinfo($data->pdf, PATHINFO_EXTENSION);
                        @endphp

                        @if(in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif']))
                            <!-- Display image -->
                            <img src="{{ asset($data->pdf) }}" width="50%" height="500" alt="Image">
                        @elseif($fileExtension == 'pdf')
                            <!-- Display PDF -->
                            <iframe src="{{ asset($data->pdf) }}" width="100%" height="500"></iframe>
                        @elseif(in_array($fileExtension, ['doc', 'docx', 'xlsx', 'ppt', 'pptx']))
                            <!-- Provide a download link for document files -->
                            <a href="{{ asset($data->pdf) }}" target="_blank">Download Document</a>
                        @else
                            <!-- For other files, just provide a download link -->
                            <a href="{{ asset($data->pdf) }}" target="_blank">Download File</a>
                        @endif
                    </div>
                </div>

            @endif

            <!--end page main-->
        </main>

        <!--start overlay-->
        <div class="overlay nav-toggle-icon"></div>
        <!--end overlay-->

       
        <footer class="footer">
            <div class="footer-text">
                <h6 class="text-dark">SHARED BY</h6>
                <img src="{{asset('front/assets/images/logo.png')  }}" class="client-img mt-2" alt="" style="width: 188px;">
                {{-- <h4 class="text-dark mt-3">{{ config('app.name') }}</h4> --}}
               
            </div>
        </footer>
        
        <a href="javaScript:;" class="back-to-top"><i class='bx bxs-up-arrow-alt'></i></a>
       
    </div>



    <!-- Bootstrap bundle JS -->
    <script src="{{ asset('front') }}/assets/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('front') }}/assets/js/jquery.min.js"></script>
    <script src="{{ asset('front') }}/assets/plugins/simplebar/js/simplebar.min.js"></script>
    <script src="{{ asset('front') }}/assets/plugins/metismenu/js/metisMenu.min.js"></script>
    <script src="{{ asset('front') }}/assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDSnI-NKLzHfh9ksrC9-r0LHuiV4IP-FhE&callback=initMap" async defer></script>

    <!--app-->
    <script src="{{ asset('front') }}/assets/js/app.js"></script>
    <script>
        function initMap() {
            var location = { lat: parseFloat("{{ $latitude ?? "0" }}"), lng: parseFloat("{{ $longitude ?? "0" }}") };
            var map = new google.maps.Map(document.getElementById('map'), {
                zoom: 15,
                center: location
            });
            var marker = new google.maps.Marker({
                position: location,
                map: map
            });
        }
    </script>

</body>

</html>
