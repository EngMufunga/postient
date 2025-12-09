@extends('admin.layouts.app')

@section('panel')
    <div class="row">

        <div class="col-lg-12">
            <div class="show-filter mb-3 text-end">
                <button type="button" class="btn btn--primary showFilterBtn btn-sm"><i class="fa-solid fa-filter"></i>
                    @lang('Filter')</button>
            </div>
            <div class="card br--solid p-16 radius--base bg--white responsive-filter-card mb-4 pb-4">

                <form class="pb-2" id="transaction-filter-form">
                    <div class="d-flex flex-wrap gap-4">
                        <div class="flex-grow-1">
                            <label>@lang('Transactions number or Username')</label>
                            <input type="text" name="search" value="{{ request()->search }}" class="form-control"
                                placeholder="@lang('Transactions number or Username')">
                        </div>
                        <div class="flex-grow-1">
                            <label>@lang('Remark')</label>
                            <select class="form-control form-select" name="remark">
                                <option value="">@lang('Any')</option>
                                @foreach ($remarks as $remark)
                                    <option value="{{ $remark->remark }}" @selected(request()->remark == $remark->remark)>
                                        {{ __(keyToTitle($remark->remark)) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex-grow-1">
                            <label>@lang('Type')</label>
                            <select name="trx_type" class="form-control form-select">
                                <option value="">@lang('All')</option>
                                <option value="+" @selected(request()->trx_type == '+')>@lang('Plus')</option>
                                <option value="-" @selected(request()->trx_type == '-')>@lang('Minus')</option>
                            </select>
                        </div>
                        <div class="flex-grow-1">
                            <label>@lang('Date')</label>
                            <input name="date" type="text" data-range="true" data-multiple-dates-separator=" - "
                                data-language="en" class="datepicker-here form-control" data-position='bottom right'
                                placeholder="@lang('Date from - to')" autocomplete="off" value="{{ request()->date }}">
                        </div>
                        <div class="flex-grow-1 align-self-end">
                            <button type="submit" class="btn btn--primary w-100"><i class="fas fa-check"></i>
                                @lang('Apply')</button>
                        </div>
                    </div>
                </form>

            </div>


            <div class="table-responsive--sm table-responsive">
                <table class="table table--light style--two">
                    <thead>
                        <tr>
                            <th>@lang('User')</th>
                            <th>@lang('Transaction number')</th>
                            <th>@lang('Date')</th>
                            <th>@lang('Amount')</th>
                            <th>@lang('Post Balance')</th>
                            <th>@lang('Details')</th>
                        </tr>
                    </thead>

                    <tbody id="items-table-body">
                        @forelse($items as $trx)
                            <tr>
                                <td class="user--td">
                                    <div class="d-flex justify-content-between justify-content-lg-start gap-3">
                                        <div class="user--info d-flex gap-3 flex-shrink-0 align-items-center flex-wrap flex-md-nowrap">
                                            <div class="user--thumb-two">
                                                @if(!empty($trx?->user?->image))
                                                    <img src="{{ getImage(getFilePath('userProfile') . '/' . $trx?->user?->image ) }}" alt="@lang('Image')">
                                                @else
                                                    <img src="{{ getImage('assets/images/general/avatar.png') }}" alt="@lang('Image')">
                                                @endif
                                            </div>
                                            <div class="user--content">
                                                <a class="text-start text-dark" href="{{ appendQuery('search', $trx->user->username) }}">
                                                    {{ $trx->user->fullname }}
                                                </a>
                                                <br>
                                                <a href="{{ route('admin.users.detail', $trx?->user_id) }}" class="text-start">{{ '@'.__($trx?->user?->username) }}</a>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <strong>{{ $trx->trx }}</strong>
                                </td>

                                <td>
                                    {{ showDateTime($trx->created_at) }}
                                </td>

                                <td class="budget">
                                    <span class="fw--500 {{ $trx->trx_type == '+' ? 'text--success' : 'text--danger' }}">
                                        {{ $trx->trx_type }} {{ showAmount($trx->amount) }} {{ $general->cur_text }}
                                    </span>
                                </td>

                                <td class="budget">
                                    {{ showAmount($trx->post_balance) }} {{ __($general->cur_text) }}
                                </td>

                                <td>{{ __($trx->details) }}</td>
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
        <div id="pagination-wrapper"  class="pagination__wrapper py-4 {{ $items->hasPages() ? '' : 'd-none' }}">
            @if ($items->hasPages())
            {{ paginateLinks($items) }}
            @endif
        </div>
    </div>
@endsection

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


