@extends('Template::layouts.frontend')
@section('content')
    @php
        $contactContent = getContent('contact_us.content', true);
        $contactElements = getContent('contact_us.element', false, 4, true);
    @endphp

    <!--==========================  Contact Info Section Start  ==========================-->
    <section class="contact__info my-120">
        <div class="container">
            <div class="contact__card__wrap">
                @forelse($contactElements as $item)
                    <div class="contact__card">
                        <span>@php echo $item->data_values->icon; @endphp</span>
                        <h4>{{ __($item->data_values->title) }}</h4>
                        <p>{{ $item->data_values->value }}</p>
                    </div>
                @empty

                @endforelse
            </div>
        </div>
    </section>
    <!--==========================  Contact Info Section End  ==========================-->

    <!--==========================  Contact Section Start  ==========================-->
    <section class="contact__area my-120">
        <div class="container">
            <div class="row g-4 align-items-center">
                <div class="col-lg-6">
                    <div class="section__heading ms-0">
                        <span class="text--info">{{ __($contactContent->data_values->title) }}</span>
                        <h2>{{ __($contactContent->data_values->heading) }}</h2>
                        <p>{{ __($contactContent->data_values->subheading) }}</p>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="contact__wrap">
                        <form method="post" action="{{ route('contact.submit') }}" class="verify-gcaptcha">
                            @csrf
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="contact__form">
                                        <label class="form-label">@lang('Name')</label>
                                        <input type="text" class="form-control" name="name"
                                            value="@if(auth()->user()){{ auth()->user()->fullname }} @else{{ old('name') }}@endif"
                                            @if(auth()->user()) readonly @endif placeholder="@lang('Name')" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="contact__form">
                                        <label class="form-label">@lang('Email')</label>
                                        <input type="email" class="form-control" name="email"
                                            value="@if(auth()->user()){{ auth()->user()->email }}@else{{  old('email') }}@endif"
                                            @if(auth()->user()) readonly @endif placeholder="@lang('Email')" required>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="contact__form">
                                        <label class="form-label">@lang('Subject')</label>
                                        <input type="text" name="subject" class="form-control form--control"
                                            value="{{old('subject')}}" required placeholder="@lang('Subject')">
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="contact__form">
                                        <label class="form-label">@lang('Message')</label>
                                        <textarea class="form-control" name="message" required
                                            placeholder="@lang('Write message')">{{ old('message') }}</textarea>
                                    </div>
                                </div>
                                <x-captcha></x-captcha>
                                <div class="col-lg-12">
                                    <div class="contact__form">
                                        <button type="submit" id="recaptach"
                                            class="btn btn--base">@lang('Send Message')</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--==========================  Contact Section End  ==========================-->

    <!--==========================  Map Section Start  ==========================-->
    <div class="contact__map">
        <iframe src="https://maps.google.com/maps?q=40.69461270785467,-73.94226835283084&z=14&output=embed"
             allowfullscreen loading="lazy"
            referrerpolicy="no-referrer-when-downgrade">
        </iframe>
    </div>
    <!--==========================  Map Section End  ==========================-->

    @if($sections->secs != null)
        @foreach(json_decode($sections->secs) as $sec)
            @includeIf('Template::sections.' . $sec)
        @endforeach
    @endif

@endsection