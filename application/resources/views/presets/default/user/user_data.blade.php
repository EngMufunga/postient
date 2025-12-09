@extends('Template::layouts.auth')

@section('content')
@includeIf('Template::components.auth_content')
<div class="auth__main">
    <div class="auth__wrapper signup__wrapper">
        <div class="auth__title">
            <h3>{{ __($pageTitle) }}</h3>
        </div>
        <form method="POST" action="{{ route('user.data.submit') }}">
            @csrf
            <div class="auth__form">
                <div class="row g-4">

                    <div class="col-xl-6 col-lg-12 col-sm-6">
                        <div class="auth__form__single">
                            <label for="firstname" class="form-label required">@lang('First Name')</label>
                            <input type="text" name="firstname" id="firstname" class="form-control" name="firstname" value="{{ old('firstname') }}" placeholder="@lang('Firstname')" required>
                        </div>
                    </div>
                    <div class="col-xl-6 col-lg-12 col-sm-6">
                        <div class="auth__form__single">
                            <label for="lastname" class="form-label required">@lang('Last Name')</label>
                            <input type="text" name="lastname" id="lastname" class="form-control" name="lastname" value="{{ old('lastname') }}" placeholder="@lang('Lastname')" required>
                        </div>
                    </div>

                    <div class="col-xl-6 col-lg-12 col-sm-6">
                        <div class="auth__form__single">
                            <label class="form-label">@lang('Country')</label>
                            <select name="country" class="select2 required" required>
                                @foreach($countries as $key => $country)
                                    <option data-mobile_code="{{ $country->dial_code }}" value="{{ $country->country }}" data-code="{{ $key }}">{{ __($country->country) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-xl-6 col-lg-12 col-sm-6">
                        <div class="auth__form__single">
                            <label class="form-label required">@lang('Mobile')</label>
                            <div class="input-group">
                                <span class="input-group-text mobile-code"></span>
                                <input type="hidden" name="mobile_code">
                                <input type="hidden" name="country_code">
                                <input type="number" name="mobile" value="{{ old('mobile') }}" class="form-control checkUser" placeholder="123456" required>
                            </div>
                            <small class="text-danger mobileExist"></small>
                        </div>
                    </div>
                    <div class="col-xl-6 col-lg-12 col-sm-6">
                        <div class="auth__form__single">
                            <label for="address" class="form-label">@lang('Address')</label>
                            <input type="text" name="address" id="address" class="form-control" value="{{session()->get('reference')}}" name="address" value="{{ old('address') }}" placeholder="@lang('Address')">
                        </div>
                    </div>
                    <div class="col-xl-6 col-lg-12 col-sm-6">
                        <div class="auth__form__single">
                            <label for="state" class="form-label">@lang('State')</label>
                            <input type="text" name="state" id="state" class="form-control" name="state" value="{{ old('state') }}" placeholder="@lang('State')">
                        </div>
                    </div>
                    <div class="col-xl-6 col-lg-12 col-sm-6">
                        <div class="auth__form__single">
                            <label for="zip" class="form-label">@lang('Zip')</label>
                            <input type="text" name="zip" id="zip" class="form-control" name="zip" value="{{ old('zip') }}" placeholder="@lang('Zip')">
                        </div>
                    </div>
                    <div class="col-xl-6 col-lg-12 col-sm-6">
                        <div class="auth__form__single">
                            <label for="city" class="form-label">@lang('City')</label>
                            <input type="text" name="city" id="city" class="form-control" name="city" value="{{ old('city') }}" placeholder="@lang('City')">
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
    <script src="{{asset('assets/common/js/select2.min.js')}}"></script>
@endpush

@push('style-lib')
    <link rel="stylesheet" href="{{asset('assets/common/css/select2.min.css')}}">
@endpush

@push('script')
    <script>
        (function ($) {
            "use strict";

            @if($mobileCode)
            $(`option[data-code={{ $mobileCode }}]`).attr('selected','');
            @endif

            $('select[name=country]').on('change', function(){
                $('input[name=mobile_code]').val($('select[name=country] :selected').data('mobile_code'));
                $('input[name=country_code]').val($('select[name=country] :selected').data('code'));
                $('.mobile-code').text('+'+$('select[name=country] :selected').data('mobile_code'));
            });
            $('input[name=mobile_code]').val($('select[name=country] :selected').data('mobile_code'));
            $('input[name=country_code]').val($('select[name=country] :selected').data('code'));
            $('.mobile-code').text('+'+$('select[name=country] :selected').data('mobile_code'));

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
                  if (response.data != false && response.type == 'email') {
                    $('#existModalCenter').modal('show');
                  }else if(response.data != false){
                    $(`.${response.type}Exist`).text(`${response.type} already exist`);
                  }else{
                    $(`.${response.type}Exist`).text('');
                  }
                });
            });

        })(jQuery);
    </script>
@endpush
