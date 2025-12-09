@extends('admin.layouts.app')
@section('panel')

<div class="row">
    <div class="col-lg-12">
        <div class="card b-radius--10 ">
            <div class="card-body p-0">
                <div class="table-responsive--md  table-responsive">
                    <table class="table table--light style--two">
                        <thead>
                            <tr>
                                <th>@lang('S.L')</th>
                                <th>@lang('Section Title')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody id="items_table__body">
                            @forelse($sections as $key=>$data)
                            <tr>
                                <td class="user--td">
                                    {{ $loop->iteration }}
                                </td>

                                <td>
                                    {{ __($data->name) }}
                                </td>

                                <td>
                                    <button type="button" title="@lang('Edit')" class="btn btn-sm edit" data-title="{{$data->name}}" data-name="{{ $key }}" data-action="{{ route('admin.custom.section.update', $key) }}">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>

                                    <button title="@lang('Delete')" type="button"
                                        class="btn btn-sm btn--danger confirmationBtn"
                                        data-action="{{ route('admin.custom.section.delete', $key) }}"
                                        data-question="@lang('Are you sure to delete this section?')">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                            </tr>
                            @endforelse

                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>



<div class="modal fade" id="menuModal" tabindex="-1" aria-labelledby="bulkActionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="menuForm" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 id="bulkActionModalLabel" class="modal-title">@lang('Add new menu')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="@lang('Close')"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 section_name">
                            <div class="form-group">
                                <label for="name"> @lang('Name'): (<small>@lang('Must be unique and small letter')</small>)</label>
                                <input type="text" class="form-control" name="name" id="name" value="{{ old('name') }}" placeholder="@lang('Section Name')" required>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="title"> @lang('Section Title'):</label>
                                <input type="text" class="form-control" name="title" id="title" value="{{ old('title') }}" placeholder="@lang('Section Title')" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn--primary submit_btn w-100">@lang('Save')</button>
                </div>
            </div>
        </form>
    </div>
</div>


<x-confirmation-modal></x-confirmation-modal>


@endsection

@push('breadcrumb-plugins')
<a class="btn btn--primary addModal" data-toggle="modal" data-action="{{ route('admin.custom.section.store') }}"><i class="fa-solid fa-plus"></i> @lang('Add Section')</a>
@endpush


@push('script')
<script>
    (function($) {
        'use strict';
        $('.addModal').on('click', function () {
            let action = $(this).data('action');
            $('#menuForm')[0].reset();
            $('#menuForm').attr('action', action);
            $('#menuModal .modal-title').text("@lang('Add Section')");
            $('#menuModal .submit_btn').text("@lang('Submit')");
            $('#menuModal').modal('show');
        });


        $('.edit').on('click', function () {
            let modal = $('#menuModal');
            let action = $(this).data('action');
            $('#menuForm')[0].reset();
            $('#menuForm').attr('action', action);
            $('#menuModal .modal-title').text("@lang('Update Section')");
            modal.find('input[name=name]').val($(this).data('name')).attr('readonly', true);
            modal.find('.section_name').hide();
            modal.find('input[name=name]').attr('required', false);
            modal.find('input[name=title]').attr('required', true);
            modal.find('input[name=title]').val($(this).data('title'));
            $('#menuModal .submit_btn').text("@lang('Update Changes')");
            $('#menuModal').modal('show');
        });

    })(jQuery);

</script>
@endpush










