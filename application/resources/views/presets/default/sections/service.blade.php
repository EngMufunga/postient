@php
    $content = getContent('service.content', true);
    $elements = getContent('service.element', false, 6);
@endphp
<!--==========================  Feature Section Start  ==========================-->
<section class="feature__area my-120 py-120 bg--img" data-background-image="{{ getImage($activeTemplateTrue . 'images/dots.svg') }}">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="section__heading mb-60 text-center">
                    <span class="text--base">{{ __($content->data_values->title) }}</span>
                    <h2>{{ __($content->data_values->heading) }}</h2>
                    <p>{{ __($content->data_values->subheading) }}</p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="feature__wrap">
                    @forelse($elements as $index =>$item)
                    <div class="feature__single">
                        <span>@php echo $item->data_values->icon; @endphp</span>
                        <h5>{{ __($item->data_values->title) }}</h5>
                        <p>{{ __($item->data_values->description) }}</p>
                    </div>
                    @empty

                    @endforelse
                </div>
            </div>
        </div>
    </div>
</section>
<!--==========================  Feature Section End  ==========================-->
