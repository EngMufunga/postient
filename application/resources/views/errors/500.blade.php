<!doctype html>
<html lang="{{ config('app.locale') }}" itemscope itemtype="http://schema.org/WebPage">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title> {{ $general->site_name  . ' - '. __('500 || Server Error') }}</title>
    <link rel="shortcut icon" href="{{ siteFavicon() }}">
    <link rel="stylesheet" href="{{ asset('assets/common/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{asset('assets/common/css/all.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/common/css/line-awesome.min.css')}}">
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue.'css/swiper-bundle.min.css') }}">
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue.'css/main.css') }}">
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue.'css/color.php') }}?color={{ $general->base_color }}&secondColor={{ $general->secondary_color }}">
    <link rel="stylesheet" href="{{asset($activeTemplateTrue.'css/custom.css')}}">
</head>
<body>

    @includeIf($activeTemplate.'components.preloader')
    <main>
    <div class="error bg--img" data-background-image="{{ getImage($activeTemplateTrue.'images/error/error-bg.jpg') }}">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="error__main py-60">
                        <img src="{{ getImage($activeTemplateTrue.'images/error/500.png') }}" alt="@lang('Error Image')">
                        <h2>500</h2>
                        <p>@lang('Internal Server Error')</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </main>


    <script src="{{asset('assets/common/js/jquery-3.7.1.min.js')}}"></script>
    <script src="{{asset('assets/common/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{ asset($activeTemplateTrue.'js/scrollreveal.min.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue.'js/swiper-bundle.min.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue.'js/main.js') }}"></script>

    <script>
        (function ($) {
            "use strict";
            $(".langSel").on("change", function() {
                window.location.href = "{{route('home')}}/change/"+$(this).val() ;
            });

        })(jQuery);
    </script>
</body>
</html>
