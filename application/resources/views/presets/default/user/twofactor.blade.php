@extends('Template::layouts.master')
@section('content')
    <div class="row g-4">
        <div class="col-xl-6">
            <div class="card p-4">
                <div class="card-header pt-0">
                    <h5 class="card-title">@lang('Add Your Account')</h5>
                </div>
                <div class="card-body px-0 pb-0">
                    <div class="two__factor__info">
                        <p>@lang('Use the QR code or setup key on your Google Authenticator app to add your account.')</p>
                        <img src="{{$qrCodeUrl}}" alt="@lang('QR Code')">
                    </div>
                    <div class="two__factor__key">
                        <label class="form-label">@lang('Setup Key')</label>
                        <div class="input-group">
                            <input type="text" name="key" class="form-control referralURL" value="{{$secret}}">
                            <button class="input-group-text copytext" id="copyBoard"><i class="fa-solid fa-copy"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            @if(auth()->user()->ts)
            <div class="card p-4">
                <div class="card-header pt-0">
                    <h5 class="card-title">@lang('Disable 2FA Security')</h5>
                </div>
                <form action="{{route('user.twofactor.disable')}}" method="POST">
                    @csrf
                    <input type="hidden" name="key" value="{{$secret}}">
                    <div class="card-body px-0 pb-0">
                        <div class="two__factor__form">
                            <label class="form-label">@lang('Google Authenticatior OTP')</label>
                            <input class="form-control" type="text" name="code" required>
                            <button type="submit" class="btn btn--base w-100 mt-3">@lang('Save')</button>
                        </div>
                    </div>
                </form>
            </div>
            @else
            <div class="card p-4">
                <div class="card-header pt-0">
                    <h5 class="card-title">@lang('Enable 2FA Security')</h5>
                </div>
                <form action="{{ route('user.twofactor.enable') }}" method="POST">
                    @csrf
                    <input type="hidden" name="key" value="{{$secret}}">
                    <div class="card-body px-0 pb-0">
                        <div class="two__factor__form">
                            <label class="form-label">@lang('Google Authenticatior OTP')</label>
                            <input class="form-control" type="text" name="code" required>
                            <button type="submit" class="btn btn--base w-100 mt-3">@lang('Save')</button>
                        </div>
                    </div>
                </form>
            </div>
            @endif
        </div>
    </div>
@endsection

@push('style')
<style>
    .copied::after {
        background-color: #{{ $general->base_color }};
    }
</style>
@endpush

@push('script')
<script>
    (function ($) {
        "use strict";
        $('#copyBoard').on('click', function () {
            var copyText = document.getElementsByClassName("referralURL");
            copyText = copyText[0];
            copyText.select();
            copyText.setSelectionRange(0, 99999);
            /*For mobile devices*/
            document.execCommand("copy");
            copyText.blur();
            this.classList.add('copied');
            setTimeout(() => this.classList.remove('copied'), 1500);
        });
    })(jQuery);
</script>
@endpush
