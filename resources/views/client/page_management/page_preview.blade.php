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
                <h4 class="my-navbar-title">Page Preview</h4>
            </nav>
        </header>

        <!--end top header-->

        <main class="page-content">

            @if (isset($data->cover_image) && !empty($data->cover_image))
                <div class="row">
                    <div class="col-12">
                        <img src="{{ asset($data->cover_image) }}" width="100%" height="500" alt="">
                    </div>
                </div>
            @endif

            <h3 class="fw-bold mt-3">{{ $data->title }}</h3>

            @if (isset($data->description) && !empty($data->description))
                <p class="mb-2">{{ $data->description }}</p>
            @endif

            @if (isset($data->galleries) && !empty($data->galleries))
                <div class="row">
                    @foreach ($data->galleries as $item)

                        <div class="col-4">
                            <img src="{{ asset($item->images) }}" class="mb-2" width="470px" height="400" alt="">
                        </div>

                    @endforeach
                </div>
            @endif

            @if (isset($data->page_website_links) && !empty($data->page_website_links))
                <div class="row mt-3">
                    @foreach ($data->page_website_links as $item)
                        <div class="col-4 mt-3">
                            <div class="input-group flex-nowrap"> <span class="input-group-text mb-2" id="addon-wrapping"><i class="fa-solid fa-link"></i></span>
                                <input type="text" class="form-control mb-2" value="{{ $item->website_link }}" disabled readonly aria-describedby="addon-wrapping">
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            @if (isset($data->page_youtube_links) && !empty($data->page_youtube_links))
                <div class="row justify-content-center mt-3">
                    @foreach ($data->page_youtube_links as $item)
                        <div class="col-12 mt-3 text-center">
                            @php
                                $url = $item->youtube_link;
                                $parsed_url = parse_url($url);
                                parse_str($parsed_url['query'], $query_params);
                                $video_id = $query_params['v'];
                                $embed_url = "https://www.youtube.com/embed/" . $video_id;
                            @endphp
                            <iframe width="700" height="315" src="{{ $embed_url }}" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        </div>
                    @endforeach
                </div>
            @endif

            @if (isset($data->google_maps) && !empty($data->google_maps))
                <div class="row">
                    <div class="col-12 mt-3">
                        @php
                            $map = explode(',', $data->google_maps);
                            $latitude = $map[0];
                            $longitude = $map[1];
                        @endphp
                        <div id="map" style="height: 400px; width: 100%;"></div>
                    </div>
                </div>
            @endif

            <!--end page main-->
        </main>

        <!--start overlay-->
        <div class="overlay nav-toggle-icon"></div>
        <!--end overlay-->
        
        <!--start footer-->
        <footer class="footer">
            <div class="footer-text">
                <h6 class="text-dark">SHARED BY</h6>
                <img src="{{asset(check_file(auth('web')->user()->image ?? $data->user->image,'user'))}}" class="client-img mt-2" alt="">
                <h4 class="text-dark mt-3">{{ auth('web')->user()->client_name ?? $data->user->client_name }}</h4>
                <p class="text-dark">{{ auth('web')->user()->phone_number ?? $data->user->phone_number }}</p>
                <p class="text-dark">{{ auth('web')->user()->user_agency->name ?? $data->user->user_agency->name}}</p>

                <a type="button" href="tel:{{ auth('web')->user()->phone_number ?? $data->user->phone_number}}" class="btn btn-info px-5"><i class="fa-solid fa-phone-flip"></i> Call</a>
                <a type="button" href="sms://{{ auth('web')->user()->phone_number ?? $data->user->phone_number}}" class="btn btn-info px-5"><i class="fa-solid fa-comment-sms"></i> SMS</a>
                <a type="button" href="https://wa.me/{{ auth('web')->user()->phone_number ?? $data->user->phone_number }}" target="_blank" class="btn btn-info px-5"><i class="fa-brands fa-whatsapp"></i> WhatsApp</a>
            </div>
        </footer>
        <!--end footer-->

        <!--Start Back To Top Button-->
        <a href="javaScript:;" class="back-to-top"><i class='bx bxs-up-arrow-alt'></i></a>
        <!--End Back To Top Button-->

        <!--start switcher-->
        <!--end switcher-->
    </div>
    <!-- end wrapper -->



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
