@php
    $cookie = App\Models\Frontend::where('data_keys','cookie.data')->first();
@endphp

@if ($cookie->data_values->status == 1 && !\Cookie::get('gdpr_cookie'))
    <!-- cookies card start -->
    <div class="cookies-card hide">
        <p class="cookies-card__content">
            {{ $cookie->data_values->short_desc }}
            <a class="text--base" href="{{ route('cookie.policy') }}" target="_blank">
                @lang('learn more')
            </a>
        </p>
        <div class="cookies-card__actions">
            <a href="javascript:void(0)" class="btn btn--md btn--base policy"> @lang('Accept') </a>
        </div>
    </div>
    <!-- cookies card end -->
@endif

