@forelse($items as $gateway)
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
             @php echo $gateway->statusBadge; @endphp
        </td>
        <td>
            <div class="d-flex justify-content-end align-items-center gap-2">
                <div class="form-group mb-0">
                    <label class="switch m-0" title="@lang('Change Status')">
                        <input type="checkbox" class="toggle-switch confirmationBtn" data-action="{{ route('admin.gateway.manual.status',$gateway->code) }}"
                        data-question="@lang('Are you sure to change gateway status from this system?')" @checked($gateway->status)>
                        <span class="slider round"></span>
                    </label>
                </div>

                <a title="@lang('Edit')"
                    href="{{ route('admin.gateway.manual.edit', $gateway->alias) }}"
                    class="btn btn--sm editGatewayBtn">
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
