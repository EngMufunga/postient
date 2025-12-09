<!doctype html>
<html lang="{{ config('app.locale') }}" itemscope itemtype="http://schema.org/WebPage">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title> {{ $general->siteName(__($pageTitle)) }}</title>
    @include('includes.seo')
    <link rel="shortcut icon" href="{{ siteFavicon() }}">
    <link rel="stylesheet" href="{{ asset('assets/common/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{asset('assets/common/css/all.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/common/css/line-awesome.min.css')}}">

    @stack('style-lib')
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue.'css/swiper-bundle.min.css') }}">
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue.'css/main.css') }}">
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue.'css/color.php') }}?color={{ $general->base_color }}&secondColor={{ $general->secondary_color }}">
    <link rel="stylesheet" href="{{asset($activeTemplateTrue.'css/custom.css')}}">
    @stack('style')
</head>
<body>

    @includeIf('Template::components.preloader')

    <main>
        <div class="auth__area">
            @yield('content')
        </div>
    </main>



    <script src="{{asset('assets/common/js/jquery-3.7.1.min.js')}}"></script>
    <script src="{{asset('assets/common/js/bootstrap.bundle.min.js')}}"></script>

    @stack('script-lib')
    <script src="{{ asset($activeTemplateTrue.'js/scrollreveal.min.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue.'js/swiper-bundle.min.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue.'js/main.js') }}"></script>
    @stack('script')

    @include('includes.plugins')

    @include('includes.notify')



</body>
</html>
