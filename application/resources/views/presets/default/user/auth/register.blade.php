@extends('Template::layouts.auth')
@section('content')
@php
    $content = getContent('authentication.content', true);
    $policyPages = getContent('policy_pages.element',false,null,true);
@endphp
@includeIf('Template::components.auth_content')
<div class="auth__main">
    <div class="auth__wrapper signup__wrapper">
        <div class="auth__title">
            <h3>{{ __($content->data_values->sign_up) }}</h3>
        </div>
        <form action="{{ route('user.register') }}" method="POST" class="verify-gcaptcha">
            @csrf
            <div class="auth__form">
                <div class="row g-4">
                    @if(session()->get('reference') != null)
                    <div class="col-xl-6 col-lg-12 col-sm-6">
                        <div class="auth__form__single">
                            <label for="referenceBy" class="form-label">@lang('Reference by')</label>
                            <input type="text" name="referBy" id="referenceBy" class="form-control" value="{{session()->get('reference')}}" readonly>
                        </div>
                    </div>
                    @endif
                    <div class="col-xl-6 col-lg-12 col-sm-6">
                        <div class="auth__form__single">
                            <label class="form-label">@lang('Username')</label>
                            <input type="text" class="form-control checkUser" name="username" value="{{ old('username') }}" placeholder="@lang('Enter Username')" required>
                            <small class="text-danger usernameExist"></small>
                        </div>
                    </div>
                    <div class="col-xl-6 col-lg-12 col-sm-6">
                        <div class="auth__form__single">
                            <label class="form-label">@lang('Email')</label>
                            <input type="email" class="form-control checkUser" name="email" placeholder="@lang('Enter Email')" value="{{ old('email') }}" required>
                        </div>
                    </div>
                    <div class="col-xl-6 col-lg-12 col-sm-6">
                        <div class="auth__form__single">
                            <label class="form-label">@lang('Password')</label>
                            <div class="password__field">
                                <input type="password" class="form-control" name="password" placeholder="@lang('Enter Password')" required>
                                <div class="password-show-hide">
                                    <i class="fa-solid fa-eye close-eye-icon"></i>
                                    <i class="fa-solid fa-eye-slash open-eye-icon"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6 col-lg-12 col-sm-6">
                        <div class="auth__form__single">
                            <label class="form-label">@lang('Confirm Password')</label>
                            <div class="password__field">
                                <input type="password" class="form-control" name="password_confirmation" placeholder="@lang('Enter Confirm Password')" required>
                                <div class="password-show-hide">
                                    <i class="fa-solid fa-eye close-eye-icon"></i>
                                    <i class="fa-solid fa-eye-slash open-eye-icon"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <x-captcha></x-captcha>
                    @if($general->agree)
                    <div class="col-lg-12">
                        <div class="auth__check">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="agree" @checked(old('agree')) name="agree" required>
                                <label class="form-check-label" for="agree">
                                    @lang('I agree with') @foreach($policyPages as $policy) <a href="{{ route('policy.pages',[slug($policy->data_values->title),$policy->id]) }}">{{ __($policy->data_values->title) }}</a> @if(!$loop->last), @endif @endforeach
                                </label>
                            </div>
                        </div>
                    </div>
                    @endif
                    <div class="col-lg-12">
                        <div class="auth__form__single">
                            <button type="submit" id="recaptcha" class="btn btn--base w-100">@lang('Sign Up')</button>
                        </div>
                    </div>
                    @includeIf('Template::components.social_login')
                    <div class="col-lg-12">
                        <div class="auth__already">
                            <p>@lang('Already have an account?') <a href="{{ route('user.login') }}">@lang('Sign In')</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>


@endsection

@push('script-lib')
<script src="{{ asset('assets/common/js/secure_password.js') }}"></script>
@endpush
@push('script')
    <script>
        (function ($) {
            "use strict";
            @if($general->secure_password)
                $('input[name=password]').on('input',function(){
                    secure_password($(this));
                });

                $('[name=password]').on('focus'function () {
                    $(this).closest('.form-group').addClass('hover-input-popup');
                });

                $('[name=password]').on('focusout', function () {
                    $(this).closest('.form-group').removeClass('hover-input-popup');
                });
            @endif

            $('.checkUser').on('focusout',function(e){
                var url = '{{ route('user.checkUser') }}';
                var value = $(this).val();
                var token = '{{ csrf_token() }}';
                if ($(this).attr('name') == 'mobile') {
                    var mobile = `${$('.mobile-code').text().substr(1)}${value}`;
                    var data = {mobile:mobile,_token:token}
                }
                if ($(this).attr('name') == 'email') {
                    var data = {email:value,_token:token}
                }
                if ($(this).attr('name') == 'username') {
                    var data = {username:value,_token:token}
                }
                $.post(url,data,function(response) {
                  if(response.data != false){
                    $(`.${response.type}Exist`).text(`${response.type} already exist`);
                  }else{
                    $(`.${response.type}Exist`).text('');
                  }
                });
            });
        })(jQuery);

    </script>
@endpush
