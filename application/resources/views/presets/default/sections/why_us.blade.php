@php
    $content = getContent('why_us.content',true);
    $elements = getContent('why_us.element',false,3);
    $bannerElements = getContent('banner.element', false, 7);
    $classes = ['one', 'two', 'three', 'four', 'five', 'six', 'seven'];
@endphp
<!--==========================  About Section Start  ==========================-->
<section class="about__area my-120">
    <div class="container">
        <div class="row g-4 align-items-center">
            <div class="col-lg-7">
                <div class="about__wrap">
                    <div class="solar-system">
                        <div class="solar__logo">
                            <img src="{{ siteLogo() }}" alt="@lang('Logo')">
                        </div>
                        <div class="orbit one"></div>
                        <div class="orbit two"></div>
                        <div class="orbit three"></div>

                        @forelse($bannerElements as $index => $item)

                        <div class="planet {{ $classes[$index] ?? '' }}">
                            <img src="{{ getImage(getFilePath('banner') . '/'. $item->data_values->image, getFileSize('banner')) }}" alt="@lang('Image')">
                        </div>
                        @empty

                        @endforelse
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="section__heading ms-0 mb-4">
                    <span class="text--pink">{{ __($content->data_values->title) }}</span>
                    <h2>{{ __($content->data_values->heading) }}</h2>
                    <p>{{ __($content->data_values->subheading) }}</p>
                </div>
                <div class="about__info">
                    @forelse($elements as $index => $item)
                    <div class="about__single">
                        <span>@php echo $item->data_values->icon; @endphp</span>
                        <div>
                            <h5>{{ __($item->data_values->title) }}</h5>
                            <p>{{ __($item->data_values->description) }}</p>
                        </div>
                    </div>
                    @empty

                    @endforelse
                </div>
            </div>
        </div>
    </div>
</section>
<!--==========================  About Section End  ==========================-->
