@forelse($items as $item)
    <tr>
        <td>
            <a href="{{ route('admin.ticket.view', $item->id) }}"
                class="fw--500 text--muted">
                @lang('Ticket')#{{ $item->ticket }} - {{ strLimit($item->subject, 30) }}
            </a>
        </td>

        <td>
            @if ($item->user_id)
                <a href="{{ route('admin.users.detail', $item->user_id) }}">
                    {{ $item->user?->fullname ?? 'N/A' }}</a>
            @else
                <p class="fw--500"> {{ $item->name }}</p>
            @endif
        </td>

        <td>
            @if ($item->priority == 1)
                <span class="badge badge--dark">@lang('Low')</span>
            @elseif($item->priority == 2)
                <span class="badge  badge--warning">@lang('Medium')</span>
            @elseif($item->priority == 3)
                <span class="badge badge--danger">@lang('High')</span>
            @endif
        </td>

        <td>
            @php echo $item->statusBadge; @endphp
        </td>

        <td>
            <a title="@lang('Details')" href="{{ route('admin.ticket.view', $item->id) }}"
                class="btn btn--sm edit--btn">
                <i class="fa-solid fa-eye"></i>
            </a>
        </td>
    </tr>
@empty
    <tr>
        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
    </tr>
@endforelse
