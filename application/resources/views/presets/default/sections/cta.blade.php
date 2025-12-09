@php
    $content = getContent('cta.content', true);
    $elements = getContent('cta.element', false, 7);
@endphp
<!--==========================  CTA Section Start  ==========================-->
<section class="cta__area my-120">
    <div class="container">
        <div class="cta__wrap bg--img" data-background-image="{{ getImage($activeTemplateTrue . 'images/cta-line.svg') }}">
            <div class="row g-4 align-items-center">
                <div class="col-lg-6">
                    <div class="section__heading">
                        <span class="text--pink">{{ __($content->data_values->title) }}</span>
                        <h2>{{ __($content->data_values->heading) }}</h2>
                        <p>{{ __($content->data_values->subheading) }}</p>
                        <a href="{{ url($content->data_values->button_url)}}" class="btn btn--base">{{ __($content->data_values->button_text) }}</a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="cta__img">
                        @forelse($elements as $item)
                        <img src="{{ getImage(getFilePath('cta') .'/'. $item->data_values->image, getFileSize('cta')) }}" alt="@lang('Image')">
                        @empty
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!--==========================  CTA Section End  ==========================-->
