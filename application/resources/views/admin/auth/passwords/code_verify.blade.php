@extends('admin.layouts.master')
@section('content')
<div class="login_area">
    <div class="login">
        <div class="login__header">
            <h2>@lang('Verification')</h2>
            <p>@lang('Please enter the verification code')</p>
        </div>
        <div class="login__body w-100">
            <form class="form w-100 submit-form" action="{{ route('admin.password.verify.code') }}" method="POST">
                @csrf
                <div class="code-box-wrapper d-flex w-100">
                    <div class="form-group mb-3 flex-fill">
                        <span class="text-white mb-1 d-block">@lang('Verification Code')</span>
                        <div class="verification-code-two">
                            <input type="text" name="code" id="verification-code" class="form-control overflow-hidden" required autocomplete="off" hidden>
                            <div class="boxes-two">
                                <input type="text" class="code-input" maxlength="1" placeholder="-">
                                <input type="text" class="code-input" maxlength="1" placeholder="-">
                                <input type="text" class="code-input" maxlength="1" placeholder="-">
                                <input type="text" class="code-input" maxlength="1" placeholder="-">
                                <input type="text" class="code-input" maxlength="1" placeholder="-">
                                <input type="text" class="code-input" maxlength="1" placeholder="-">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-row my-2">
                    <a href="{{ route('admin.password.reset') }}" class="forget-text">@lang('Try to send again')</a>
                </div>
                <div class="form-row button-login">
                    <button type="submit" class="btn btn-login btn--primary sign-in">@lang('Verify')</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('style-lib')
    <link rel="stylesheet" href="{{asset('assets/admin/css/auth.css')}}">
    <link rel="stylesheet" href="{{asset('assets/common/css/verification_code.css')}}">
@endpush


@push('script')
<script>
    (function($) {
        'use strict';

        let codeInput = $('#verification-code');
        let inputs = $('.code-input');

        inputs.on('input', function() {
            let code = '';
            inputs.each(function() {
                code += $(this).val();
            });
            codeInput.val(code);

            if (code.length === 6) {
                setTimeout(() => {
                    $('.submit-form').find('button[type=submit]').html('<i class="las la-spinner fa-spin"></i>');
                    $('.submit-form').find('button[type=submit]').removeClass('disabled');
                    $('.submit-form').submit();
                }, 200);
            } else {
                $('.submit-form').find('button[type=submit]').addClass('disabled');
            }

            if ($(this).val().length === 1 && $(this).next('.code-input').length) {
                $(this).next('.code-input').focus();
            }
        });

        inputs.on('focus', function() {
            $(this).select();
        });

    })(jQuery);

</script>
@endpush

