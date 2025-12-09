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
    @includeIf('Template::components.cookie')
    @includeIf('Template::components.header')
    <main>
        @if(Route::currentRouteName() !== 'home')
            @includeIf('Template::components.frontend_breadcrumb')
        @endif
        @yield('content')
    </main>
    @includeIf('Template::components.footer')

    <script src="{{asset('assets/common/js/jquery-3.7.1.min.js')}}"></script>
    <script src="{{asset('assets/common/js/bootstrap.bundle.min.js')}}"></script>

    @stack('script-lib')
    <script src="{{ asset($activeTemplateTrue.'js/scrollreveal.min.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue.'js/swiper-bundle.min.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue.'js/main.js') }}"></script>
    @stack('script')

    @include('includes.plugins')

    @include('includes.notify')


<script>
    (function ($) {
        "use strict";
        $(".langSel").on("click", function() {
            const langCode = $(this).data('value');
            window.location.href = "{{ route('home') }}/change/" + langCode;
        });



        $('.policy').on('click',function(){
            $.get('{{route('cookie.accept')}}', function(response){
                $('.cookies-card').addClass('d-none');
            });
        });

        setTimeout(function(){
            $('.cookies-card').removeClass('hide')
        },2000);

    })(jQuery);
</script>

</body>
</html>
