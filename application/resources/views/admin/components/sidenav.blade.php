<div class="sidebar">
    <button class="res-sidebar-close-btn"><i class="fa-solid fa-xmark"></i></button>
    <div class="sidebar__inner">
        <div class="sidebar__logo">
            <a href="{{ route('admin.dashboard') }}" class="sidebar__main-logo"><img
                    src="{{ siteLogo() }}" alt="@lang('image')"></a>
        </div>

        <div class="sidebar__menu-wrapper">
            <ul class="sidebar__menu">
                <li class="sidebar-menu-item {{ menuActive('admin.dashboard') }}">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link ">
                        <i class="menu-icon fa-solid fa-gauge"></i>
                        <span class="menu-title">@lang('Dashboard')</span>
                    </a>
                </li>

                
                @adminHasAny(['role', 'staff'])
                    <li class="sidebar-menu-item sidebar-dropdown {{ menuActive(['admin.role.*', 'admin.staff.*']) }}">
                        <a href="javascript:void(0)" class="{{ menuActive(['admin.role.*', 'admin.staff.*'], 3) }}">
                            <i class="menu-icon fas fa-user-shield"></i>
                            <span class="menu-title">@lang('Staff & Roles')</span>
                        </a>
                        <div class="sidebar-submenu {{ menuActive(['admin.role.*', 'admin.staff.*'], 2) }} ">
                            <ul>
                                @adminHas('role')
                                    <li class="sidebar-menu-item {{ menuActive('admin.role.*') }}">
                                        <a class="nav-link" href="{{ route('admin.role.index') }}">
                                            <i class="menu-icon fa-solid fa-circle"></i>
                                            <span class="menu-title"> @lang('Roles')</span>
                                        </a>
                                    </li>
                                @endadminHas

                                @adminHas('staff')
                                    <li class="sidebar-menu-item {{ menuActive('admin.staff.*') }}">
                                        <a class="nav-link" href="{{ route('admin.staff.index') }}">
                                            <i class="menu-icon fa-solid fa-circle"></i>
                                            <span class="menu-title"> @lang('Staffs')</span>
                                        </a>
                                    </li>
                                @endadminHas
                            </ul>
                        </div>
                    </li>
                @endadminHasAny

                @adminHasAny(['user-management', 'subscriber-management'])
                    <li
                        class="sidebar-menu-item sidebar-dropdown {{ menuActive(['admin.users.*', 'admin.subscriber.*']) }}">
                        <a href="javascript:void(0)"
                            class="{{ menuActive(['admin.users.*', 'admin.subscriber.*'], 3) }}">
                            <i class="menu-icon fa-regular fa-user"></i>
                            <span class="menu-title">@lang('Users')</span>
                            @if (
                                $bannedUsersCount > 0 || $emailUnverifiedUsersCount > 0 || $mobileUnverifiedUsersCount > 0 )
                                <div class="blob white"></div>
                            @endif
                        </a>
                        <div
                            class="sidebar-submenu {{ menuActive(['admin.users.*', 'admin.subscriber.*'], 2) }} ">
                            <ul>
                                @adminHas('user-management')
                                    <li class="sidebar-menu-item {{ menuActive(['admin.users.all']) }}">
                                        <a class="nav-link" href="{{ route('admin.users.all') }}">
                                            <i class="menu-icon fa-solid fa-circle"></i>
                                            <span class="menu-title"> @lang('All Users')</span>
                                        </a>
                                    </li>

                                    <li class="sidebar-menu-item {{ menuActive(['admin.users.notification.all']) }}">
                                        <a class="nav-link" href="{{ route('admin.users.notification.all') }}">
                                            <i class="menu-icon fa-solid fa-circle"></i>
                                            <span class="menu-title"> @lang('Notification to Users')</span>
                                        </a>
                                    </li>
                                @endadminHas

                                @adminHas('subscriber-management')
                                    <li class="sidebar-menu-item {{ menuActive(['admin.subscriber.*']) }}">
                                        <a class="nav-link" href="{{ route('admin.subscriber.index') }}">
                                            <i class="menu-icon fa-solid fa-circle"></i>
                                            <span class="menu-title"> @lang('Subscribers')</span>
                                        </a>
                                    </li>
                                @endadminHas
                            </ul>
                        </div>
                    </li>
                @endadminHasAny
                
                @adminHas('platform-management')
                <li class="sidebar-menu-item {{ menuActive('admin.platform.*') }}">
                    <a href="{{ route('admin.platform.index') }}" class="nav-link ">
                        <i class="menu-icon fa-solid fa-layer-group"></i>
                        <span class="menu-title">@lang('Platform')</span>
                    </a>
                </li>
                @endadminHas

                @adminHas('post-management')
                <li class="sidebar-menu-item {{ menuActive('admin.posts.*') }}">
                    <a href="{{ route('admin.posts.index') }}" class="nav-link ">
                        <i class="menu-icon fa-solid fa-blog"></i>
                        <span class="menu-title">@lang('Posts')</span>
                    </a>
                </li>
                @endadminHas



                @adminHas('plan-management')
                    <li class="sidebar-menu-item sidebar-dropdown {{ menuActive('admin.plan.*') }}">
                        <a href="javascript:void(0)" class="{{ menuActive('admin.plan.*', 3) }}">
                            <i class="menu-icon fa-solid fa-gift"></i>
                            <span class="menu-title">@lang('Plan Manage')</span>
                        </a>
                        <div class="sidebar-submenu {{ menuActive('admin.plan.*', 2) }} ">
                            <ul>
                                <li class="sidebar-menu-item {{ menuActive(['admin.plan.create', 'admin.plan.index', 'admin.plan.edit']) }}">
                                    <a class="nav-link" href="{{ route('admin.plan.index') }}">
                                        <i class="menu-icon fa-solid fa-circle"></i>
                                        <span class="menu-title"> @lang('Plans')</span>
                                    </a>
                                </li>

                                <li class="sidebar-menu-item {{ menuActive('admin.plan.subscriptions') }}">
                                    <a class="nav-link" href="{{ route('admin.plan.subscriptions') }}">
                                        <i class="menu-icon fa-solid fa-circle"></i>
                                        <span class="menu-title"> @lang('Subscriptions')</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endadminHas

                @adminHasAny(['deposit-management', 'payment-method'])
                    <li class="sidebar-menu-item sidebar-dropdown {{ menuActive(['admin.deposit.*', 'admin.gateway.*']) }}">
                        <a href="javascript:void(0)" class="{{ menuActive(['admin.deposit.*', 'admin.gateway.*'], 3) }}">
                            <i class="menu-icon fa-solid fa-dollar-sign"></i>
                            <span class="menu-title">@lang('Payments')</span>
                            @if (0 < $pendingDepositsCount)
                                <div class="blob white"></div>
                            @endif
                        </a>
                        <div class="sidebar-submenu {{ menuActive(['admin.deposit.*', 'admin.gateway.*'], 2) }} ">
                            <ul>
                                @adminHas('deposit-management')
                                    <li class="sidebar-menu-item {{ menuActive('admin.deposit.*') }}">
                                        <a class="nav-link" href="{{ route('admin.deposit.log') }}">
                                            <i class="menu-icon fa-solid fa-circle"></i>
                                            <span class="menu-title"> @lang('Deposits Log')</span>
                                            @if (0 < $pendingDepositsCount)
                                                <div class="blob white"></div>
                                            @endif
                                        </a>
                                    </li>
                                @endadminHas

                                @adminHas('payment-method')
                                    <li class="sidebar-menu-item {{ menuActive('admin.gateway.automatic.index') }}">
                                        <a class="nav-link" href="{{ route('admin.gateway.automatic.index') }}">
                                            <i class="menu-icon fa-solid fa-circle"></i>
                                            <span class="menu-title"> @lang('Automatic Gateways')</span>
                                        </a>
                                    </li>

                                    <li class="sidebar-menu-item {{ menuActive('admin.gateway.manual.index') }}">
                                        <a class="nav-link" href="{{ route('admin.gateway.manual.index') }}">
                                            <i class="menu-icon fa-solid fa-circle"></i>
                                            <span class="menu-title"> @lang('Manual Gateways')</span>
                                        </a>
                                    </li>
                                @endadminHas
                            </ul>
                        </div>
                    </li>
                @endadminHasAny

                @adminHas('reports')
                    <li class="sidebar-menu-item sidebar-dropdown {{ menuActive('admin.report.*') }}">
                        <a href="javascript:void(0)" class="{{ menuActive('admin.report.*', 3) }}">
                            <i class="menu-icon fa-solid fa-chart-line"></i>
                            <span class="menu-title">@lang('Reports')</span>
                        </a>
                        <div class="sidebar-submenu {{ menuActive('admin.report.*', 2) }} ">
                            <ul>
                                <li
                                    class="sidebar-menu-item {{ menuActive(['admin.report.transaction']) }}">
                                    <a class="nav-link" href="{{ route('admin.report.transaction') }}">
                                        <i class="menu-icon fa-solid fa-circle"></i>
                                        <span class="menu-title"> @lang('Transactions')</span>
                                    </a>
                                </li>
                                <li
                                    class="sidebar-menu-item {{ menuActive(['admin.report.login.history', 'admin.report.login.ipHistory']) }}">
                                    <a class="nav-link" href="{{ route('admin.report.login.history') }}">
                                        <i class="menu-icon fa-solid fa-circle"></i>
                                        <span class="menu-title"> @lang('Login Activities')</span>
                                    </a>
                                </li>
                                <li class="sidebar-menu-item {{ menuActive('admin.report.notification.history') }}">
                                    <a class="nav-link" href="{{ route('admin.report.notification.history') }}">
                                        <i class="menu-icon fa-solid fa-circle"></i>
                                        <span class="menu-title"> @lang('Notifications')</span>
                                    </a>
                                </li>

                            </ul>
                        </div>
                    </li>
                @endadminHas


                @adminHas('support-ticket')
                    <li class="sidebar-menu-item {{ menuActive(['admin.ticket.*']) }}">
                        <a href="{{ route('admin.ticket.index') }}" class="nav-link ">
                            <i class="menu-icon fa-solid fa-headset"></i>
                            <span class="menu-title"> @lang('Support Ticket')</span>
                            @if (0 < $pendingTicketCount)
                                <div class="blob white">
                                </div>
                            @endif
                        </a>
                    </li>
                @endadminHas

                @adminHasAny(['website-menu-management', 'section-management', 'page-management'])
                    <li
                        class="sidebar-menu-item sidebar-dropdown {{ menuActive(['admin.menu.*', 'admin.frontend.sections*', 'admin.custom.section.index', 'admin.frontend.manage.*']) }}">
                        <a href="javascript:void(0)"
                            class="{{ menuActive(['admin.menu.*', 'admin.frontend.sections*', 'admin.custom.section.index', 'admin.frontend.manage.*'], 3) }}">
                            <i class="menu-icon fa-solid fa-bars"></i>
                            <span class="menu-title">@lang('Website Contents')</span>
                        </a>
                        <div
                            class="sidebar-submenu {{ menuActive(['admin.menu.*', 'admin.frontend.sections*', 'admin.custom.section.index', 'admin.frontend.manage.*'], 2) }} ">
                            <ul>
                                @adminHas('page-management')
                                    <li class="sidebar-menu-item {{ menuActive('admin.frontend.manage.*') }}">
                                        <a class="nav-link" href="{{ route('admin.frontend.manage.pages') }}">
                                            <i class="me-1 fa-solid fa-file"></i>
                                            <span class="menu-title"> @lang('Pages')</span>
                                        </a>
                                    </li>
                                @endadminHas

                                @adminHas('website-menu-management')
                                    <li class="sidebar-menu-item {{ menuActive('admin.menu.*') }}">
                                        <a class="nav-link" href="{{ route('admin.menu.index') }}">
                                            <i class="me-1 fa-solid fa-bars-staggered"></i>
                                            <span class="menu-title"> @lang('Website Menus')</span>
                                        </a>
                                    </li>
                                @endadminHas

                                @adminHas('section-management')
                                    <li class="sidebar-menu-item {{ menuActive('admin.custom.section.index') }}">
                                        <a class="nav-link" href="{{ route('admin.custom.section.index') }}">
                                            <i class="me-1 fa-brands fa-html5"></i>
                                            <span class="menu-title">@lang('Add HTML Section')</span>
                                        </a>
                                    </li>

                                    @php
                                        $lastSegment = collect(request()->segments())->last();
                                    @endphp

                                    @foreach (getPageSections(true) as $k => $secs)
                                        @if ($secs['builder'])
                                            <li class="sidebar-menu-item  @if ($lastSegment == $k) active @endif ">
                                                <a href="{{ route('admin.frontend.sections', $k) }}" class="nav-link">
                                                    <i class="me-1 fa-solid fa-table-cells-large"></i>
                                                    <span class="menu-title">{{ __($secs['name']) }}</span>
                                                </a>
                                            </li>
                                        @endif
                                    @endforeach
                                @endadminHas

                            </ul>
                        </div>
                    </li>
                @endadminHasAny


                @adminHas('settings')
                    <li
                        class="sidebar-menu-item {{ menuActive(['admin.setting.index', 'admin.setting.logo.icon', 'admin.setting.notification.*', 'admin.seo', 'admin.setting.cookie', 'admin.setting.custom.css', 'admin.setting.maintenance', 'admin.language.key', 'admin.language.manage', 'admin.plugins.index']) }}">
                        <a href="{{ route('admin.setting.index') }}" class="nav-link">
                            <i class="menu-icon fa-solid fa-gear"></i>
                            <span class="menu-title">@lang('Settings')</span>
                        </a>
                    </li>
                @endadminHas

                <li class="sidebar-menu-item">
                    <span class="nav-link admin__version">
                        <i class="menu-icon fa-solid fa-code-branch"></i>
                         <span class="menu-title">@lang('Panel') {{ sysInfo()['admin_version'] }}</span>
                    </span>
                </li>

            </ul>
        </div>
    </div>
</div>



@push('script')
    <script>
        (function($) {
            'use strict';
            var $scroll = $('.sidebar__menu-wrapper');

            $('.sidebar-menu-item.active').each(function() {
                var itemPosition = $(this).offset().top - $scroll.offset().top + $scroll.scrollTop() - 110;
                $scroll.animate({
                    scrollTop: itemPosition
                }, 500);
            });
        })(jQuery);
    </script>
@endpush
