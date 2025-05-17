@php
    $notifications = App\Models\Notification::where('user_id',auth('web')->id())->where('user_type', 'user');
    $unread = App\Models\Notification::where('user_id',auth('web')->id())->where('user_type', 'user')->where('is_read',0)->count();
@endphp

<!--start top header-->
<header class="top-header">
    <nav class="navbar navbar-expand gap-3">
        <div class="mobile-toggle-icon fs-3">
            <i class="bi bi-list"></i>
        </div>
        <div class="top-navbar-right ms-auto">
            <ul class="navbar-nav align-items-center">
                <li class="nav-item dropdown dropdown-large">
                    <a class="nav-link dropdown-toggle dropdown-toggle-nocaret" href="#" data-bs-toggle="dropdown">
                        <div class="messages">
                            {{-- <span class="notify-badge">5</span> --}}
                            {{-- <i class="bi bi-chat-right-fill"></i> --}}
                        </div>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end p-0">
                        <div class="p-2 border-bottom m-2">
                            <h5 class="h5 mb-0">Messages</h5>
                        </div>
                        <div class="header-message-list p-2">
                            {{-- <a class="dropdown-item" href="#">
                                <div class="d-flex align-items-center">
                                    <img src="{{ asset('front') }}/assets/images/avatars/avatar-1.png" alt=""
                                        class="rounded-circle" width="50" height="50">
                                    <div class="ms-3 flex-grow-1">
                                        <h6 class="mb-0 dropdown-msg-user">Amelio Joly <span
                                                class="msg-time float-end text-secondary">1 m</span></h6>
                                        <small
                                            class="mb-0 dropdown-msg-text text-secondary d-flex align-items-center">The
                                            standard chunk of lorem...</small>
                                    </div>
                                </div>
                            </a>
                            <a class="dropdown-item" href="#">
                                <div class="d-flex align-items-center">
                                    <img src="{{ asset('front') }}/assets/images/avatars/avatar-2.png" alt=""
                                        class="rounded-circle" width="50" height="50">
                                    <div class="ms-3 flex-grow-1">
                                        <h6 class="mb-0 dropdown-msg-user">Althea Cabardo <span
                                                class="msg-time float-end text-secondary">7 m</span></h6>
                                        <small
                                            class="mb-0 dropdown-msg-text text-secondary d-flex align-items-center">Many
                                            desktop publishing</small>
                                    </div>
                                </div>
                            </a>
                            <a class="dropdown-item" href="#">
                                <div class="d-flex align-items-center">
                                    <img src="{{ asset('front') }}/assets/images/avatars/avatar-3.png" alt=""
                                        class="rounded-circle" width="50" height="50">
                                    <div class="ms-3 flex-grow-1">
                                        <h6 class="mb-0 dropdown-msg-user">Katherine Pechon <span
                                                class="msg-time float-end text-secondary">2 h</span></h6>
                                        <small
                                            class="mb-0 dropdown-msg-text text-secondary d-flex align-items-center">Making
                                            this the first true</small>
                                    </div>
                                </div>
                            </a>
                            <a class="dropdown-item" href="#">
                                <div class="d-flex align-items-center">
                                    <img src="{{ asset('front') }}/assets/images/avatars/avatar-4.png" alt=""
                                        class="rounded-circle" width="50" height="50">
                                    <div class="ms-3 flex-grow-1">
                                        <h6 class="mb-0 dropdown-msg-user">Peter Costanzo <span
                                                class="msg-time float-end text-secondary">3 h</span></h6>
                                        <small
                                            class="mb-0 dropdown-msg-text text-secondary d-flex align-items-center">It
                                            was popularised in the 1960</small>
                                    </div>
                                </div>
                            </a>
                            <a class="dropdown-item" href="#">
                                <div class="d-flex align-items-center">
                                    <img src="{{ asset('front') }}/assets/images/avatars/avatar-5.png" alt=""
                                        class="rounded-circle" width="50" height="50">
                                    <div class="ms-3 flex-grow-1">
                                        <h6 class="mb-0 dropdown-msg-user">Thomas Wheeler <span
                                                class="msg-time float-end text-secondary">1 d</span></h6>
                                        <small
                                            class="mb-0 dropdown-msg-text text-secondary d-flex align-items-center">If
                                            you are going to use a passage</small>
                                    </div>
                                </div>
                            </a>
                            <a class="dropdown-item" href="#">
                                <div class="d-flex align-items-center">
                                    <img src="{{ asset('front') }}/assets/images/avatars/avatar-6.png" alt=""
                                        class="rounded-circle" width="50" height="50">
                                    <div class="ms-3 flex-grow-1">
                                        <h6 class="mb-0 dropdown-msg-user">Johnny Seitz <span
                                                class="msg-time float-end text-secondary">2 w</span></h6>
                                        <small
                                            class="mb-0 dropdown-msg-text text-secondary d-flex align-items-center">All
                                            the Lorem Ipsum generators</small>
                                    </div>
                                </div>
                            </a>
                            <a class="dropdown-item" href="#">
                                <div class="d-flex align-items-center">
                                    <img src="{{ asset('front') }}/assets/images/avatars/avatar-1.png" alt=""
                                        class="rounded-circle" width="50" height="50">
                                    <div class="ms-3 flex-grow-1">
                                        <h6 class="mb-0 dropdown-msg-user">Amelio Joly <span
                                                class="msg-time float-end text-secondary">1 m</span></h6>
                                        <small
                                            class="mb-0 dropdown-msg-text text-secondary d-flex align-items-center">The
                                            standard chunk of lorem...</small>
                                    </div>
                                </div>
                            </a>
                            <a class="dropdown-item" href="#">
                                <div class="d-flex align-items-center">
                                    <img src="{{ asset('front') }}/assets/images/avatars/avatar-2.png" alt=""
                                        class="rounded-circle" width="50" height="50">
                                    <div class="ms-3 flex-grow-1">
                                        <h6 class="mb-0 dropdown-msg-user">Althea Cabardo <span
                                                class="msg-time float-end text-secondary">7 m</span></h6>
                                        <small
                                            class="mb-0 dropdown-msg-text text-secondary d-flex align-items-center">Many
                                            desktop publishing</small>
                                    </div>
                                </div>
                            </a>
                            <a class="dropdown-item" href="#">
                                <div class="d-flex align-items-center">
                                    <img src="{{ asset('front') }}/assets/images/avatars/avatar-3.png" alt=""
                                        class="rounded-circle" width="50" height="50">
                                    <div class="ms-3 flex-grow-1">
                                        <h6 class="mb-0 dropdown-msg-user">Katherine Pechon <span
                                                class="msg-time float-end text-secondary">2 h</span></h6>
                                        <small
                                            class="mb-0 dropdown-msg-text text-secondary d-flex align-items-center">Making
                                            this the first true</small>
                                    </div>
                                </div>
                            </a> --}}
                        </div>
                        <div class="p-2">
                            {{-- <div>
                                <hr class="dropdown-divider">
                            </div>
                            <a class="dropdown-item" href="#">
                                <div class="text-center">View All Messages</div>
                            </a> --}}
                        </div>
                    </div>
                </li>
                <li class="nav-item dropdown dropdown-large">
                    <a class="nav-link dropdown-toggle dropdown-toggle-nocaret" href="#" data-bs-toggle="dropdown" id="notify_ancher">
                        <div class="notifications">
                            @if($unread > 0)
                                <span class="notify-badge" id="notification_count_badge">{{ $unread }}</span>
                            @else
                                <span class="notify-badge d-none" id="notification_count_badge">{{ $unread }}</span>
                            @endif
                             <i class="bi bi-bell-fill"></i> 
                        </div>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end p-0">
                        <div class="header-notifications-list p-2">
                            @forelse ($notifications->latest()->limit(100)->get() as $val)
                            <a class="dropdown-item" href="{{ $val->lead_url ?? 'javascript:void(0);' }}">
                                <div class="d-flex align-items-center">
                                    <div class="ms-3 flex-grow-1">
                                        <h6 class="mb-0 dropdown-msg-user">{{ $val->title }}
                                            @if (isset($val->lead))
                                                {{ $val?->lead?->name }}
                                            @endif
                                            <span class="msg-time float-end text-secondary">{{ $val->created_at->diffForHumans() }}</span></h6>
                                        <small
                                            class="mb-0 dropdown-msg-text text-secondary d-flex align-items-center" style="max-width: 60ch; overflow: hidden; text-overflow: ellipsis; white-space: normal; ">{{ $val->body }}</small>
                                    </div>
                                </div>
                            </a>
                            @empty
                            <a class="dropdown-item" href="javascript:void(0);">
                                <div class="text-center">
                                    <h5 class="mb-0">No Notifiaction Found</h5>
                                </div>
                            </a>
                            @endforelse
                        </div>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('user.client_tour.restart') }}">
                        <i class="bi bi-info-circle-fill">&nbsp;<small>Restart Tour</small></i>
                    </a>
                </li>
            </ul>
        </div>
        <div class="dropdown dropdown-user-setting">
            <a class="dropdown-toggle dropdown-toggle-nocaret" href="#" data-bs-toggle="dropdown">
                <div class="user-setting d-flex align-items-center gap-3">
                    <img src="{{asset(check_file(auth('web')->user()->image,'user'))}}" class="user-img"
                        alt="user avatar">
                    <div class="d-none d-sm-block">
                        <p class="user-name mb-0">{{auth('web')->user()->client_name}}</p>
                    </div>

                </div>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <a class="dropdown-item" href="{{route('user.profile.edit')}}">
                        <div class="d-flex align-items-center">
                            <div class=""><i class="bi bi-person-fill"></i></div>
                            <div class="ms-3"><span>Profile</span></div>
                        </div>
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ route('user.client_tour.restart') }}">
                        <div class="d-flex align-items-center">
                            <div class=""><i class="bi bi-info-circle-fill"></i></div>
                            <div class="ms-3"><span>Restart Tour</span></div>
                        </div>
                    </a>
                </li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li>
                    <a class="dropdown-item" href="{{ route('auth.logout') }}" onclick="logout(event)">
                        <div class="d-flex align-items-center">
                            <div class=""><i class="bi bi-lock-fill"></i></div>
                            <div class="ms-3"><span>Logout</span></div>
                        </div>
                    </a>
                </li>
            </ul>
        </div>
    </nav>
</header>
<!--end top header-->
