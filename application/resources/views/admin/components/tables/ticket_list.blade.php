@forelse($items as $item)
    <tr>
        <td>
            <a href="{{ route('admin.ticket.view', $item->id) }}"
                class="fw--500 text-muted">
                @lang('Ticket')#{{ $item->ticket }} - {{ strLimit($item->subject, 30) }}
            </a>
        </td>

        <td>
            @if ($item->user_id)
                <a href="{{ route('admin.users.detail', $item->user_id) }}">
                    {{ $item->user->fullname ?? 'N/A' }}
                </a>
            @else
                <p class="fw--500"> {{ $item->name }}</p>
            @endif
        </td>

        <td>
            @php echo $item->priorityBadge; @endphp
        </td>

        <td>
            @php echo $item->statusBadge; @endphp
        </td>

        <td>
            {{ showDateTime($item->created_at) }}
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
