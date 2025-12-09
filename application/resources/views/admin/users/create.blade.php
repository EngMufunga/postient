@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <h5 class="card-title mb-3">@lang('Basic Information to create a new user')</h5>
                    <div class="card radius--base bg--white br--solid p-16">
                        <div class="row mt-2">
                            <div class="col-md-4">
                                <div class="form-group ">
                                    <label for="username">@lang('Username')</label>
                                    <input class="form-control checkUser" type="text" name="username" id="username" required>
                                    <small class="text-danger usernameExist"></small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group ">
                                    <label for="firstname">@lang('First Name')</label>
                                    <input class="form-control" type="text" name="firstname" id="firstname" required>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="lastname" class="form-control-label">@lang('Last Name')</label>
                                    <input class="form-control" type="text" name="lastname" id="lastname" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group ">
                                    <label for="country">@lang('Country')</label>
                                    <select name="country" class="form-control form-select" id="country">
                                        @foreach($countries as $key => $country)
                                            <option data-mobile_code="{{ $country->dial_code }}" value="{{ $country->country }}" data-code="{{ $key }}" {{ $key == 'AF' ? 'selected' : '' }}>{{ __($country->country) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">@lang('Mobile')</label>
                                    <div class="input-group ">
                                        <span class="input-group-text mobile-code">

                                        </span>
                                        <input type="hidden" name="mobile_code">
                                        <input type="hidden" name="country_code">
                                        <input type="number" name="mobile" value="{{ old('mobile') }}" class="form-control form--control checkUser" required>
                                    </div>
                                    <small class="text-danger mobileExist"></small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">@lang('Email') </label>
                                    <input class="form-control checkUser" type="email" name="email" id="email" required>
                                                     <small class="text-danger emailExist"></small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">@lang('Password') </label>
                                    <input type="password" name="password"  id="password" class="form-control checkUser" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group ">
                                    <label for="address">@lang('Address')</label>
                                    <textarea class="form-control" type="text" name="address" id="address"></textarea>
                                </div>
                            </div>

                            <div class="col-xl-4 col-md-6">
                                <div class="form-group">
                                    <label for="city">@lang('City')</label>
                                    <input class="form-control" type="text" name="city" id="city">
                                </div>
                            </div>

                            <div class="col-xl-4 col-md-6">
                                <div class="form-group ">
                                    <label for="state">@lang('State')</label>
                                    <input class="form-control" type="text" name="state" id="state">
                                </div>
                            </div>

                            <div class="col-xl-4 col-md-6">
                                <div class="form-group ">
                                    <label for="zip">@lang('Zip/Postal')</label>
                                    <input class="form-control" type="text" name="zip" id="zip">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group  text-end mb-0">
                                    <button type="submit" class="btn btn--primary">
                                        @lang('Create')
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection


@push('script')
    <script>
        (function($) {
            "use strict"

            $('.bal-btn').on('click', function() {
                var act = $(this).data('act');
                $('#addSubModal').find('input[name=act]').val(act);
                if (act == 'add') {
                    $('.type').text('Add');
                } else {
                    $('.type').text('Subtract');
                }
            });
            let mobileElement = $('.mobile-code');
            $('select[name=country]').on('change', function() {
                mobileElement.text(`+${$('select[name=country] :selected').data('mobile_code')}`);
            });

            $('select[name=country]').trigger('change');


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
                    $(`.${response.type}Exist`).text(`${response.type} already exist`);
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
