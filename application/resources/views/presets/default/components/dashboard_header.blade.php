@php
    $user = auth()->user();
    $userNotifications = App\Models\UserNotification::where('is_read', Status::NO)->where('user_id', $user->id)->orderBy('id', 'desc')->take(10)->get();

    $userNotificationCount = $userNotifications->count();
@endphp


<div class="dashboard__header">
    <div class="current__balance">
        <h4>@lang('Image Credit') <span>{{ showAmount(auth()->user()->image_credit,2) }}</span></h4>
    </div>
    <div class="dashboard__header__widgets">
        <a href="{{ route('home') }}" class="ds__home"><i class="fa-solid fa-house"></i></a>
        <div class="dropdown">
            <button type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <span class="notification__btn">
                    <i class="las la-bell"></i>
                    <span class="notification__count">
                        <span>{{ $userNotificationCount > 0 ? $userNotificationCount : '0' }}</span>
                    </span>
                </span>
            </button>
            <div class="dropdown-menu">
                <div class="notification__wrap">
                    <div class="notification__header">
                        <h4>@lang('Notification')</h4>
                        <span class="badge badge--base">{{ $userNotificationCount }} @lang('Unread')</span>
                    </div>
                    <div class="notification__body">
                        @forelse ($userNotifications as $notification)
                        <a href="{{ route('user.notification.read', $notification->id) }}">
                            <p>{{ __($notification->title) }}</p>
                            <span>{{ $notification->created_at->diffForHumans() }}</span>
                        </a>
                        @empty
                            <a href="javascript:void(0)">
                                <p>@lang('No unread notification found')</p>
                            </a>
                        @endforelse
                    </div>
                    @if($userNotificationCount > 0)
                    <div class="notification__footer">
                        <a href="{{ route('user.notifications') }}" class="btn btn--base w-100">@lang('View All')</a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="dropdown">
            <button class="dropdown-toggle d-flex align-items-center gap-2" type="button"
                data-bs-toggle="dropdown" aria-expanded="false">
                <span class="profile__dropdown">
                    <img src="{{ getImage(getFilePath('userProfile') . '/' . auth()->user()->image, getFileSize('userProfile')) }}" alt="@lang('Image')">
                    <span>{{ auth()->user()->fullname }}</span>
                </span>
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="{{ route('user.profile.setting') }}">@lang('Profile Setting')</a></li>
                <li><a class="dropdown-item" href="{{ route('user.change.password') }}">@lang('Change Password')</a></li>
                <li><a class="dropdown-item" href="{{ route('user.twofactor') }}">@lang('2FA Setting')</a></li>
                <li><a class="dropdown-item" href="{{ route('user.logout') }}">@lang('Logout')</a></li>
            </ul>
        </div>
        <span class="sidebar__open"><i class="fa-solid fa-bars"></i></span>
    </div>
</div>
