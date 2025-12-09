@extends('Template::layouts.auth')
@section('content')
@php
    $content = getContent('authentication.content', true);
@endphp
@includeIf('Template::components.auth_content')
<div class="auth__main">
    <div class="auth__wrapper">
        <div class="auth__title">
            <h3>{{ __($content->data_values->sign_in) }}</h3>
        </div>
        <form method="POST" action="{{ route('user.login') }}" class="verify-gcaptcha">
            @csrf
            <div class="auth__form">
                <div class="row g-4">
                    <div class="col-lg-12">
                        <div class="auth__form__single">
                            <label class="form-label">@lang('Username or Email')</label>
                            <input type="text" class="form-control" name="username" value="{{ old('username') }}" placeholder="@lang('Enter username or email')" required>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="auth__form__single">
                            <label class="form-label">@lang('Password')</label>
                            <div class="password__field">
                                <input type="password" class="form-control" name="password" placeholder="@lang('Enter password')" required>
                                <div class="password-show-hide">
                                    <i class="fa-solid fa-eye-slash open-eye-icon"></i>
                                    <i class="fa-solid fa-eye close-eye-icon"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <x-captcha></x-captcha>
                    <div class="col-lg-12">
                        <div class="auth__widgets">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label" for="remember">
                                    @lang('Remember Me')
                                </label>
                            </div>
                            <a href="{{ route('user.password.request') }}">@lang('Forgot your password?')</a>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="auth__form__single">
                            <button type="submit" id="recaptcha" class="btn btn--base w-100">@lang('Sign In')</button>
                        </div>
                    </div>

                    @includeIf('Template::components.social_login')

                    <div class="col-lg-12">
                        <div class="auth__already">
                            <p>@lang('Don\'t have any account?') <a href="{{ route('user.register') }}">@lang('Sign Up')</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
