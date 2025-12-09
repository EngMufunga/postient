@extends('admin.layouts.app')

@section('panel')
    <div class="row gy-4 justify-content-start mb-3 pb-3">
        <div class="col-lg-5">
            <form>
                <div class="row gy-4">
                    <div class="col-sm-6">
                        <div class="search-input--wrap position-relative">
                            <input type="search" name="search" class="form-control" placeholder="@lang('Search by username')"
                                value="{{ request()->search }}">
                            <button class="search--btn position-absolute" type="submit"><i class="fa fa-search"></i></button>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="search-input--wrap position-relative">
                            <input name="date" type="text" data-range="true" data-multiple-dates-separator=" - " data-language="en" class="datepicker-here form-control" data-position='bottom right' placeholder="@lang('Date from - to')" autocomplete="off" value="{{ request()->date }}">
                            <button class="search--btn position-absolute" type="submit"><i class="fa fa-search"></i></button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('User')</th>
                                    <th>@lang('Login at')</th>
                                    <th>@lang('IP')</th>
                                    <th>@lang('Browser and OS')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($loginLogs as $log)
                                    <tr>

                                        <td class="user--td">
                                            <div class="d-flex justify-content-between justify-content-lg-start gap-3">
                                                <div class="user--info d-flex gap-3 flex-shrink-0 align-items-center flex-wrap flex-md-nowrap">
                                                    <div class="user--thumb-two">
                                                        @if(!empty($log->user->image))
                                                            <img src="{{ getImage(getFilePath('userProfile') . '/' . $log?->user?->image ) }}" alt="@lang('Image')">
                                                        @else
                                                            <img src="{{ getImage('assets/images/general/avatar.png') }}" alt="@lang('Image')">
                                                        @endif
                                                    </div>

                                                    <div class="user--content">
                                                        <a class="text-start text-dark" href="{{ appendQuery('search', $log?->user?->username) }}">
                                                            {{ $log->user?->fullname ?? '' }}
                                                        </a>
                                                        <br>
                                                        <a href="{{ route('admin.users.detail', $log->user_id) }}" class="text-start">{{ '@'.$log->user?->username ?? ''}}</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <td>
                                            {{ showDateTime($log->created_at) }}
                                        </td>

                                        <td>
                                            <span class="fw--500">
                                                <a href="{{ route('admin.report.login.ipHistory', [$log->user_ip]) }}">{{ $log->user_ip }}</a>
                                            </span>
                                        </td>

                                        <td>
                                            {{ __($log->browser) }}, {{ __($log->os) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse

                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($loginLogs->hasPages())
                    <div class="pagination__wrapper py-4">
                        {{ paginateLinks($loginLogs) }}
                    </div>
                @endif
            </div>
        </div>


    </div>
@endsection


@if (request()->routeIs('admin.report.login.ipHistory'))
    @push('breadcrumb-plugins')
        <a href="https://www.ip2location.com/{{ $ip }}" target="_blank"
            class="btn btn--primary">@lang('Lookup IP') {{ $ip }}</a>
    @endpush
@endif


@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/common/css/datepicker.min.css') }}">
@endpush


@push('script-lib')
    <script src="{{ asset('assets/common/js/datepicker.min.js') }}"></script>
    <script src="{{ asset('assets/common/js/datepicker.en.js') }}"></script>
@endpush
@push('script')
    <script>
        (function($) {
            "use strict";
            if (!$('.datepicker-here').val()) {
                $('.datepicker-here').datepicker();
            }
        })(jQuery)
    </script>
@endpush
