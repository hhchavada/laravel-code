<nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
    id="layout-navbar">
    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
            <i class="ti ti-menu-2 ti-sm"></i>
        </a>
    </div>
    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
        @can('add-blog')
        <div class="btn-group" id="dropdown-icon-demo">
            <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown"
                aria-expanded="false">
                {{__('lang.admin_create_blog')}}
            </button>
            <ul class="dropdown-menu">
                <li>
                    <a href="{{url('admin/add-post/post')}}"
                        class="dropdown-item d-flex align-items-center">{{__('lang.admin_create_post')}}</a>
                </li>
                <li>
                    <a href="{{url('admin/add-post/quote')}}"
                        class="dropdown-item d-flex align-items-center">{{__('lang.admin_create_quote')}}</a>
                </li>
            </ul>
        </div>
        @endcan

        <ul class="navbar-nav flex-row align-items-center ms-auto">
            <li class="nav-item dropdown-language dropdown me-2 me-xl-0">
                <?php
                $langList = \Helpers::getAllLangList();

                if (Session()->has('admin_locale')) {
                    $langCode = Session()->get('admin_locale');
                } else {
                    $langCode = config('app.fallback_locale');
                }
                ?>
                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <i class="ti ti-language rounded-circle ti-md" aria-hidden="true"
                        style="margin-top: 9px;font-size:25px"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    @if(count($langList)>0)
                    @foreach($langList as $langRow)
                    <li>
                        <a class="dropdown-item" href="{!! url('/admin/setlang') !!}?lang={{$langRow->code}}"
                            data-language="{{$langRow->code}}">
                            <i class="ti ti-language rounded-circle ti-md" aria-hidden="true"></i>
                            <span class="align-middle">{{$langRow->name}}</span>
                        </a>
                    </li>
                    @endforeach
                    @endif
                </ul>
            </li>

            <!-- Notification -->
            @php
            $notificationsCount = \Helpers::getUnreadNotificationCount();
            @endphp
            <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-3 me-xl-1">
                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown"
                    data-bs-auto-close="outside" aria-expanded="false">
                    <i class="ti ti-bell ti-md"></i>
                    <span class="badge bg-danger rounded-pill badge-notifications">{{$notificationsCount}}</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end py-0">
                    <li class="dropdown-menu-header border-bottom">
                        <div class="dropdown-header d-flex align-items-center py-3">
                            <h5 class="text-body mb-0 me-auto">{{__('lang.admin_notification')}}</h5>
                            <a href="javascript:void(0)" class="dropdown-notifications-all text-body"
                                data-bs-toggle="tooltip" data-bs-placement="top"
                                title="{{__('lang.admin_notification_marked_as_read')}}"><i
                                    class="ti ti-mail-opened fs-4"></i></a>
                        </div>
                    </li>
                    @php
                    $notifications = \Helpers::getNotification();
                    @endphp
                    <li class="dropdown-notifications-list scrollable-container">
                        <ul class="list-group list-group-flush">
                            @foreach($notifications as $each)
                            <li class="list-group-item list-group-item-action dropdown-notifications-item">
                                <div class="d-flex">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="avatar">
                                            <img src="{{ url('uploads/user/'.Auth::user()->photo)}}" alt class="rounded-circle" onerror="this.onerror=null;this.src=`{{ asset('uploads/no-profile-image.png') }}`" />
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ $each->title }}</h6>
                                        <p class="mb-0">{{ $each->message }} #{{$each->blog_id}}</p>
                                        <small class="text-muted">{{ $each->created_at->diffForHumans() }}</small>
                                    </div>
                                    <div class="flex-shrink-0 dropdown-notifications-actions">
                                        @if($each->is_read == 0)
                                        <a href="javascript:void(0)" class="dropdown-notifications-read"><span
                                                class="badge badge-dot"></span></a>
                                        @endif
                                        <a data-id="{{$each->id}}" href="javascript:void(0)"
                                            class="dropdown-notifications-archive"><span class="ti ti-x"></span></a>
                                    </div>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </li>

                </ul>
            </li>
            <!--/ Notification -->

            <li class="nav-item">
                <a class="nav-link theme-switcher" href="javascript:void(0);" onclick="setTheme();">
                    @if(isset($_COOKIE['theme']))
                    @if($_COOKIE['theme']=='dark')
                    <i class="ti ti-md ti-sun icon-switch"></i>
                    @else
                    <i class="ti ti-md ti-moon-stars icon-switch"></i>
                    @endif
                    @else
                    <i class="ti ti-md ti-moon-stars icon-switch"></i>
                    @endif
                </a>
            </li>
            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <div class="avatar avatar-online">
                        <img src="{{ url('uploads/user/'.Auth::user()->photo)}}" alt class="rounded-circle"
                            onerror="this.onerror=null;this.src=`{{ asset('uploads/no-profile-image.png') }}`" />
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="{{url('/admin/profile')}}">
                            <i class="ti ti-user-check me-2 ti-sm"></i>
                            <span class="align-middle">{{__('lang.admin_my_profile')}}</span>
                        </a>
                    </li>
                    <li>
                        <div class="dropdown-divider"></div>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('logout') }}"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="ti ti-logout me-2 ti-sm"></i>
                            <span class="align-middle">{{__('lang.admin_logout')}}</span>
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
    <div class="navbar-search-wrapper search-input-wrapper d-none">
        <input type="text" class="form-control search-input container-xxl border-0" placeholder="Search..."
            aria-label="Search..." />
        <i class="ti ti-x ti-sm search-toggler cursor-pointer"></i>
    </div>
</nav>