@php
    $credentials = gs('socialite_credentials');
@endphp

@if ($credentials->google->status == Status::YES || $credentials->facebook->status == Status::YES)
<div class="col-lg-12">
    <div class="auth__or">
        <p>@lang('Or')</p>
    </div>
    <div class="social__login">
        <ul class="social__icon">
            @if ($credentials->facebook->status == Status::YES)
            <li><a href="{{ route('user.social.login', 'facebook') }}"><i class="fa-brands fa-facebook-f"></i></a></li>
            @endif

            @if ($credentials->google->status == Status::YES)
            <li><a href="{{ route('user.social.login', 'google') }}"><i class="fa-solid fa-g"></i></a></li>
            @endif

        </ul>
    </div>
</div>
@endif
