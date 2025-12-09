@php
    $imenu = App\Models\Menu::where('code', 'useful_links')->latest()->first();
    $usefulLinks = $imenu ? $imenu->items()->get() : [];
    $qmenu = App\Models\Menu::where('code', 'quick_links')->latest()->first();
    $quickLinks = $qmenu ? $qmenu->items()->get() : [];
    $socialLinks = getContent('social_icon.element', false, 4);
    $contact = getContent('contact_us.content', true);
    $newsletter = getContent('subscribe.content', true);
@endphp
<!--==========================  Footer Section Start  ==========================-->
<section class="footer__area bg-img pt-120" data-background-image="{{ getImage($activeTemplateTrue . 'images/footer-dot.svg') }}">
    <div class="footer__dots">
        <img src="{{ getImage($activeTemplateTrue . 'images/footer-dots.svg') }}" alt="image">
    </div>
    <div class="container">
        <div class="row g-4 justify-content-center">
            <div class="col-xl-3 col-md-5">
                <div class="footer__about">
                    <a href="{{ route('home') }}"><img src="{{ siteLogo('white') }}" alt="@lang('Image')"></a>
                    <p>{{ __($contact->data_values->short_details) }} </p>
                    <ul class="social__icon dark__social">
                        @forelse($socialLinks as $item)
                        <li><a href="{{ $item->data_values->url }}">@php echo $item->data_values->social_icon @endphp</a></li>
                        @empty

                        @endforelse
                    </ul>
                </div>
            </div>
            <div class="col-xl-3 col-md-3 col-6 d-flex justify-content-start justify-content-sm-center">
                <div class="footer__single">
                    <h5>@lang('Quick Links')</h5>
                    <ul>
                        @if($quickLinks)
                            @foreach($quickLinks as $k => $data)
                                @if($data->link_type == 2)
                                    <li>
                                        <a href="{{ $data->url ?? '' }}" target="_blank"><i class="fa-solid fa-angle-right"></i> {{__($data->title)}}</a>
                                    </li>
                                @else
                                    <li>
                                        <a href="{{route('pages',[$data->url])}}"><i class="fa-solid fa-angle-right"></i> {{__($data->title)}}</a>
                                    </li>
                                @endif
                            @endforeach
                        @endif
                    </ul>
                </div>
            </div>
            <div
                class="col-xl-3 col-md-4 col-6 d-flex justify-content-start justify-content-sm-center  justify-content-xl-start">
                <div class="footer__single">
                    <h5>@lang('Useful Links')</h5>
                    <ul>
                        @if($usefulLinks)
                            @foreach($usefulLinks as $k => $data)
                                @if($data->link_type == 2)
                                    <li>
                                       <a href="{{ $data->url ?? '' }}" target="_blank"><i class="fa-solid fa-angle-right"></i> {{__($data->title)}}</a>
                                    </li>
                                @else
                                    <li>
                                       <a href="{{route('pages',[$data->url])}}"><i class="fa-solid fa-angle-right"></i> {{__($data->title)}}</a>
                                    </li>
                                @endif
                            @endforeach
                        @endif
                    </ul>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 col-sm-7">
                <div class="footer__single">
                    <h5>@lang('Newsletter')</h5>
                    <p>{{ __($newsletter->data_values->subheading) }}</p>
                    <form action="{{ route('subscribers') }}" method="POST">
                        @csrf
                        <div class="footer__newsletter">
                            <input type="email" name="email" class="form-control" placeholder="@lang('Email address')">
                            <button type="submit"><i class="fa-solid fa-paper-plane"></i></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="footer__copyright">
        @php echo $contact->data_values->website_footer; @endphp
    </div>
</section>
<!--==========================  Footer Section End  ==========================-->
