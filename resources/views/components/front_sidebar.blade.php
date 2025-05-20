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

            <li>
                <a href="{{ route('user.leads-management.all') }}">
                    <div class="parent-icon"><i class="bi bi-person-lines-fill"></i></div>
                    <div class="menu-title">Lead Management</div>
                </a>
            </li>

            <li>
                <a href="{{ route('user.package.index') }}">
                    <div class="parent-icon"><i class="bi bi-box"></i></div>
                    <div class="menu-title">Package</div>
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