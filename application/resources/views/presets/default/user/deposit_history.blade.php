@extends('Template::layouts.master')
@section('content')
<div class="dashboard__table">
    <table class="table table--responsive--md">
        <thead>
            <tr>
                <th>@lang('Gateway')</th>
                <th>@lang('Trx')</th>
                <th class="text-center">@lang('Amount')</th>
                <th class="text-center">@lang('Initiated')</th>
                <th class="text-center">@lang('Status')</th>
                <th>@lang('Details')</th>
            </tr>
        </thead>
        <tbody>
            @forelse($deposits as $deposit)
            <tr>
                <td>
                    <span class="text--base">{{__($deposit->gateway?->name) }}</span>
                </td>
                <td>
                    {{$deposit->trx }}
                </td>
                <td class="text-center">
                    <strong title="@lang('Amount with charge')">
                        {{ showAmount($deposit->amount+$deposit->charge) }} {{
                        __($general->cur_text) }}
                    </strong>
                </td>
                <td class="text-center">
                    {{ showDateTime($deposit->created_at) }}
                </td>
                <td class="text-center">
                    @php echo $deposit->statusBadge @endphp
                </td>
                @php
                $details = ($deposit->detail != null) ? json_encode($deposit->detail) : null;
                @endphp

                <td>
                    <a href="javascript:void(0)"
                        class="btn btn--base btn--sm @if($deposit->method_code >= 1000) detailBtn @else disabled @endif"
                        @if($deposit->method_code >= 1000)
                        data-info="{{ $details }}"
                        @endif
                        @if ($deposit->status == 3)
                        data-admin_feedback="{{ $deposit->admin_feedback }}"
                        @endif
                        >
                        <i class="fa fa-eye"></i>
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="100%" class="text-center">{{ __($emptyMessage) }}</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($deposits->hasPages())
<div class="row">
    <div class="col-lg-12">
        {{ $deposits->links() }}
    </div>
</div>
@endif

{{-- APPROVE MODAL --}}
<div id="detailModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Deposit Details')</h5>
                <span type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fa-solid fa-xmark"></i>
                </span>
            </div>
            <div class="modal-body">
                <ul class="list-group userData mb-2">
                </ul>
                <div class="feedback"></div>
            </div>
        </div>
    </div>
</div>

@endsection


@push('breadcrumb-plugins')
    <form>
        <div class="search__box">
            <input type="search" class="form-control" name="search" value="{{ request()->search }}" placeholder="@lang('Search by transactions')">
            <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
        </div>
    </form>
@endpush


@push('script')
<script>
    (function ($) {
        "use strict";
        $('.detailBtn').on('click', function () {
            var modal = $('#detailModal');

            var userData = $(this).data('info');
            var html = '';
            if (userData) {
                userData.forEach(element => {
                    if (element.type != 'file') {
                        html += `
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>${element.name}</span>
                                <span">${element.value}</span>
                            </li>`;
                    }
                });
            }

            modal.find('.userData').html(html);

            if ($(this).data('admin_feedback') != undefined) {
                var adminFeedback = `
                        <div class="my-3">
                            <strong>@lang('Admin Feedback')</strong>
                            <p>${$(this).data('admin_feedback')}</p>
                        </div>
                    `;
            } else {
                var adminFeedback = '';
            }

            modal.find('.feedback').html(adminFeedback);


            modal.modal('show');
        });
    })(jQuery);

</script>
@endpush
