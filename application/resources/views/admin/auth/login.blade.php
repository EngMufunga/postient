@extends('admin.layouts.master')
@section('content')
    <div class="login_area">
        <div class="login">
            <div class="login--logo">
                <img src="{{ siteFavicon() }}" alt="@lang('image')">
            </div>
            <div class="login__header">
                <h2>{{ __($pageTitle) }}</h2>
                <p>{{ __($general->site_name) }} @lang('Dashboard')</p>
            </div>
            <div class="login__body w-100">
                <form action="{{ route('admin.login') }}" method="POST">
                    @csrf
                    <div class="field mb-3">
                        <label>@lang('Username')</label>
                        <div class="position-relative">
                            <input type="text" name="username" placeholder="@lang('Username')">
                        </div>
                    </div>
                    <div class="field">
                        <label>@lang('Password')</label>
                        <div class="position-relative">
                            <input type="password" name="password" placeholder="@lang('Password')">
                        </div>
                    </div>
                    <div class="login__footer">
                        <div class="field_remember">
                            <div class="remember_wrapper form--check flex-nowrap">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                                <label class="form-check-label" class="remember" for="remember">@lang('Remember')</label>
                            </div>
                        </div>
                        <div class="field_foget">
                            <a href="{{ route('admin.password.reset') }}">@lang('Forgot password?')</a>
                        </div>
                    </div>
                    <x-captcha></x-captcha>
                    <div class="field">
                        <button type="submit" class="sign-in">@lang('Sign in')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('style')
    <link rel="stylesheet" href="{{ asset('assets/admin/css/auth.css') }}">
@endpush

