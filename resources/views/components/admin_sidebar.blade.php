@php
    $round_robin = App\Models\SubAccount::WhereNotNull('sub_account_url')->get();
    $sub_account_id = session()->get('sub_account_id') ?? '';
    if ($round_robin->isEmpty()) {
        Session()->forget('sub_account_id');
        $sub_account_id = '';
    }
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
    <ul class="metismenu" id="menu">
        @if ($auth_user->role_name !== 'ISA Team')
            @if ($round_robin->count() > 0)
                <li>
                    <a href="javascript:void();" href="{{ route('admin.home') }}" class="has-arrow">
                        <div class="parent-icon"><i class="fa-solid fa-users"></i></i>
                        </div>
                        <div class="menu-title">Sub Account</div>
                    </a>
                    <ul>
                        @foreach ($round_robin as $item)
                        <li> <a href="{{ route('admin.sub_account.advertisements.running_ads', ['sub_account_id' => $item->hashid ]) }}"
                                class="d-flex align-items-start"><i
                                    class="fa-solid fa-circle mt-1"></i>{{$item->sub_account_name }}</a></li>
                        @endforeach
                    </ul>

                </li>
            @else
                <li class="{{ request()->routeIs('admin.home') ? 'mm-active' : '' }}">
                    <a href="{{ route('admin.home') }}">
                        <div class="parent-icon"><i class="fa-solid fa-users"></i></i>
                        </div>
                        <div class="menu-title">Sub Account</div>
                    </a>
                </li>
            @endif
        @endif

        @if ($auth_user->can('lead-frequency-read'))
            @if (isset($sub_account_id) && !empty($sub_account_id))
                <!-- <li
                    class="{{ request()->routeIs('admin.sub_account.advertisements.running_ads', ['sub_account_id' => $sub_account_id]) ? 'mm-active' : '' }}">
                    <a href="{{ route('admin.sub_account.advertisements.running_ads', ['sub_account_id' => $sub_account_id]) }}">
                        <div class="parent-icon"><i class="fa-solid fa-people-group"></i></i>
                        </div>
                        <div class="menu-title">Lead Frequency</div>
                    </a>
                </li> -->
            @endif
        @endif

        @if ($auth_user->user_type == 'admin' && $auth_user->role_name == 'super_admin')
            @if (isset($sub_account_id) && !empty($sub_account_id))
                <li>
                    <a href="javascript:voud(0);" class="has-arrow">
                        <div class="parent-icon"><i class="fa-solid fa-users"></i>
                        </div>
                        <div class="menu-title">Client Management</div>
                    </a>
                    <ul>
                        {{-- <li>
                            <a
                                href="{{ route('admin.sub_account.client-management.add', ['sub_account_id' => $sub_account_id ]) }}"><i
                                    class="fa-solid fa-circle"></i>
                                Add Client</a>
                        </li> --}}
                        {{-- <li>
                            <a
                                href="{{ route('admin.sub_account.client-management.all', ['sub_account_id' => $sub_account_id ]) }}"><i
                                    class="fa-solid fa-circle"></i>
                                All Client</a>
                        </li> --}}
                        {{-- <li>
                            <a
                                href="{{ route('admin.sub_account.client-management.all_clients', ['sub_account_id' => $sub_account_id ]) }}"><i
                                    class="fa-solid fa-circle"></i>
                                Import Client</a>
                        </li> --}}
                        {{-- <li>
                            <a
                                href="{{ route('admin.sub_account.client-management.top_up', ['sub_account_id' => $sub_account_id ]) }}"><i
                                    class="fa-solid fa-circle"></i>TopUp</a>
                        </li> --}}

                        @if ($auth_user->can('client-update'))
                            <li>
                                <a
                                    href="{{ route('admin.sub_account.client-management.edit', ['sub_account_id' => $sub_account_id]) }}">
                                    <i class="fa-solid fa-circle"></i>Edit Client
                                </a>
                            </li>
                        @endif

                        @if ($auth_user->can('google-ad-read'))
                            <li>
                                <a
                                    href="{{ route('admin.sub_account.client-management.google_ads.create', ['sub_account_id' => $sub_account_id]) }}">
                                    <i class="fa-solid fa-circle"></i>Create Google Ads
                                </a>
                            </li>
                        @endif

                        @if ($auth_user->can('campaigns-read'))
                            <li>
                                <a
                                    href="{{ route('admin.sub_account.client-management.google_ads_campaign', ['sub_account_id' => $sub_account_id]) }}">
                                    <i class="fa-solid fa-circle"></i>All Campaigns
                                </a>
                            </li>
                        @endif

                        @if ($auth_user->can('google-ad-group-read'))
                            <li>
                                <a
                                    href="{{ route('admin.sub_account.client-management.google_ads_ad_group', ['sub_account_id' => $sub_account_id]) }}">
                                    <i class="fa-solid fa-circle"></i>All Ad Groups
                                </a>
                            </li>

                            <li>
                                <a
                                    href="{{ route('admin.sub_account.client-management.google_ads_ad_group_ad', ['sub_account_id' => $sub_account_id]) }}">
                                    <i class="fa-solid fa-circle"></i>All Ads
                                </a>
                            </li>


                            <li>
                                <a
                                    href="{{ route('admin.sub_account.client-management.google_ads_conversion_action', ['sub_account_id' => $sub_account_id]) }}">
                                    <i class="fa-solid fa-circle"></i>All Conversion Goals
                                </a>
                            </li>

                            <li>
                                <a
                                    href="{{ route('admin.sub_account.client-management.google_ads_conversion_action.create', ['sub_account_id' => $sub_account_id]) }}">
                                    <i class="fa-solid fa-circle"></i>Create Conversion Goal
                                </a>
                            </li>
                        @endif


                        @if ($auth_user->can('google-ad-report-read'))
                            <li>
                                <a
                                    href="{{ route('admin.sub_account.google-ads-report.google_ads_report', ['sub_account_id' => $sub_account_id]) }}">
                                    <i class="fa-brands fa-google"></i>Google Ads Report
                                </a>
                            </li>
                        @endif

                    </ul>
                </li>
            @endif
        @endif

        @if ($auth_user->can('user-management-read'))
            <li>
                <a href="javascript:;" class="has-arrow">
                    <div class="parent-icon"><i class="fa-solid fa-user"></i>
                    </div>
                    <div class="menu-title">User Management</div>
                </a>
                <ul>
                    @if ($auth_user->can('user-management-write'))
                        <li> <a href="{{ route('admin.user-management.add-user') }}"><i
                                    class="fa-solid fa-circle"></i>Add
                                Users</a></li>
                    @endif
                    <li> <a href="{{ route('admin.user-management.view') }}"><i class="fa-solid fa-circle"></i>All
                            Users</a></li>
                </ul>
            </li>
        @endif

        @if ($auth_user->user_type == 'admin' && $auth_user->role_name == 'super_admin')
            <li>
                <a href="javascript:;" class="has-arrow">
                    <div class="parent-icon"><i class="fa-solid fa-drum-steelpan"></i>
                    </div>
                    <div class="menu-title">Role Management</div>
                </a>
                <ul>
                    <li> <a href="{{ route('admin.permission_type.permission_type') }}"><i
                                class="fa-solid fa-circle"></i>Permission Type</a></li>
                    <li> <a href="{{ route('admin.permission.permission') }}"><i
                                class="fa-solid fa-circle"></i>Permission</a></li>
                    <li> <a href="{{ route('admin.role.all') }}"><i class="fa-solid fa-circle"></i>Role</a></li>
                </ul>
            </li>
        @endif

        {{-- @if ($auth_user->user_type == 'admin' && $auth_user->role_name == 'super_admin') --}}
        @if ($auth_user->can('settings-read'))
            <li>
                <a href="javascript:voud(0);" class="has-arrow">
                    <div class="parent-icon"><i class="fa-solid fa-gears"></i>
                    </div>
                    <div class="menu-title">Settings</div>
                </a>
                <ul>
                    <li> <a href="{{ route('admin.setting.google_account') }}"><i class="fa-solid fa-circle"></i>Google
                            Account</a></li>
                    <li> <a href="{{ route('admin.setting.taxes') }}"><i class="fa-solid fa-circle"></i>Taxes & Vat
                            Charges / Topup Setting</a></li>
                    <li> <a href="{{ route('admin.setting.whatsapp_temp') }}"><i
                                class="fa-solid fa-circle"></i>Whatsapp
                            Message Template</a></li>
                </ul>
            </li>
        @endif

        {{-- @endif --}}

        @if ($auth_user->can('agencies-read'))
            <li>
                <a href="{{ route('admin.package.index') }}">
                    <div class="parent-icon"><i class="fa-solid fa-cube"></i></div>
                    <div class="menu-title">Packages</div>
                </a>
            </li>

            <li>
                <a href="{{ route('admin.notification.index') }}">
                    <div class="parent-icon"><i class="fa-solid fa-bell"></i></div>
                    <div class="menu-title">Notification</div>
                </a>
            </li>

            <li>
                <a href="{{ route('admin.lead-management.client_leads') }}">
                    <div class="parent-icon"><i class="bi bi-person-lines-fill"></i></div>
                    <div class="menu-title">Lead Management</div>
                </a>
            </li>

            <li>
                <a href="{{ route('admin.file_manager.view') }}">
                    <div class="parent-icon"><i class="bi bi-folder-fill"></i></div>
                    <div class="menu-title">File Manager</div>
                </a>
            </li>

            <li
                class="{{ request()->routeIs(['admin.message-template.all', 'admin.message-template.temp_details']) ? 'mm-active' : '' }}">
                <a href="{{ route('admin.message-template.all') }}">
                    <div class="parent-icon"><i class="fadeIn animated bx bx-comment-detail"></i>
                    </div>
                    <div class="menu-title">Message Template</div>
                </a>
            </li>

            <li
                class="{{ request()->routeIs(['admin.email-template.all', 'admin.email-template.temp_details']) ? 'mm-active' : '' }}">
                <a href="{{ route('admin.email-template.all') }}">
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

            <li class="{{ request()->routeIs('admin.google.index') ? 'mm-active' : '' }}">
                <a href="{{ route('admin.google.index') }}">
                    <div class="parent-icon"><i class="bi bi-google"></i>
                    </div>
                    <div class="menu-title">Google Calender</div>
                </a>
            </li>
        @endif

        @if ($auth_user->can('package-read'))
            <li>
                <a href="{{ route('admin.package.index') }}">
                    <div class="parent-icon"><i class="fa-solid fa-cube"></i></div>
                    <div class="menu-title">Packages</div>
                </a>
            </li>
        @endif


        @if ($auth_user->can('send-notification-read'))
            <li>
                <a href="{{ route('admin.notification.index') }}">
                    <div class="parent-icon"><i class="fa-solid fa-bell"></i></div>
                    <div class="menu-title">Notification</div>
                </a>
            </li>
        @endif


        @if ($auth_user->can('lead-management-read'))
            <li>
                <a href="{{ route('admin.lead-management.client_leads') }}">
                    <div class="parent-icon"><i class="bi bi-person-lines-fill"></i></div>
                    <div class="menu-title">Lead Management</div>
                </a>
            </li>
        @endif

        @if ($auth_user->can('file-manager-read'))
            <li>
                <a href="{{ route('admin.file_manager.view') }}">
                    <div class="parent-icon"><i class="bi bi-folder-fill"></i></div>
                    <div class="menu-title">File Manager</div>
                </a>
            </li>
        @endif

        @if ($auth_user->can('message-template-read'))
            <li
                class="{{ request()->routeIs(['admin.message-template.all', 'admin.message-template.temp_details']) ? 'mm-active' : '' }}">
                <a href="{{ route('admin.message-template.all') }}">
                    <div class="parent-icon"><i class="fadeIn animated bx bx-comment-detail"></i>
                    </div>
                    <div class="menu-title">Message Template</div>
                </a>
            </li>
        @endif

        @if ($auth_user->can('email-template-read'))
            <li
                class="{{ request()->routeIs(['admin.email-template.all', 'admin.email-template.temp_details']) ? 'mm-active' : '' }}">
                <a href="{{ route('admin.email-template.all') }}">
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

        @if ($auth_user->can('calendar-management-read'))
            <li class="{{ request()->routeIs('admin.google.index') ? 'mm-active' : '' }}">
                <a href="{{ route('admin.google.index') }}">
                    <div class="parent-icon"><i class="bi bi-google"></i>
                    </div>
                    <div class="menu-title">Google Calender</div>
                </a>
            </li>
        @endif
        
        @if ($auth_user->can('script-read'))
            <li class="{{ request()->routeIs(['admin.scripts.index']) ? 'mm-active' : '' }}">
                <a href="{{ route('admin.scripts.index') }}">
                    <div class="parent-icon"><i class="fa-solid fa-file"></i></i>
                    </div>
                    <div class="menu-title">Script</div>
                </a>
            </li>
        @endif


        @if ($auth_user->user_type == 'admin' && $auth_user->role_name == 'super_admin')
            <li>

                <a href="javascript:voud(0);" class="has-arrow">
                    <div class="parent-icon"><i class="fa-solid fa-gears"></i>
                    </div>
                    <div class="menu-title">Graphic Task</div>
                </a>
                <ul>
                    @if ($auth_user->can('designer-read'))
                        <li> <a href="{{ route('admin.assign_task.create_design') }}"><i
                                    class="fa-solid fa-circle"></i>Create Designer
                            </a></li>
                    @endif

                    @if ($auth_user->can('designer-read'))
                        <li> <a href="{{ route('admin.assign_task.upload_images') }}"><i
                                    class="fa-solid fa-circle"></i>
                                Upload Images</a></li>
                    @endif

                </ul>
            </li>
        @endif

    </ul>
    <!--end navigation-->
</aside>
<!--end sidebar -->
