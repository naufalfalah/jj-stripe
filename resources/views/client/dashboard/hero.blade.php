@push('styles')
    <style>
        /* Channel section */
        .channel-buttons .btn {
            border-radius: 10px;
            margin: 5px;
            background-color: white;
            border: 2px solid #dedbdb;
        }
        .channel-button.active {
            border: 2px solid #010101;
        }
        .back-button {
            border: 0px;
            background-color: transparent;
        }

        /* Hero section */
        .hero-section {
            background-color: #111;
            color: white;
            padding: 0px;
            border-radius: 15px;
            background-image: url('front/assets/images/dashboard/world-map.png'); 
            background-size: contain, cover;
            background-position: right;
            background-repeat: no-repeat;
        }
        .hero-card {
            padding: 0px;
        }
        .hero-content {
            padding: 60px;
        }
        .hero-features {
            display: flex;
            gap: 20px;
        }
        .hero-features .feature {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.9rem;
        }
        .app-buttons img {
            height: 50px;
            margin-right: 20px;
        }
        .hero-right-column {
            /* position: relative; */
            display: flex;
            justify-content: center;
            align-items: end;
        }
        .app-mockup {
            max-width: 90%;
            margin: 0px;
            margin-top: -10%;
        }
        .check {
            width: 24px;
        }
        .feature span {
            color: #BBB0B0;
            font-size: 12px
        }
        @media screen and (max-width: 767px) {
            .back-button {
                display: none;
            }
            .channel-buttons {
                flex-direction: column;
                gap: 10px;
            }
            
            /* Hero section adjustments */
            
            .hero-content {
                max-width: 100%;
            }
            
            .hero-features {
                flex-direction: column;
            }
            
            .app-buttons {
                flex-direction: row;
                align-items: center;
                gap: 5px;
            }
            .app-buttons img {
                height: auto;
                max-width: 30%;
                margin-right: 10px;
            }
        }
    </style>
@endpush

<div id="channel" class="mb-3">
    <b class="text-uppercase">Select Channel</b>
    <div class="row">
        <div class="col-lg-8">
            <div class="channel-buttons d-flex mt-2">
                <button class="back-button fs-3"><i class="bi bi-chevron-compact-left"></i></button>
                <button class="channel-button active btn btn-light p-3 shadow-sm"><img src="{{ asset('front/assets/images/dashboard/google-ads.png') }}" alt="google-ads-btn" width="120px"></button>
                <button class="channel-button btn btn-light p-3 shadow-sm"><img src="{{ asset('front/assets/images/dashboard/facebook.png') }}" alt="facebook-btn" width="120px"></button>
                <button class="channel-button btn btn-light p-3 shadow-sm"><img src="{{ asset('front/assets/images/dashboard/project.png') }}" alt="project-btn" width="120px"></button>
                <button class="channel-button btn btn-light p-3 shadow-sm"><img src="{{ asset('front/assets/images/dashboard/valuation.png') }}" alt="valuation-btn" width="120px"></button>
            </div>
        </div>
    </div>
</div>
        
<div id="hero">
    <div class="card hero-section world-map">
        <div class="card-body hero-card">
            <div class="row">
                <div class="col-lg-8 hero-content mb-5">
                    <h1 class="fs-1">Manage Leads efficiently using Our DataPoco Mobile App</h1>
    
                    <div class="hero-features mt-5 mb-5">
                        <div class="feature">
                            <img src="{{ asset('front/assets/images/dashboard/check.png') }}" alt="check" class="check">
                            <span>Easy Monitoring</span>
                        </div>
                        <div class="feature">
                            <img src="{{ asset('front/assets/images/dashboard/check.png') }}" alt="check" class="check">
                            <span>24/7 Support</span>
                        </div>
                        <div class="feature">
                            <img src="{{ asset('front/assets/images/dashboard/check.png') }}" alt="check" class="check">
                            <span>Multiple payment methods</span>
                        </div>
                    </div>
    
                    <div class="app-buttons d-flex mt-4">
                        <img src="{{ asset('front/assets/images/dashboard/app-store.png') }}" alt="App Store">
                        <img src="{{ asset('front/assets/images/dashboard/google-play.png') }}" alt="Google Play Store">
                    </div>
                </div>
    
                <div class="col hero-right-column">
                    <img src="{{ asset('front/assets/images/dashboard/app-2.png') }}" alt="App Mockup" class="app-mockup img-fluid">
                </div>
            </div>
        </div>
    </div>
</div>