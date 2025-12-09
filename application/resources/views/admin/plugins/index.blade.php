@extends('admin.layouts.app')

@section('panel')

    <div class="row gy-4 justify-content-start mb-none-30">
        <div class="col-xxl-3 col-xl-3 col-lg-12">
            @include('admin.components.navigate_sidebar')
        </div>

        <div class="col-xxl-9 col-xl-9 col-lg-12 mb-30">
            <div class="row gy-4">
                <div class="col-md-12 mb-30">
                    <div class="card b-radius--10 ">
                        <div class="card-body p-0">
                            <div class="table-responsive--sm table-responsive">
                                <table class="table table--light style--two custom-data-table">
                                    <thead>
                                        <tr>
                                            <th>@lang('Plugin')</th>
                                            <th>@lang('Status')</th>
                                            <th>@lang('Action')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($plugins as $plugin)
                                            <tr>
                                                <td>
                                                    <div class="user">
                                                        <div class="thumb">
                                                            <img src="{{ getImage(getFilePath('extensions') . '/' . $plugin->image, getFileSize('extensions')) }}" alt="{{ __($plugin->name) }}" class="plugin_bg">
                                                        </div>
                                                        <span class="name">{{ __($plugin->name) }}</span>
                                                    </div>
                                                </td>

                                                <td>
                                                    @php echo $plugin->statusBadge; @endphp
                                                </td>

                                                <td>
                                                    <div class="d-flex justify-content-end align-items-center gap-2">
                                                        <div class="form-group mb-0">
                                                            <label class="switch m-0"  title="@lang('Change Status')">
                                                                <input type="checkbox" class="toggle-switch confirmationBtn" data-action="{{ route('admin.plugins.status', $plugin->id) }}"
                                                                data-question="@lang('Are you sure to change plugin status from this system?')" @checked($plugin->status)>
                                                                <span class="slider round"></span>
                                                            </label>
                                                        </div>

                                                        <button title="@lang('Edit')" type="button" class="btn btn-sm editBtn" data-name="{{ __($plugin->name) }}" data-shortcode="{{ json_encode($plugin->shortcode) }}" data-action="{{ route('admin.plugins.update', $plugin->id) }}">
                                                            <i class="fa-solid fa-pen-to-square"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- EDIT METHOD MODAL --}}
    <div id="editModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Update Plugin'): <span class="extension-name"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <form method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="col-md-12 control-label fw--500">@lang('Script')</label>
                            <div class="col-md-12">
                                <textarea name="script" class="form-control" required rows="8" placeholder="@lang('Paste your script with proper key')">{{ old('script') }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary w-100" id="editBtn">@lang('Save Changes')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <x-confirmation-modal></x-confirmation-modal>
@endsection


@push('script')
    <script>
        (function($) {
            "use strict";

            $(document).on('click', '.editBtn', function() {
                var modal = $('#editModal');
                var shortcode = $(this).data('shortcode');

                modal.find('.extension-name').text($(this).data('name'));
                modal.find('form').attr('action', $(this).data('action'));

                var html = '';
                $.each(shortcode, function(key, item) {
                    html += `<div class="form-group">
                        <label class="col-md-12 control-label fw--500">${item.title}</label>
                        <div class="col-md-12">
                            <input name="${key}" class="form-control" placeholder="--" value="${item.value}" required>
                        </div>
                    </div>`;
                })
                modal.find('.modal-body').html(html);

                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush
