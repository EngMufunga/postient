@extends('Template::layouts.master')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <form action="{{ route('user.credit.refill.confirm') }}" method="post" id="creditForm">
                @csrf
                <div class="card custom--card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">@lang('Image Credit Refill')</h5>
                        <div>
                            <span class="fw-bold">@lang('Wallet Balance'): </span>
                            <span id="walletBalance">{{ showAmount(auth()->user()->balance, 2) }} {{ $general->cur_text }}</span>
                            <a href="{{ route('user.deposit') }}" class="btn btn--sm btn--base ms-2">
                                @lang('Add Balance')
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <h6 class="mb-3">@lang('Per credit price'): <strong>{{ gs('per_credit_price') }} {{ gs('cur_text') }}</strong></h6>

                        <div class="form-group mb-3">
                            <label class="form-label">@lang('Number of Credit')</label>
                            <input type="number" name="credit" id="creditInput" class="form-control form--control"
                                value="{{ old('credit', 0) }}" min="0" step="1">
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label">@lang('Amount')</label>
                            <div class="input-group">
                                <input type="number" step="any" id="amountInput" class="form-control form--control credit-refill" value="0" readonly>
                                <span class="input-group-text">{{ $general->cur_text }}</span>
                            </div>
                        </div>

                        <button type="submit" class="btn btn--base w-100 mt-3">@lang('Purchase')</button>

                        <p class="text-danger mt-2">
                            @lang('Your payment will be processed only in wallet balance. Before purchase make sure that you have enough balance in your wallet.')
                        </p>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('style')
    <style>
        .input-group .credit-refill.form-control[readonly] {
            background: none !important;
            cursor: not-allowed;
        }
        .dashboard .credit-refill.form-control {
            background: none !important;
            cursor: not-allowed;
        }
        .credit-refill.form-control:disabled, .credit-refill.form-control[readonly] {
                background: none !important;
                cursor: not-allowed;
            opacity: 1;
        }

    </style>
@endpush

@push('script')
    <script>
        (function ($) {
            "use strict";
            const perCreditPrice = parseFloat("{{ gs('per_credit_price') }}");
            const walletBalance = parseFloat("{{ auth()->user()->balance ?? 0 }}");

            function updateAmount() {
                let credit = parseInt($('#creditInput').val()) || 0;
                let amount = (credit * perCreditPrice).toFixed(2);
                $('#amountInput').val(amount);

                // Check against wallet balance
                if (parseFloat(amount) > walletBalance) {
                    notify('error', '@lang("Your wallet balance is not enough to complete this purchase.")');
                }
            }

            // Update on input change
            $('#creditInput').on('input', updateAmount);

            // Initialize on page load
            updateAmount();

            // Optional: prevent form submission if balance is insufficient
            $('#creditForm').on('submit', function(e) {
                let amount = parseFloat($('#amountInput').val());
                if (amount > walletBalance) {
                    e.preventDefault();
                    notify('error', '@lang("You cannot purchase credits. Your wallet balance is insufficient.")');
                }
            });
        })(jQuery);
    </script>
@endpush



