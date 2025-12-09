@php
    $content = getContent('faq.content', true);
    $elements = getContent('faq.element', false, 8);
@endphp
<!--==========================  FAQ Section Start  ==========================-->
<section class="faq__area py-120 my-120 bg--img" data-background-image="{{getImage($activeTemplateTrue . 'images/dots.svg')}}">
    <div class="container">
        <div class="row g-4 align-items-center">
            <div class="col-lg-6">
                <div class="section__heading ms-0">
                    <span class="text--info">{{ __($content->data_values->title) }}</span>
                    <h2>{{ __($content->data_values->heading) }}</h2>
                    <p>{{ __($content->data_values->subheading) }}</p>
                    <a href="{{ url($content->data_values->button_url)}}" class="btn btn--base">{{ __($content->data_values->button_text) }}</a>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="faq__main">
                    <div class="accordion" id="accordionExample">
                        @forelse($elements as $element)
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#collapse{{ $loop->iteration }}"
                                        aria-expanded="{{ $loop->first ? 'true' : 'false' }}"
                                        aria-controls="collapse{{ $loop->iteration }}">
                                        {{ __($element->data_values->question) }}
                                    </button>
                                </h2>
                                <div id="collapse{{ $loop->iteration }}"
                                    class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}"
                                    data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        {{ __($element->data_values->answer) }}
                                    </div>
                                </div>
                            </div>
                        @empty

                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!--==========================  FAQ Section End  ==========================-->
