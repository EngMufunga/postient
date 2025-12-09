@extends('Template::layouts.master')
@section('content')
    <div class="row g-4">
        <div class="col-xl-5">
            <div class="profile__left card p-4">
                <form action="{{ route('user.profile.image.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="profile__wr">
                        <div class="profile__upload">
                            <label for="profile__change">
                                <img src="{{ getImage(getFilePath('userProfile') . '/' . $user->image, getFileSize('userProfile')) }}" alt="@lang('Image')">
                                <i class="fa-solid fa-image"></i>
                            </label>
                            <input type="file" id="profile__change" name="image" onchange="this.form.submit()" accept=".png, .jpeg, .jpg">
                        </div>
                        <h4>{{ $user->fullname }}</h4>
                    </div>
                </form>
                <ul>
                    <li>
                        <div class="profile__contact">
                            <p><i class="fa-solid fa-envelope"></i>@lang('Email')</p>
                            <a href="mailto:{{ $user->email }}">{{ $user->email }}</a>
                        </div>
                    </li>
                    <li>
                        <div class="profile__contact">
                            <p><i class="fa-solid fa-phone"></i>@lang('Mobile Number')</p>
                            <a href="tel:+{{$user->mobile}}">+{{$user->mobile}}</a>
                        </div>
                    </li>
                    <li>
                        <div class="profile__contact">
                            <p><i class="fa-solid fa-location-dot"></i>@lang('Address')</p>
                            <span>{{$user->address?->address ?? ''}}</span>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
        <div class="col-xl-7">
            <div class="profile__wrap card p-4">
                    <form action="{{ route('user.password.update') }}" method="post">
                        @csrf
                        <div class="form-group">
                            <label class="form-label">@lang('Current Password')</label>
                            <div class="password__field">
                                <input type="password" class="form-control form--control" name="current_password" required  autocomplete="current-password">
                                <div class="password-show-hide">
                                    <i class="fa-solid fa-eye close-eye-icon"></i>
                                    <i class="fa-solid fa-eye-slash open-eye-icon"></i>
                                </div>
                            </div>

                        </div>
                        <div class="form-group">
                            <label class="form-label">@lang('Password')</label>
                            <div class="password__field">
                                <input type="password" class="form-control form--control" name="password" required autocomplete="current-password">
                                <div class="password-show-hide">
                                    <i class="fa-solid fa-eye close-eye-icon"></i>
                                    <i class="fa-solid fa-eye-slash open-eye-icon"></i>
                                </div>
                            </div>


                            @if($general->secure_password)
                            <div class="input-popup">
                                <p class="error lower">@lang('1 small letter minimum')</p>
                                <p class="error capital">@lang('1 capital letter minimum')</p>
                                <p class="error number">@lang('1 number minimum')</p>
                                <p class="error special">@lang('1 special character minimum')</p>
                                <p class="error minimum">@lang('6 character password')</p>
                            </div>
                            @endif
                        </div>
                        <div class="form-group">
                            <label class="form-label">@lang('Confirm Password')</label>
                            <div class="password__field">
                                <input type="password" class="form-control form--control" name="password_confirmation" required autocomplete="current-password">
                                <div class="password-show-hide">
                                    <i class="fa-solid fa-eye close-eye-icon"></i>
                                    <i class="fa-solid fa-eye-slash open-eye-icon"></i>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn--base w-100">@lang('Change Password')</button>
                        </div>
                    </form>
            </div>
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
            @if ($general -> secure_password)
                $('input[name=password]').on('input', function () {
                    secure_password($(this));
                });

            $('[name=password]').on('focus', function () {
                $(this).closest('.form-group').addClass('hover-input-popup');
            });

            $('[name=password]').on('focusout', function () {
                $(this).closest('.form-group').removeClass('hover-input-popup');
            });

            @endif
        })(jQuery);
    </script>
@endpush
