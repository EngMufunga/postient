@php
    $hmenu = App\Models\Menu::where('code', 'header_menu')->first();
    $pages = $hmenu ? $hmenu->items()->get() : [];

    $selectedLang = App\Models\Language::where('code', session('lang'))->first();
@endphp
<!--==========================  Offcanvas Section Start  ==========================-->
<div class="offcanvas__area">
    <div class="offcanvas__topbar">
        <a href="{{ route('home') }}">
            <img src="{{ siteLogo() }}" alt="@lang('Logo')">
        </a>
        <span class="menu__close"><i class="las la-times"></i></span>
    </div>
    <div class="offcanvas__main">
        <div class="offcanvas__widgets">
            <div class="offcanvas__language">
                <div class="dropdown">
                    <div role="button" class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="language__item">
                            <img src="{{ getImage(getFilePath('language') . '/' . $selectedLang->image) }}" alt="@lang('Flag')">
                            <p>{{ __($selectedLang->name) }}</p>
                        </div>
                    </div>
                    <ul class="dropdown-menu">
                        @foreach ($language as $item)
                        <li class="langSel" data-value="{{ $item->code }}">
                            <div class="language__item dropdown-item">
                                <img src="{{ getImage(getFilePath('language') . '/' . $item->image) }}" alt="@lang('Flag')">
                                <p>{{ __($item->name) }}</p>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="offcanvas__login">
                @guest
                <a href="{{ route('user.login') }}"><i class="fa-regular fa-user"></i> @lang('Login')</a>
                @else
                <a href="{{ route('user.home') }}"><i class="fa-solid fa-house"></i> @lang('Dashboard')</a>
                @endguest
            </div>
        </div>
        <div class="offcanvas__menu">
            <ul>
                @if($pages)
                    @foreach($pages as $k => $data)
                        @if($data->link_type == 2)
                            <li>
                                <a href="{{ $data->url ?? '' }}" target="_blank">{{__($data->title)}}</a>
                            </li>
                        @else
                            <li>
                                <a href="{{route('pages',[$data->url])}}" class="{{ route('pages',[$data->url]) == url()->current() ? 'active' : null }}">{{__($data->title)}}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            </ul>
        </div>
    </div>
</div>
<!--==========================  Offcanvas Section End  ==========================-->


<!-- ==================== Header Start Here ==================== -->
<header class="header__area">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="header__main">
                    <div class="header__logo">
                        <a href="{{ route('home') }}"><img src="{{ siteLogo() }}" alt="@lang('Logo')"></a>
                    </div>
                    <div class="header__menu">
                        <ul>
                            @if($pages)
                                @foreach($pages as $k => $data)
                                    @if($data->link_type == 2)
                                        <li>
                                            <a href="{{ $data->url ?? '' }}" target="_blank">{{__($data->title)}}</a>
                                        </li>
                                    @else
                                        <li>
                                            <a href="{{route('pages',[$data->url])}}" class="{{ route('pages',[$data->url]) == url()->current() ? 'active' : null }}">{{__($data->title)}}</a>
                                        </li>
                                    @endif
                                @endforeach
                            @endif
                        </ul>
                    </div>
                    <div class="header__widgets">
                        <div class="dropdown">
                            <div role="button" class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                <div class="language__item">
                                    <img src="{{ getImage(getFilePath('language') . '/' . $selectedLang->image) }}" alt="@lang('Flag')">
                                    <p>{{ __($selectedLang->name) }}</p>
                                </div>
                            </div>
                            <ul class="dropdown-menu">
                                @foreach ($language as $item)
                                <li class="langSel" data-value="{{ $item->code }}">
                                    <div class="language__item dropdown-item">
                                        <img src="{{ getImage(getFilePath('language') . '/' . $item->image) }}" alt="@lang('Flag')">
                                        <p>{{ __($item->name) }}</p>
                                    </div>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="header__login">
                            @guest
                                <a href="{{ route('user.login') }}"><i class="fa-regular fa-user"></i> @lang('Login')</a>
                            @else
                                <a href="{{ route('user.home') }}"><i class="fa-solid fa-house"></i> @lang('Dashboard')</a>
                            @endguest
                        </div>
                    </div>
                    <span class="menu__open"><i class="fa-solid fa-bars"></i></span>
                </div>
            </div>
        </div>
    </div>
</header>
<!-- ==================== Header End Here ==================== -->
