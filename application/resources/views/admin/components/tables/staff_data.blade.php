@forelse ($items as $loop=>$admin)
    <tr>
         <td class="user--td">
            <div class="d-flex justify-content-between justify-content-lg-start gap-3">
                <div class="user--info d-flex gap-3 flex-shrink-0 align-items-center flex-wrap flex-md-nowrap">
                    <div class="user--thumb-two">
                        @if(!empty($admin->image))
                            <img src="{{ getImage(getFilePath('adminProfile') . '/' . $admin->image ) }}" alt="@lang('Image')">
                        @else
                            <img src="{{ getImage('assets/images/general/avatar.png') }}" alt="@lang('Image')">
                        @endif
                    </div>
                    <div class="user--content">
                        <p>{{ $admin->name }}</p>
                        <p class="text-start">{{ '@'.$admin->username }}</p>
                    </div>
                </div>
            </div>
        </td>

        <td>{{ $admin->email }}</td>

        <td>
            <div class="button--group">
                @if($admin->supperAdmin())
                    <a href="javascript:void(0)" title="@lang('Not Allow')" class="btn btn-sm">
                        <i class="fa-solid fa-ban"></i>
                    </a>
                @else
                    <a href="{{ route('admin.staff.setup', $admin->id) }}" title="@lang('Setup')" class="btn btn-sm">
                        <i class="fa-solid fa-screwdriver-wrench"></i>
                    </a>

                    <a href="{{ route('admin.staff.login', $admin->id) }}" class="btn btn-sm" title="@lang('Login')">
                        <i class="fa-solid fa-right-to-bracket"></i>
                    </a>

                    <a href="{{ route('admin.staff.edit', $admin->id) }}" class="btn btn-sm" title="@lang('Edit')">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>

                    <button title="@lang('Delete')" type="button"
                        class="btn btn-sm btn--danger confirmationBtn"
                        data-action="{{ route('admin.staff.delete', $admin->id) }}"
                        data-question="@lang('Are you sure to delete this staff?')">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                @endif
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
    </tr>
@endforelse
