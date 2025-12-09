@extends('admin.layouts.app')
@section('panel')
    <div class="row gy-4">
        <div class="col-md-12 mb-30">
            <div class="card br--solid b-radius--10">
                <div class="card-body">
                    <h4 class="mb-3">@lang('Setup Permissions for Role'): {{ $role->name }} : {{ $staff->name }}</h4>

                    <form action="{{ route('admin.staff.setup.update', $staff->id) }}" method="POST">
                        @csrf
                        <div class="row">
                            @foreach($permissions as $group => $groupPermissions)
                                <div class="col-md-3 mb-4">
                                    <div class="border rounded p-3 h-100">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h6>{{ ucfirst($group) }}</h6>
                                        </div>

                                        @foreach($groupPermissions as $permission)
                                            <div class="form-check d-flex align-items-center gap-2">
                                                <input class="form-check-input m-0 permission-checkbox group-{{ Str::slug($group) }}"
                                                    type="checkbox"
                                                    name="permissions[]"
                                                    value="{{ $permission->id }}"
                                                    id="perm-{{ $permission->id }}"
                                                    {{ in_array($permission->id, $assignedPermissions) ? 'checked' : '' }}>
                                                <label class="form-check-label m-0" for="perm-{{ $permission->id }}">
                                                    {{ $permission->name }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="row">
                            <div class="col-lg-12 text-end">
                                <button type="submit" class="btn btn--primary mt-4">@lang('Save')</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <x-confirmation-modal></x-confirmation-modal>
@endsection


@push('breadcrumb-plugins')
    @if(auth()->guard('admin')->user()->supperAdmin())
        <button type="button" class="btn btn--primary me-2 confirmationBtn"  data-action="{{ route('admin.staff.seeder') }}" data-question="@lang('Are you sure to reset permission? If you reset permission, all permissions will be set to default.')"><i class="fa-solid fa-database"></i> @lang('Permission Reset')</button>
    @endif
    <button type="button" id="toggle-permissions" class="btn btn-sm btn--primary">
        @lang('Check All')
    </button>
@endpush

@push('script')
    <script>
        (function ($) {
            'use strict';
            let allChecked = false;

            $('#toggle-permissions').on('click',function () {
                allChecked = !allChecked;

                $('input[name="permissions[]"]').prop('checked', allChecked);

                $(this).text(allChecked ? 'Uncheck All' : 'Check All');
            });
        })(jQuery);
    </script>
@endpush


