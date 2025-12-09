@forelse($items as $deposit)
    @php
        $details = $deposit->detail ? json_encode($deposit->detail) : null;
    @endphp
    <tr>
        <td class="user--td">
            <div class="d-flex justify-content-between justify-content-lg-start gap-3">

                <div class="user--info d-flex gap-3 flex-shrink-0 align-items-center flex-wrap flex-md-nowrap">
                    <div class="user--thumb-two">
                        @if(!empty($deposit->user->image))
                            <img src="{{ getImage(getFilePath('userProfile') . '/' . $deposit?->user?->image ) }}" alt="@lang('Image')">
                        @else
                            <img src="{{ getImage('assets/images/general/avatar.png') }}" alt="@lang('Image')">
                        @endif
                    </div>
                    <div class="user--content">
                        <a class="text-start text-dark" href="{{ appendQuery('search', $deposit?->user?->username) }}">
                            {{ __($deposit->user?->fullname ?? 'N/A') }}
                        </a>
                        <br>
                        <a href="{{ route('admin.users.detail', $deposit?->user_id) }}" class="text-start">{{ '@'.__($deposit->user?->username ?? 'N/A') }}</a>
                    </div>
                </div>
            </div>
        </td>

        <td>
            {{ __($deposit->gateway?->name ?? 'N/A') }}
            <br>
            <span class="fw--500">{{ __($deposit->trx) }}</span>
        </td>

        <td>
            <span title="@lang('Amount without charge')">
                {{ showAmount($deposit->amount) }}
                {{ __($general->cur_text) }}
            </span><br>
            <span class="fw--500" title="@lang('Charge')">
                {{ showAmount($deposit->charge) }}
                {{ __($general->cur_text) }}
            </span>
        </td>


        <td>
            {{ showDateTime($deposit->created_at) }}
        </td>

        <td>
            @php echo $deposit->statusBadge @endphp
        </td>

        <td>
            <a title="@lang('Details')" href="{{ route('admin.deposit.details', $deposit->id) }}" class="btn btn-sm ms-1">
                <i class="fa-solid fa-eye"></i>
            </a>
        </td>
    </tr>
@empty
    <tr>
        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
    </tr>
@endforelse
