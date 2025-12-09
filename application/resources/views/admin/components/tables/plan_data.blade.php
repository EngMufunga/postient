@forelse ($items as $loop=>$item)
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td>
            {{ __($item->name) }}
        </td>
        <td>{{ showAmount($item->price) }} {{ __($general->cur_text) }}</td>
        <td>@php echo $item->durationTypeBadge; @endphp</td>
        <td>@php echo $item->featureBadge; @endphp</td>
        <td>@php echo $item->scheduleBadge; @endphp</td>
        <td>
            @php echo $item->statusBadge; @endphp
        </td>
        <td>
            <div class="d-flex justify-content-end align-items-center gap-2">
                <div class="form-group mb-0">
                    <label class="switch m-0" title="@lang('Change Status')">
                        <input type="checkbox" class="toggle-switch confirmationBtn" data-action="{{ route('admin.plan.status', $item->id) }}"
                        data-question="@lang('Are you sure to change plan status from this system?')" @checked($item->status)>
                        <span class="slider round"></span>
                    </label>
                </div>

                <a href="{{ route('admin.plan.edit', $item->id) }}" title="@lang('Edit')" class="btn btn-sm">
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
