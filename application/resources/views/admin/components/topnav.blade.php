<nav class="navbar-wrapper">
    <div class="navbar__left">
        <button type="button" class="res-sidebar-open-btn me-3"><i class="fa-solid fa-bars"></i></button>

        <div class="header-favicon--wrap">
            <img src="{{ getImage(getFilePath('logoIcon') . '/favicon.png') }}" alt="...">
        </div>

        <form class="navbar-search">
            <input type="search" name="#0" class="navbar-search-field" id="searchInput" autocomplete="off"
                placeholder="@lang('Search Options...')">
            <i class="fa-solid fa-magnifying-glass"></i>
            <ul class="search-list"></ul>
        </form>
    </div>
    <div class="navbar__right">
        <ul class="navbar__action-list">
            <li><span class="navbar-search-responsive-btn"><i class="fa-solid fa-magnifying-glass"></i></span></li>

            <li>
                <a title="@lang('Visit Site')" href="{{ route('home') }}" target="_blank" class="btn btn-sm"><i class="fas fa-globe-americas"></i></a>
            </li>

            <li>
                <a title="@lang('Clear Cache')" href="{{ route('admin.clear.cache') }}" class="btn btn-sm"><i class="menu-icon fa-solid fa-broom"></i></a>
            </li>

            @adminHas('admin-notification')
            <li class="dropdown">
                <button type="button" class="primary--layer" data-bs-toggle="dropdown" data-display="static"
                    aria-haspopup="true" aria-expanded="false">
                    <i class="far fa-bell"></i>
                    @if ($adminNotificationCount > 0)
                        <div class="new-not white"></div>
                    @endif
                </button>
                <div class="dropdown-menu dropdown-menu--md p-0 border-0 box--shadow1 dropdown-menu-right">
                    <div class="dropdown-menu__header">
                        <span class="caption">@lang('Notification')</span>
                        @if ($adminNotificationCount > 0)
                            <p>@lang('You have') {{ $adminNotificationCount }} @lang('unread notification')</p>
                        @else
                            <p>@lang('No unread notification found')</p>
                        @endif
                    </div>
                    <div class="dropdown-menu__body">
                        @foreach ($adminNotifications as $notification)
                            <a href="{{ route('admin.notification.read', $notification->id) }}"
                                class="dropdown-menu__item">
                                <div class="navbar-notifi">
                                    <div class="navbar-notifi__left bg--green b-radius--rounded">
                                        @if(!empty($notification->user->image))
                                        <img src="{{ getImage(getFilePath('userProfile') . '/' . $notification->user?->image ?? '', getFileSize('userProfile')) }}" alt="@lang('Profile Image')">
                                        @else
                                        <img src="{{ getImage('assets/images/general/default.png') }}" alt="@lang('Profile Image')">
                                        @endif
                                    </div>
                                    <div class="navbar-notifi__right">
                                        <h6 class="notifi__title">{{ __($notification->title) }}</h6>
                                        <span class="time"><i class="far fa-clock"></i>
                                            {{ $notification->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                    <div class="dropdown-menu__footer">
                        <a href="{{ route('admin.notifications') }}" class="view-all-message">
                            @lang('View all notification')
                        </a>
                    </div>
                </div>
            </li>
            @endadminHas


            <li class="dropdown">
                <button type="button" class="" data-bs-toggle="dropdown" data-display="static"
                    aria-haspopup="true" aria-expanded="false">
                    <span class="navbar-user">
                        <span class="navbar-user__thumb">
                            <img src="{{ getImage('assets/admin/images/profile/' . auth()->guard('admin')->user()->image) }}" alt="image">
                            </span>
                        <span class="navbar-user__info">
                            <span class="navbar-user__name">{{ auth()->guard('admin')->user()->username }}</span>
                        </span>
                        <span class="icon"><i class="fa-solid fa-angle-down"></i></span>
                    </span>
                </button>
                <div class="dropdown-menu dropdown-menu--sm p-0 border-0 box--shadow1 dropdown-menu-right">
                    <a href="{{ route('admin.profile') }}"
                        class="dropdown-menu__item d-flex align-items-center px-3 py-2">
                        <i class="dropdown-menu__icon las la-user"></i>
                        <span class="dropdown-menu__caption">@lang('Profile')</span>
                    </a>
                    <a href="{{ route('admin.logout') }}" class="dropdown-menu__item d-flex align-items-center px-3 py-2">
                        <i class="dropdown-menu__icon las la-chevron-circle-right"></i>
                        <span class="dropdown-menu__caption">@lang('Logout')</span>
                    </a>
                </div>
            </li>
        </ul>
    </div>
</nav>
<!-- navbar-wrapper end -->
