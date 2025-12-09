@extends('Template::layouts.auth')
@section('content')
@includeIf('Template::components.auth_content')
<div class="auth__main">
    <div class="auth__wrapper">
        <div class="d-flex justify-content-center">
            <div class="verification-code-wrapper border-0">
                <div class="verification-area border-0">
                    <h5 class="pb-3 text-center border-0">@lang('Verify Email Address')</h5>
                    <form action="{{ route('user.password.verify.code') }}" method="POST" class="submit-form">
                        @csrf
                        <p class="verification-text">@lang('A 6 digit verification code sent to your email address')
                            : {{ showEmailAddress($email) }}</p>
                        <input type="hidden" name="email" value="{{ $email }}">

                        @include('Template::components.verification_code')

                        <div class="form-group">
                            <button type="submit" class="btn btn--base w-100">@lang('Submit')</button>
                        </div>

                        <div class="form-group">
                            @lang('Please check including your Junk/Spam Folder. if not found, you can')
                            <a href="{{ route('user.password.request') }}" class="text--base">@lang('Try to send again')</a>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
