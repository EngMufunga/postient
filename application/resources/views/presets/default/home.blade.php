@extends('Template::layouts.frontend')
@section('content')
@php
    $bannerContent = getContent('banner.content', true);
    $bannerElements = getContent('banner.element', false, 6);
    $bannerImages = getContent('banner_image.element', false, 3);
@endphp

<!--==========================  Hero Section Start  ==========================-->
<section class="hero__area">
    <div class="container">
        <div class="hero__wrap bg--img" data-background-image="{{ getImage($activeTemplateTrue . 'images/dots.svg') }}">
            <div class="hero__line"><img src="{{ getImage($activeTemplateTrue . 'images/hero/bg-line.svg') }}" alt="image"></div>
            <div class="row g-4 align-items-center">
                <div class="col-lg-6">
                    <div class="hero__main">
                        <div class="platform">
                            <div class="platform__img">
                                @forelse($bannerElements as $item)
                                <span><img src="{{ getImage(getFilePath('banner') . '/' . $item->data_values->image, getFileSize('banner')) }}" alt="@lang('Image')"></span>
                                @empty
                                @endforelse

                            </div>
                            <p>{{ __($bannerContent->data_values->title) }}</p>
                        </div>
                        <div class="hero__content">
                            <h1>{{ __($bannerContent->data_values->heading) }}</h1>
                            <p>{{ __($bannerContent->data_values->subheading) }}</p>
                            <div class="hero__btn">
                                <a href="{{ url($bannerContent->data_values->primary_button_url)}}" class="btn btn--base">{{ __($bannerContent->data_values->primary_button_text) }}</a>
                                <a href="{{ url($bannerContent->data_values->secondary_button_url)}}" class="btn btn--white">{{ __($bannerContent->data_values->secondary_button_text) }}</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="hero__wrapper">
                        <div class="hero__ripple">
                            <div class="ripple__inner">
                                <div class="hero__logo">
                                    <img src="{{ siteLogo() }}" alt="@lang('Logo')">
                                </div>
                                <div class="hero__sp"></div>
                                <div class="hero__sp"></div>
                                <div class="hero__sp"></div>
                                <div class="hero__sp"></div>
                                <div class="hero__sp"></div>
                            </div>
                        </div>
                        <div class="hero__img">
                            @forelse($bannerImages as $item)
                            <div class="hero__img__single">
                                <img src="{{ getImage(getFilePath('banner_image'). '/'. $item->data_values->image, getFileSize('banner_image')) }}" alt="@lang('Image')">
                            </div>
                            @empty

                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!--==========================  Hero Section End  ==========================-->

@if($sections->secs != null)
    @foreach(json_decode($sections->secs) as $sec)
        @includeIf('Template::sections.'.$sec)
    @endforeach
@endif
@endsection


