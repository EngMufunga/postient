@forelse ($items as $role)
    <tr>
        <td>
            <span class="name">{{ __($role->name) }}</span>
        </td>
        <td>
            @php echo $role->statusBadge; @endphp
        </td>
        <td>
            <div class="d-flex justify-content-end align-items-center gap-2">
                <div class="form-group mb-0">
                    <label class="switch m-0" title="@lang('Change Status')">
                        <input type="checkbox" class="toggle-switch confirmationBtn" data-action="{{ route('admin.role.status', $role->id) }}"
                        data-question="@lang('Are you sure to change role status from this system?')" @checked($role->status)>
                        <span class="slider round"></span>
                    </label>
                </div>

                <button title="@lang('Edit')" type="button" class="btn btn-sm editBtn" data-name="{{ __($role->name) }}" data-action="{{ route('admin.role.store', $role->id) }}">
                    <i class="fa-solid fa-pen-to-square"></i>
                </button>

                <button title="@lang('Delete')" type="button"
                    class="btn btn-sm btn--danger confirmationBtn"
                    data-action="{{ route('admin.role.delete', $role->id) }}"
                    data-question="@lang('Are you sure to delete this role?')">
                    <i class="fa-solid fa-trash-can"></i>
                </button>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
    </tr>
@endforelse
