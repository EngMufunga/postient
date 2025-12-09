@forelse($items->sortBy('alias') as $k=>$gateway)
    <tr>
        <td>
            <div class="d-flex align-items-center justify-content-start gap-3">
                <div class="gateway--thumb">
                    @if(!empty($gateway?->image))
                        <img src="{{ getImage(getFilePath('paymentGateway') . '/' . $gateway?->image ) }}" alt="@lang('Image')">
                    @else
                        <img src="{{ getImage('assets/images/general/default.png') }}" alt="@lang('Image')">
                    @endif
                </div>

                <h6>
                    {{ __($gateway->name ?? '') }}
                </h6>
            </div>
        </td>

        <td>
            @php
                $supportedCurrencies = collect($gateway->supported_currencies)->except($gateway->currencies->pluck('currency'));
            @endphp
            <span>
                {{ $gateway->currencies()->count() }}
                @if($gateway->currencies->count() > 0)
                    <i data-bs-toggle="modal" data-bs-target="#currencyModal" class="fa-solid fa-caret-down enabled_currency c-pointer"  data-enabled_currency="{{ $gateway->currencies->pluck('currency')->toJson()}}" data-supported_currency="{{ collect($gateway->supported_currencies)->keys()->toJson() }}"></i>
                @endif
                /
                {{ $supportedCurrencies->count() }}
                <i data-bs-toggle="modal" data-bs-target="#currencyModal" class="fa-solid fa-caret-down supported_currency c-pointer" data-supported_currency="{{ collect($gateway->supported_currencies)->keys()->toJson() }}"></i>
            </span>
        </td>

        <td>
             @php echo $gateway->statusBadge; @endphp
        </td>

        <td>
            <div class="d-flex justify-content-end align-items-center gap-2">
                <div class="form-group mb-0">
                    <label class="switch m-0" title="@lang('Change Status')">
                        <input type="checkbox" class="toggle-switch confirmationBtn" data-action="{{ route('admin.gateway.automatic.status', $gateway->code) }}"
                        data-question="@lang('Are you sure to change gateway status from this system?')" @checked($gateway->status)>
                        <span class="slider round"></span>
                    </label>
                </div>

                <a title="@lang('Edit')"
                    href="{{ route('admin.gateway.automatic.edit', $gateway->alias) }}"
                    class="btn btn--sm edit--btn editGatewayBtn">
                    <i class="fa-solid fa-pen-to-square"></i>
                </a>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
    </tr>
@endforelse
