@extends('admin.layouts.app')

@section('panel')
    <div class="row gy-4 pb-4 mb-2">
        <div class="col-lg-12 mt-5">
            <div class="row gy-4">
                <div class="col-sm-6 col-xxl-3 col-xl-3">
                    <a class="dashboard-widget--card position-relative"
                        href="{{ route('admin.deposit.log', ['status' => 'approved']) }}">
                        <div class="dashboard-widget__icon">
                            <i class="dashboard-card-icon fa-solid fa-check-to-slot"></i>
                        </div>
                        <div class="dashboard-widget__content">
                            <span class="title">@lang('Successful Deposit')</span>
                            <h5 class="number">{{ __($general->cur_sym) }}{{ showAmount($successful) }}</h5>
                        </div>
                        <span class="arrow--btn position-absolute"><i class="fa-solid fa-chevron-right"></i></span>
                    </a>
                </div>

                <div class="col-sm-6 col-xxl-3 col-xl-3">
                    <a class="dashboard-widget--card position-relative"
                        href="{{ route('admin.deposit.log', ['status' => 'pending']) }}">
                        <div class="dashboard-widget__icon">
                            <i class="dashboard-card-icon fa-solid fa-hourglass-half"></i>
                        </div>
                        <div class="dashboard-widget__content">
                            <span class="title">@lang('Pending Deposit')</span>
                            <h5 class="number">{{ __($general->cur_sym) }}{{ showAmount($pending) }}</h5>
                        </div>
                        <span class="arrow--btn position-absolute"><i class="fa-solid fa-chevron-right"></i></span>
                    </a>
                </div>

                <div class="col-sm-6 col-xxl-3 col-xl-3">
                    <a class="dashboard-widget--card position-relative"
                        href="{{ route('admin.deposit.log', ['status' => 'rejected']) }}">
                        <div class="dashboard-widget__icon">
                            <i class="dashboard-card-icon fa-solid fa-ban"></i>
                        </div>
                        <div class="dashboard-widget__content">
                            <span class="title">@lang('Rejected Deposit')</span>
                            <h5 class="number">{{ __($general->cur_sym) }}{{ showAmount($rejected) }}</h5>
                        </div>
                        <span class="arrow--btn position-absolute"><i class="fa-solid fa-chevron-right"></i></span>
                    </a>
                </div>

                <div class="col-sm-6 col-xxl-3 col-xl-3">
                    <a class="dashboard-widget--card position-relative"
                        href="{{ route('admin.deposit.log', ['status' => 'initiated']) }}">
                        <div class="dashboard-widget__icon">
                            <i class="dashboard-card-icon fa-solid fa-check-to-slot"></i>
                        </div>
                        <div class="dashboard-widget__content">
                            <span class="title">@lang('Initiated Deposit')</span>
                            <h5 class="number">{{ __($general->cur_sym) }}{{ showAmount($initiated) }}</h5>
                        </div>
                        <span class="arrow--btn position-absolute"><i class="fa-solid fa-chevron-right"></i></span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row gy-4 justify-content-between mb-3 pb-3">
        <div class="col-lg-5 ">
            <form>
                <div class="row gy-4">
                    <div class="col-md-6">
                        <div class="search-input--wrap position-relative">
                            <input type="search" name="search" class="form-control" placeholder="@lang('Search by username')"
                                value="{{ request()->search }}">
                            <button class="search--btn position-absolute" type="submit"><i class="fa fa-search"></i></button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="search-input--wrap position-relative">
                            <input name="date" type="text" data-range="true" data-multiple-dates-separator=" - "
                                data-language="en" class="datepicker-here form-control" data-position='bottom right'
                                placeholder="@lang('Date from - to')" autocomplete="off" value="{{ request()->date }}">
                            <button class="search--btn position-absolute" type="submit"><i class="fa fa-search"></i></button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="col-xl-2 col-lg-6">
            <div class="d-flex justify-content-end">
                <select id="table__data__filter" name="status" class="form-control form-select bg--transparent outline">
                    <option value="all" {{ request()->status == 'all' ? 'selected' : '' }}>@lang('All')</option>
                    <option value="1" {{ request()->status == '1' ? 'selected' : '' }}>@lang('Approved')</option>
                    <option value="2" {{ request()->status == '2' ? 'selected' : '' }}>@lang('Pending')</option>
                    <option value="3" {{ request()->status == '3' ? 'selected' : '' }}>@lang('Rejected')</option>
                    <option value="0" {{ request()->status == '0' ? 'selected' : '' }}>@lang('Initiated')</option>
                </select>
            </div>
        </div>
    </div>

    <div class="row justify-content-center gy-4">
        <div class="col-md-12">
            <div class="card b-radius--10">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('User')</th>
                                    <th>@lang('Gateway')||@lang('Trx')</th>
                                    <th>@lang('Amount')||@lang('Charge')</th>
                                    <th>@lang('Created at')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody id="items_table__body">
                                @include('admin.components.tables.deposit_data')
                            </tbody>
                        </table>
                    </div>
                </div>

                <div id="pagination-wrapper" class="pagination__wrapper py-4 {{ $items->hasPages() ? '' : 'd-none' }}">
                    @if ($items->hasPages())
                        {{ paginateLinks($items) }}
                    @endif
                </div>

            </div>
        </div>
    </div>
@endsection

@push('style')
    <style>
        .nav-tabs {
            border: 0;
        }

        .nav-tabs li a {
            border-radius: 4px !important;
        }
    </style>
@endpush




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
            'use strict';

            if (!$('.datepicker-here').val()) {
                $('.datepicker-here').datepicker();
            }


            let baseUrl = `{{ route('admin.deposit.log', ':status') }}`;

            $('#table__data__filter').on('change', function() {
                let statusMap = {
                    '1': 'approved',
                    '2': 'pending',
                    '3': 'rejected',
                    '0': 'initiated',
                    'all': 'all'
                };

                let status = $(this).val();
                let url = baseUrl.replace(':status', statusMap[status] ?? 'all');

                $.ajax({
                    url: url,
                    method: 'GET',
                    data: {
                        search: $('#search-box').val()
                    },
                    beforeSend: function() {
                        $('#items_table__body').html(
                            '<tr><td colspan="6">@lang('Loading')...</td></tr>');
                    },
                    success: function(response) {
                        $('#items_table__body').html(response.html);
                        $('.card-footer').html(response.pagination);

                        if ($.trim(response.pagination) === '') {
                            $('#pagination-wrapper').addClass('d-none');
                        } else {
                            $('#pagination-wrapper').removeClass('d-none');
                        }
                    },
                    error: function() {
                        alert('Failed to load filtered tickets.');
                    }
                });
            });

        })(jQuery);
    </script>
@endpush
