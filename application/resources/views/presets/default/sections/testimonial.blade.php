@php
    $testimonials = getContent('testimonial.element',false,6);
@endphp
    <!--==========================  Testimonial Section Start  ==========================-->
<section class="testimonial__area my-120">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="testimonial__wrap">
                    <div class="swiper testimonial__slider">
                        <div class="swiper-wrapper">
                            @forelse($testimonials as $index=>$data)
                            <div class="swiper-slide">
                                <div class="testimonial__single">
                                    <img src="{{ getImage(getFilePath('testimonial').'/'. $data->data_values->image, getFileSize('testimonial')) }}" alt="@lang('Image')">
                                    <div class="testimonial__quote">
                                        <h3>“{{ __($data->data_values->review) }}”</h3>
                                        <img class="quote__img" src="{{ getImage($activeTemplateTrue . 'images/quote.svg') }}" alt="@lang('Image')">
                                    </div>
                                    <div class="testimonial__user">
                                        <h4>{{ __($data->data_values->name) }}</h4>
                                        <p>{{ __($data->data_values->designation) }}</p>
                                    </div>
                                </div>
                            </div>
                            @empty

                            @endforelse
                        </div>
                        <div class="swiper-pagination"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!--==========================  Testimonial Section End  ==========================-->
