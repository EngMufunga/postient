@extends('Template::layouts.auth')
@section('content')
@includeIf('Template::components.auth_content')
<div class="auth__main">
    <div class="auth__wrapper">
        <div class="d-flex justify-content-center">
            <div class="verification-code-wrapper border-0">
                <div class="verification-area border-0">
                    <h5 class="pb-3 text-center border-0">@lang('Verify Mobile Number')</h5>
                    <form action="{{route('user.verify.mobile')}}" method="POST" class="submit-form">
                        @csrf
                        <p class="verification-text">@lang('A 6 digit verification code sent to your mobile number') : +{{
                        showMobileNumber(auth()->user()->mobile) }}</p>

                        @include('Template::components.verification_code')

                        <div class="form-group">
                            <button type="submit" class="btn btn--base w-100">@lang('Submit')</button>
                        </div>

                        <div class="form-group">
                            <p>
                                @lang('If you don\'t get any code'),
                                <a href="{{route('user.send.verify.code', 'phone')}}" class="forget-pass text--base"> @lang('Try again')</a>
                            </p>
                            @if($errors->has('resend'))
                            <br />
                            <small class="text--danger">{{ $errors->first('resend') }}</small>
                            @endif
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

