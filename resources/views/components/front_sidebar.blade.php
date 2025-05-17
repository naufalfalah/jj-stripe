@php
    $clientId = auth('web')->user()->id;
    $userSubAccounts = App\Models\UserSubAccount::with('package.menus')
        ->where('client_id', $clientId)
        ->get();
    $accessibleMenus = $userSubAccounts
        ->flatMap(function ($userSubAccount) {
            return $userSubAccount->package->getMenus() ?? [];
        })
        ->unique()
        ->values()
        ->toArray();
@endphp

<!--start sidebar -->
<aside class="sidebar-wrapper" data-simplebar="true">
    <div class="sidebar-header">
        <div>
            <a href="{{ url('/') }}">
                <img src="{{ asset('front') }}/assets/images/logo.png" class="logo-icon" alt="logo icon">
            </a>
        </div>
    </div>

    <!--navigation-->
    <ul class="metismenu d-flex justify-content-between min-vh-90" id="menu">
        <div>
            <li>
                <a href="{{ route('user.dashboard') }}">
                    <div class="parent-icon"><i class="bi bi-house-fill"></i></div>
                    <div class="menu-title">Dashboard</div>
                </a>
            </li>
    
            @if (in_array('wallet', $accessibleMenus))
                <li id="wallet-sidebar">
                    <a href="javascript:void(0);" class="has-arrow" id="wallet-sidebar-toggle">
                        <div class="parent-icon"><i class="fa-solid fa-wallet"></i></div>
                        <div class="menu-title">Wallet</div>
                    </a>
                    <ul>
                        <li id="live-account-submenu">
                            <a href="{{ route('user.wallet.add') }}">
                                <i class="fa-solid fa-circle"></i>Live Account
                            </a>
                        </li>
                        <li id="ads-request-submenu">
                            <a href="{{ route('user.ads.add') }}">
                                <i class="fa-solid fa-circle"></i>Ads Request
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('user.ads.all') }}">
                                <i class="fa-solid fa-circle"></i>Ads Status Request
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('user.wallet.transaction_report') }}">
                                <i class="fa-solid fa-circle"></i>Transaction Reports
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('user.wallet.transfer_funds') }}">
                                <i class="fa-solid fa-circle"></i>Transfer Funds
                            </a>
                        </li>
                    </ul>
                </li>
            @endif
    
            @if (in_array('report', $accessibleMenus))
                <li>
                    <a href="{{ route('user.google-ads-report.google_ads_report') }}">
                        <div class="parent-icon"><i class="fa-brands fa-google"></i></div>
                        <div class="menu-title">Google Report</div>
                    </a>
                </li>
            @endif
            <li>
                    <a href="{{ route('user.leads-management.all') }}">
                        <div class="parent-icon"><i class="bi bi-person-lines-fill"></i></div>
                        <div class="menu-title">Lead Management</div>
                    </a>
                </li>
            @if (in_array('lead_management', $accessibleMenus))
                <li>
                    <a href="{{ route('user.leads-management.all') }}">
                        <div class="parent-icon"><i class="bi bi-person-lines-fill"></i></div>
                        <div class="menu-title">Lead Management</div>
                    </a>
                </li>
            @endif
    
            @if (in_array('message_template', $accessibleMenus))
                <li
                    class="{{ request()->routeIs(['user.message-template.all', 'user.message-template.temp_details']) ? 'mm-active' : '' }}">
                    <a href="{{ route('user.message-template.all') }}">
                        <div class="parent-icon"><i class="fadeIn animated bx bx-comment-detail"></i>
                        </div>
                        <div class="menu-title">Message Template</div>
                    </a>
                </li>
            @endif
    
            @if (in_array('email_template', $accessibleMenus))
                <li
                    class="{{ request()->routeIs(['user.email-template.all', 'user.email-template.temp_details']) ? 'mm-active' : '' }}">
                    <a href="{{ route('user.email-template.all') }}">
                        <div class="parent-icon"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                fill="currentColor" class="bi bi-envelope-at-fill" viewBox="0 0 16 16">
                                <path
                                    d="M2 2A2 2 0 0 0 .05 3.555L8 8.414l7.95-4.859A2 2 0 0 0 14 2zm-2 9.8V4.698l5.803 3.546zm6.761-2.97-6.57 4.026A2 2 0 0 0 2 14h6.256A4.5 4.5 0 0 1 8 12.5a4.49 4.49 0 0 1 1.606-3.446l-.367-.225L8 9.586zM16 9.671V4.697l-5.803 3.546.338.208A4.5 4.5 0 0 1 12.5 8c1.414 0 2.675.652 3.5 1.671" />
                                <path
                                    d="M15.834 12.244c0 1.168-.577 2.025-1.587 2.025-.503 0-1.002-.228-1.12-.648h-.043c-.118.416-.543.643-1.015.643-.77 0-1.259-.542-1.259-1.434v-.529c0-.844.481-1.4 1.26-1.4.585 0 .87.333.953.63h.03v-.568h.905v2.19c0 .272.18.42.411.42.315 0 .639-.415.639-1.39v-.118c0-1.277-.95-2.326-2.484-2.326h-.04c-1.582 0-2.64 1.067-2.64 2.724v.157c0 1.867 1.237 2.654 2.57 2.654h.045c.507 0 .935-.07 1.18-.18v.731c-.219.1-.643.175-1.237.175h-.044C10.438 16 9 14.82 9 12.646v-.214C9 10.36 10.421 9 12.485 9h.035c2.12 0 3.314 1.43 3.314 3.034zm-4.04.21v.227c0 .586.227.8.581.8.31 0 .564-.17.564-.743v-.367c0-.516-.275-.708-.572-.708-.346 0-.573.245-.573.791" />
                            </svg>
                        </div>
                        <div class="menu-title">Email Template</div>
                    </a>
                </li>
            @endif
    
            @if (in_array('whatsapp_template', $accessibleMenus))
                <li class="{{ request()->routeIs('user.whatsap_message_template.all') ? 'mm-active' : '' }}">
                    <a href="{{ route('user.whatsap_message_template.all') }}">
                        <div class="parent-icon"><i class="fa-brands fa-whatsapp"></i></div>
                        <div class="menu-title">WhatsApp Template</div>
                    </a>
                </li>
            @endif
    
            {{-- @if (in_array('file_manager', $accessibleMenus)) --}}
                <li
                    class="{{ request()->routeIs(['user.file_manager.view', 'user.file_manager.file_detail']) ? 'mm-active' : '' }}">
                    <a href="{{ route('user.file_manager.view') }}">
                        <div class="parent-icon"><i class="bi bi-folder-fill"></i></div>
                        <div class="menu-title">File Manager</div>
                    </a>
                </li>
            {{-- @endif --}}
    
            @if (in_array('google_calendar', $accessibleMenus))
                <li class="{{ request()->routeIs('user.google.index') ? 'mm-active' : '' }}">
                    <a href="{{ route('user.google.index') }}">
                        <div class="parent-icon"><i class="fa-brands fa-google"></i></div>
                        <div class="menu-title">Google Calender</div>
                    </a>
                </li>
            @endif

            <li>
                <a href="{{ route('user.ai-tts.index') }}">
                    <div class="parent-icon"><i class="bi bi-mic-fill"></i></div>
                    <div class="menu-title">Text to Speech</div>
                </a>
            </li>

            <li>
                <a href="{{ route('user.ai-chat.index') }}">
                    <div class="parent-icon"><i class="bi bi-chat-left-text-fill"></i></div>
                    <div class="menu-title">AI Chat</div>
                </a>
            </li>

            <li>
                <a href="{{ route('user.ai-voice-chat.index') }}">
                    <div class="parent-icon"><i class="bi bi-chat-left-text-fill"></i></div>
                    <div class="menu-title">AI Voice Chat</div>
                </a>
            </li>
        </div>

        <div class="d-flex justify-content-center">
            <img src="{{ asset('front') }}/assets/images/google.jpg" alt="logo" class="img-fluid w-75 rounded">
        </div>
    </ul>

    <!--end navigation-->
</aside>
<!--end sidebar -->