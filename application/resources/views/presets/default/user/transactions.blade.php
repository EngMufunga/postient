@extends('Template::layouts.master')
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="dashboard__table">
                <div class="row gy-4">
                    <div class="col-lg-12">
                        <form>
                            <div class="d-flex flex-wrap gap-4 align-items-end">
                                <div class="flex-grow-1">
                                    <label class="form-label">@lang('Transaction Number')</label>
                                    <input type="text" name="search" value="{{ request()->search }}" class="form-control">
                                </div>
                                <div class="flex-grow-1">
                                    <label class="form-label">@lang('Type')</label>
                                    <select name="trx_type" class="form-control form-select">
                                        <option value="">@lang('All')</option>
                                        <option value="+" @selected(request()->trx_type == '+')>@lang('Plus')</option>
                                        <option value="-" @selected(request()->trx_type == '-')>@lang('Minus')</option>
                                    </select>
                                </div>
                                <div class="flex-grow-1">
                                    <label class="form-label">@lang('Remark')</label>
                                    <select class="form-control form-select" name="remark">
                                        <option value="">@lang('Any')</option>
                                        @foreach($remarks as $remark)
                                        <option value="{{ $remark->remark }}" @selected(request()->remark ==
                                            $remark->remark)>{{ __(keyToTitle($remark->remark)) }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="flex-grow-1">
                                    <label class="form-label">@lang('Date')</label>
                                    <input name="date" type="text" data-range="true" data-multiple-dates-separator=" - " data-language="en" class="datepicker-here form-control" data-position='bottom right' placeholder="@lang('Date from - to')" autocomplete="off" value="{{ request()->date }}">
                                </div>

                                <div class="flex-grow-1 text-end">
                                    <button class="btn btn--base"><i class="fa-solid fa-filter"></i> @lang('Filter')</button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="col-lg-12">
                        <table class="table table--responsive--md">
                            <thead>
                                <tr>
                                    <th>@lang('Trx')</th>
                                    <th>@lang('Transacted')</th>
                                    <th>@lang('Amount')</th>
                                    <th>@lang('Post Balance')</th>
                                    <th>@lang('Detail')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions as $trx)
                                <tr>
                                    <td>
                                        {{ $trx->trx }}
                                    </td>

                                    <td>
                                        {{ showDateTime($trx->created_at) }}<br>{{ diffForHumans($trx->created_at)
                                            }}
                                    </td>

                                    <td class="budget">
                                        <span class="fw--500 @if($trx->trx_type == '+')text-success @else text-danger @endif">
                                            {{ $trx->trx_type }} {{showAmount($trx->amount)}} {{ $general->cur_text
                                                }}
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

                @if($transactions->hasPages())
                <div class="row gy-3">
                    <div class="col-lg-12">
                        {{ $transactions->links() }}
                    </div>
                </div>
                @endif
            </div>
        </div>
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
