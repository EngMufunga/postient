@extends('Template::layouts.auth')
@section('content')
@includeIf('Template::components.auth_content')
<div class="auth__main">
    <div class="auth__wrapper">
        <div class="auth__title">
            <h3>@lang('Reset Password')</h3>
            <p>@lang('Your account is verified successfully. Now you can change your password. Please enter a strong password and don\'t share it with anyone.')</p>
        </div>
        <form method="POST" action="{{ route('user.password.update') }}">
            @csrf
            <input type="hidden" name="email" value="{{ $email }}">
            <input type="hidden" name="token" value="{{ $token }}">

            <div class="auth__form">
                <div class="row g-4">
                    <div class="col-xl-12">
                        <div class="auth__form__single">
                            <label class="form-label">@lang('Password')</label>
                            <div class="password__field">
                                <input type="password" class="form-control" name="password" required>
                                <div class="password-show-hide">
                                    <i class="fa-solid fa-eye close-eye-icon"></i>
                                    <i class="fa-solid fa-eye-slash open-eye-icon"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-12">
                        <div class="auth__form__single">
                            <label class="form-label">@lang('Confirm Password')</label>
                            <div class="password__field">
                                <input type="password" class="form-control" name="password_confirmation" required>
                                <div class="password-show-hide">
                                    <i class="fa-solid fa-eye close-eye-icon"></i>
                                    <i class="fa-solid fa-eye-slash open-eye-icon"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="auth__form__single">
                            <button type="submit" class="btn btn--base w-100">@lang('Save')</button>
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
        @if ($general->secure_password)
            $('input[name=password]').on('input', function () {
                secure_password($(this));
            });

        $('[name=password]').on('focus',function () {
            $(this).closest('.form-group').addClass('hover-input-popup');
        });

        $('[name=password]').on('focusout', function () {
            $(this).closest('.form-group').removeClass('hover-input-popup');
        });
        @endif
    })(jQuery);
</script>
@endpush
