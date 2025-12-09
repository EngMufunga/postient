@php
    $content = getContent('feature.content', true);
    $elements = getContent('feature.element', false, 3);
@endphp

<!--==========================  Key Feature Section Start  ==========================-->
<section class="key__feature my-120">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="key__topbar mb-60">
                    <div class="section__heading ms-0">
                        <span class="text--pink">{{ __($content->data_values->title) }}</span>
                        <h2>{{ __($content->data_values->heading) }}</h2>
                        <p>{{ __($content->data_values->subheading) }}</p>
                    </div>
                    <a href="{{ route('features') }}" class="btn btn--base">@lang('View all Features')</a>
                </div>
            </div>
        </div>
        <div class="row gy-5 justify-content-center">
            @forelse($elements as $item)
            <div class="col-md-4 col-sm-6">
                <div class="key__feature__single">
                    <img src="{{ getImage(getFilePath('feature') . '/'. $item->data_values->image, getFileSize('feature')) }}" alt="@lang('Image')">
                    <h4>{{ __($item->data_values->title) }}</h4>
                    <p>{{ __($item->data_values->description) }}</p>
                </div>
            </div>
            @empty

            @endforelse

        </div>
    </div>
</section>
<!--==========================  Key Feature Section End  ==========================-->
