@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-12">
            <div class="row mb-5">
                <div class="user-profile--wrap">
                    <div class="user-profile--banner bg--img"
                        style="background-image: url({{ getImage('assets/images/general/user-banner.png') }})">

                    </div>

                    <div class="user-info--wrap">
                        <div class="row justify-content-around">
                            <div class="col-xxl-11 col-xl-12">
                                <div class="row gy-3 justify-content-between align-items-end ">
                                    <div class="col-xl-10 d-flex align-items-end position-relative">
                                        <div class="user--thumb flex-shrink-0 me-5">
                                            @if (!empty($user->image))
                                                <img src="{{ getImage(getFilePath('userProfile') . '/' . $user->image ?? '') }}"
                                                    alt="@lang('Image')">
                                            @else
                                                <img src="{{ getImage('assets/images/general/default.png') }}"
                                                    alt="@lang('Image')">
                                            @endif
                                        </div>
                                        <div class="row gy-3 w-100 mt-1 mt-lg-0">
                                            <div class="col-xl-4 col-md-6">
                                                <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                                                    <i class="fa-regular fa-user"></i>
                                                    <h6>{{ $user->fullname }}</h6>
                                                    <span class="badge badge--primary onHover">
                                                        <a href="{{ route('admin.users.login', $user->id) }}"
                                                            target="_blank">
                                                            <i class="fa-solid fa-arrow-right-to-bracket"></i>
                                                            @lang('Login as User')
                                                        </a>
                                                    </span>
                                                </div>
                                                <div class="d-flex align-items-center gap-2">
                                                    <i class="fa-solid fa-location-dot"></i>
                                                    <h6>{{ $user->address->address ?? trans('No Location Data') }}</h6>
                                                </div>
                                            </div>
                                            <div class="col-xl-4 col-md-6">
                                                <div class="d-flex align-items-center gap-2 mb-3">
                                                    <i class="fa-regular fa-envelope"></i>
                                                    <h6>{{ $user->email }}</h6>
                                                </div>
                                                <div class="d-flex align-items-center gap-2 mb-3">
                                                    <i class="fa-solid fa-phone"></i>
                                                    <h6>+{{ $user->mobile }}</h6>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="row gy-4">
                <div class="col-lg-12">
                    <div class="row gy-4 mb-4">
                        <div class="col-sm-6 col-xxl-3 col-xl-3">
                            <a class="dashboard-widget--card position-relative"
                                href="{{ route('admin.report.transaction') }}?search={{ $user->username }}">
                                <div class="dashboard-widget__icon">
                                    <i class="dashboard-card-icon fa-solid fa-check-to-slot"></i>
                                </div>

                                <div class="dashboard-widget__content">
                                    <span class="title">@lang('Balance')</span>
                                    <h5 class="number">{{ $general->cur_sym }}{{ showAmount($user->balance) }}</h5>
                                </div>
                                <span class="arrow--btn position-absolute"><i class="fa-solid fa-chevron-right"></i></span>
                            </a>
                        </div>
                        <div class="col-sm-6 col-xxl-3 col-xl-3">
                            <a class="dashboard-widget--card position-relative"
                                href="{{ route('admin.report.transaction') }}?search={{ $user->username }}">
                                <div class="dashboard-widget__icon">
                                    <i class="dashboard-card-icon fa-solid fa-credit-card"></i>
                                </div>
                                <div class="dashboard-widget__content">
                                    <span class="title">@lang('Total Deposit')</span>
                                    <h5 class="number">{{ $general->cur_sym }}{{ showAmount($totalDeposit) }}</h5>
                                </div>
                                <span class="arrow--btn position-absolute"><i class="fa-solid fa-chevron-right"></i></span>
                            </a>
                        </div>
                        <div class="col-sm-6 col-xxl-3 col-xl-3">
                            <a class="dashboard-widget--card position-relative"
                                href="{{ route('admin.report.transaction') }}?search={{ $user->username }}">
                                <div class="dashboard-widget__icon">
                                    <i class="dashboard-card-icon fa-solid fa-blog"></i>
                                </div>
                                <div class="dashboard-widget__content">
                                    <span class="title">@lang('Total Post')</span>
                                    <h5 class="number">{{ $totalPosts }}</h5>
                                </div>
                                <span class="arrow--btn position-absolute"><i class="fa-solid fa-chevron-right"></i></span>
                            </a>
                        </div>
                        <div class="col-sm-6 col-xxl-3 col-xl-3">
                            <a class="dashboard-widget--card position-relative"
                                href="{{ route('admin.report.transaction') }}?search={{ $user->username }}">
                                <div class="dashboard-widget__icon">
                                    <i class="dashboard-card-icon fa-solid fa-arrow-right-arrow-left"></i>
                                </div>
                                <div class="dashboard-widget__content">
                                    <span class="title">@lang('Transactions')</span>
                                    <h5 class="number">{{ $totalTransaction }}</h5>
                                </div>
                                <span class="arrow--btn position-absolute"><i class="fa-solid fa-chevron-right"></i></span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>


            <form action="{{ route('admin.users.update', [$user->id]) }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row gy-4">
                    <div class="col-xl-3">
                        <div class="card bg--white br--solid p-16 radius--base mb-4">
                            <div class="row gy-2 gx-2">
                                <div class="col-lg-12 mb-3 pb-3 border-bottom">
                                    <div
                                        class="card mb-3 bg--secondary-light p-16 radius--base d-flex justify-content-center align-items-center flex-column">
                                        <h6 class="mb-2"><i class="fa-regular fa-credit-card"></i> @lang('Available Balance')</h6>
                                        <h4>{{ $general->cur_sym }}{{ showAmount($user->balance) }}</h4>
                                    </div>
                                    <div class="row gy-2 gx-3">
                                        <div class="col-sm-6">
                                            <a class="d-block btn btn--primary bal-btn outline" href="javascript:void(0)"
                                                data-bs-toggle="modal" data-bs-target="#addSubModal" data-act="add"><i
                                                    class="fa-solid fa-plus"></i>
                                                @lang('Add')</a>
                                        </div>
                                        <div class="col-sm-6">
                                            <a class="d-block btn btn--danger bal-btn outline" href="javascript:void(0)"
                                                data-bs-toggle="modal" data-bs-target="#addSubModal" data-act="sub"><i
                                                    class="fa-solid fa-minus"></i>
                                                @lang('Sub')</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <a class="d-block btn btn--secondary outline"
                                        href="{{ route('admin.users.notification.log', $user->id) }}">
                                        <i class="fa-regular fa-bell"></i> @lang('Notifiactions')
                                    </a>
                                </div>
                                <div class="col-sm-12">
                                    <a class="d-block btn btn--secondary outline"
                                        href="{{ route('admin.report.login.history') }}?search={{ $user->username }}">
                                        <i class="fa-solid fa-clock-rotate-left"></i> @lang('Login History')
                                    </a>
                                </div>

                                <div class="col-sm-12">
                                    <a class="d-block btn btn--secondary outline"
                                        href="{{ route('admin.users.notification.single', $user->id) }}">
                                        <i class="fa-regular fa-paper-plane"></i> @lang('Send Email')
                                    </a>
                                </div>
                                <div class="col-sm-12">
                                    @if ($user->status == 1)
                                        <a class="d-block btn btn--danger outline userStatus" data-bs-toggle="modal"
                                            data-bs-target="#userStatusModal" href="javascript:void(0)">
                                            <i class="fa-solid fa-ban"></i> @lang('Ban User')
                                        </a>
                                    @else
                                        <a class="d-block btn userStatus btn-f-success outline" data-bs-toggle="modal"
                                            data-bs-target="#userStatusModal" href="javascript:void(0)">
                                            <i class="fa-solid fa-ban"></i> @lang('Unban User')
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="card bg--white br--solid p-16 radius--base">
                            <div class="row">
                                <div class="form-group col-12 d-flex justify-content-between align-items-center">
                                    <label>@lang('Email Verification') </label>
                                    <label class="switch m-0">
                                        <input type="checkbox" class="toggle-switch" name="ev"
                                            {{ $user->ev ? 'checked' : null }}>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                                <div class="form-group col-12 d-flex justify-content-between align-items-center">
                                    <label>@lang('Mobile Verification') </label>
                                    <label class="switch m-0">
                                        <input type="checkbox" class="toggle-switch" name="sv"
                                            {{ $user->sv ? 'checked' : null }}>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                                <div class="form-group col-12 d-flex justify-content-between align-items-center">
                                    <label>@lang('2FA Verification') </label>
                                    <label class="switch m-0">
                                        <input type="checkbox" class="toggle-switch" name="ts"
                                            {{ $user->ts ? 'checked' : null }}>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-9">
                        <div class="card">
                            <h5 class="card-title mb-3">@lang('Basic Information of') {{ $user->fullname }}</h5>
                            <div class="card radius--base bg--white br--solid p-16">
                                <div class="row mt-2">
                                    <div class="col-md-6">
                                        <div class="form-group ">
                                            <label>@lang('First Name')</label>
                                            <input class="form-control" type="text" name="firstname" required
                                                value="{{ $user->firstname }}">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-control-label">@lang('Last Name')</label>
                                            <input class="form-control" type="text" name="lastname" required
                                                value="{{ $user->lastname }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('Email') </label>
                                            <input class="form-control" type="email" name="email"
                                                value="{{ $user->email }}" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('Mobile Number') </label>
                                            <div class="input-group ">
                                                <span class="input-group-text mobile-code"></span>
                                                <input type="number" name="mobile" value="{{ old('mobile') }}"
                                                    id="mobile" class="form-control checkUser" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-4">
                                    <div class="col-md-12">
                                        <div class="form-group ">
                                            <label>@lang('Address')</label>
                                            <input class="form-control" type="text" name="address"
                                                value="{{ $user->address?->address ?? '' }}">
                                        </div>
                                    </div>

                                    <div class="col-xl-3 col-md-6">
                                        <div class="form-group">
                                            <label>@lang('City')</label>
                                            <input class="form-control" type="text" name="city"
                                                value="{{ $user->address?->city ?? '' }}">
                                        </div>
                                    </div>

                                    <div class="col-xl-3 col-md-6">
                                        <div class="form-group ">
                                            <label>@lang('State')</label>
                                            <input class="form-control" type="text" name="state"
                                                value="{{ $user->address?->state ?? '' }}">
                                        </div>
                                    </div>

                                    <div class="col-xl-3 col-md-6">
                                        <div class="form-group ">
                                            <label>@lang('Zip/Postal')</label>
                                            <input class="form-control" type="text" name="zip"
                                                value="{{ $user->address?->zip ?? '' }}">
                                        </div>
                                    </div>

                                    <div class="col-xl-3 col-md-6">
                                        <div class="form-group ">
                                            <label>@lang('Country')</label>
                                            <select name="country" class="form-control form-select">
                                                @foreach ($countries as $key => $country)
                                                    <option data-mobile_code="{{ $country->dial_code }}"
                                                        value="{{ $key }}">{{ __($country->country) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-4">
                                    <div class="col-md-12">
                                        <div class="form-group  text-end mb-0">
                                            <button type="submit" class="btn btn--primary">@lang('Save Changes')
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </div>

    {{-- Add Sub Balance MODAL --}}
    <div id="addSubModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><span class="type"></span> <span>@lang('Balance')</span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <form action="{{ route('admin.users.add.sub.balance', $user->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="act">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>@lang('Amount')</label>
                            <div class="input-group">
                                <input type="number" step="any" name="amount" class="form-control"
                                    placeholder="@lang('Please provide positive amount')" required>
                                <div class="input-group-text">{{ __($general->cur_text) }}</div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>@lang('Remark')</label>
                            <textarea class="form-control" placeholder="@lang('Remark')" name="remark" rows="4" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary">@lang('Save')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div id="userStatusModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        @if ($user->status == 1)
                            <span>@lang('Ban User')</span>
                        @else
                            <span>@lang('Unban User')</span>
                        @endif
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <form action="{{ route('admin.users.status', $user->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        @if ($user->status == 1)
                            <h6 class="mb-2">@lang('If you ban this user he/she won\'t able to access his/her
                                                                                                                                                                                                                                                        dashboard.')</h6>
                            <div class="form-group">
                                <label>@lang('Reason')</label>
                                <textarea class="form-control" name="reason" rows="4" required></textarea>
                            </div>
                        @else
                            <p><span>@lang('Ban reason was'):</span></p>
                            <p>{{ $user->ban_reason }}</p>
                            <h4 class="text-center mt-3">@lang('Are you sure to unban this user?')</h4>
                        @endif
                    </div>
                    <div class="modal-footer">
                        @if ($user->status == 1)
                            <button type="submit" class="btn btn--primary">@lang('Save')</button>
                        @else
                            <button type="button" class="btn btn--dark"
                                data-bs-dismiss="modal">@lang('No')</button>
                            <button type="submit" class="btn btn--primary">@lang('Yes')</button>
                        @endif
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

            $('select[name=country]').val('{{ $user->country_code ?? '' }}');
            let dialCode = $('select[name=country] :selected').data('mobile_code');
            let mobileNumber = `{{ $user->mobile }}`;
            mobileNumber = mobileNumber.replace(dialCode, '');
            $('input[name=mobile]').val(mobileNumber);
            mobileElement.text(`+${dialCode}`);

        })(jQuery);
    </script>
@endpush
