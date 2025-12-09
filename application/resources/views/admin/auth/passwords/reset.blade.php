@extends('admin.layouts.master')
@section('content')
<div class="login_area">
    <div class="login">
        <div class="login__header">
            <h2>@lang('Reset Password')</h2>
            <p>@lang('Provide a new password to log in')</p>
        </div>
        <div class="login__body">
            <form action="{{ route('admin.password.change') }}" method="POST">
                @csrf
                <input type="hidden" name="email" value="{{ $email }}">
                <input type="hidden" name="token" value="{{ $token }}">
                <div class="field">
                    <input type="password" name="password" placeholder="@lang('Password')" required>
                    <span class="show-pass new-password"><i class="fas fa-eye-slash"></i></span>
                </div>
                <div class="field">
                    <input type="password" id="password_confirmation" name="password_confirmation" placeholder="@lang('Password Confirmation')"
                        required>
                        <span class="show-pass confirm-password"><i class="fas fa-eye-slash"></i></span>
                </div>
                <div class="field">
                    <button type="submit" class="sign-in mt-2">@lang('Reset')</button>
                </div>
                <div class="login__footer d-flex justify-content-center">
                    <a class="float-end" href="{{ route('admin.login') }}">@lang('Go back')</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@push('style')
    <link rel="stylesheet" href="{{asset('assets/admin/css/auth.css')}}">
@endpush
@push('script')
    <script>
        (function ($) {
            'use strict';

            $(".new-password").on('click', function() {
                var passwordInput = $("#password");
                var showPassIcon = $(this).find("i");
                if (passwordInput.attr("type") === "password") {
                    passwordInput.attr("type", "text");
                    showPassIcon.removeClass("fa-eye-slash");
                    showPassIcon.addClass("fa-eye");
                } else {
                    passwordInput.attr("type", "password");
                    showPassIcon.removeClass("fa-eye");
                    showPassIcon.addClass("fa-eye-slash");
                }
            });

            $(".confirm-password").on('click', function() {
                var passwordInput = $("#password_confirmation");
                var showPassIcon = $(this).find("i");
                if (passwordInput.attr("type") === "password") {
                    passwordInput.attr("type", "text");
                    showPassIcon.removeClass("fa-eye-slash");
                    showPassIcon.addClass("fa-eye");
                } else {
                    passwordInput.attr("type", "password");
                    showPassIcon.removeClass("fa-eye");
                    showPassIcon.addClass("fa-eye-slash");
                }
            });

        })(jQuery);
    </script>
@endpush
