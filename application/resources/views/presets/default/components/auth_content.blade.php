@php
    $elements = getContent('authentication.element', false, 4);
@endphp
<div class="auth__thumb bg--img" data-background-image="{{ getImage($activeTemplateTrue . 'images/footer-dots2.svg') }}">
    <div class="auth__thumb__wrap">
        <div class="hero__ripple auth__ripple">
            <div class="ripple__inner">
                <div class="hero__logo">
                    <a href="{{ route('home') }}"><img src="{{ siteLogo('white') }}" alt="@lang('Logo')"></a>
                </div>
                <div class="hero__sp"></div>
                <div class="hero__sp"></div>
                <div class="hero__sp"></div>
                <div class="hero__sp"></div>
                <div class="hero__sp"></div>
            </div>
        </div>
        <div class="swiper auth__slider">
            <div class="swiper-wrapper">
                @foreach ($elements ?? [] as $item)
                <div class="swiper-slide">
                    <div class="auth__slide">
                        <h3>{{ __($item->data_values->heading) }}</h3>
                        <p>{{ __($item->data_values->description) }}</p>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="swiper-pagination"></div>
        </div>
    </div>
</div>
