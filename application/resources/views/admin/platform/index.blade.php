@extends('admin.layouts.app')

@section('panel')
    <div class="row gy-4 justify-content-between mb-3 pb-3">
        <div class="col-xl-4 col-md-6">
            <div class="d-flex flex-wrap justify-content-start w-100">
                <form class="form-inline w-100">
                    <div class="search-input--wrap position-relative">
                        <input type="text" name="search" class="form-control" placeholder="@lang('Search')..." value="{{ request()->search ?? '' }}">
                        <button class="search--btn position-absolute"><i class="fa fa-search"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="row gy-4">
        <div class="col-md-12 mb-30">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two custom-data-table">
                            <thead>
                                <tr>
                                    <th>@lang('Platform')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody id="items_table__body">
                                    @forelse ($items as $item)
                                    <tr>
                                        <td>
                                            <div class="platform--pt">
                                                <img src="{{ getImage(getFilePath('platform') . '/'. $item->image, getFileSize('platform')) }}" alt="@lang('Platform')" class="image-thumbnail">
                                                {{__($item->name)}}
                                            </div>
                                        </td>

                                        <td>
                                            @php echo $item->statusBadge; @endphp
                                        </td>

                                        <td>
                                            <div class="d-flex justify-content-end align-items-center gap-2">
                                                <div class="form-group mb-0">
                                                    <label class="switch m-0" title="@lang('Change Status')">
                                                        <input type="checkbox" class="toggle-switch confirmationBtn" data-action="{{ route('admin.platform.status', $item->id) }}"
                                                        data-question="@lang('Are you sure to change platform status from this system?')" @checked($item->status)>
                                                        <span class="slider round"></span>
                                                    </label>
                                                </div>

                                                <button title="@lang('Edit')" type="button" class="btn btn-sm editBtn" data-image="{{ getImage(getFilePath('platform').'/'.$item->image, getFileSize('platform')) }}" data-action="{{ route('admin.platform.update', $item->id) }}">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </button>
                                            </div>
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

                <div id="pagination-wrapper"  class="pagination__wrapper py-4 {{ $items->hasPages() ? '' : 'd-none' }}">
                    @if ($items->hasPages())
                    {{ paginateLinks($items) }}
                    @endif
                </div>

            </div>
        </div>
    </div>

    {{-- ROLE MODAL --}}
    <div id="editModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Platform')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <div class="image-upload">
                                <div class="thumb">
                                    <div class="avatar-preview">
                                        <div class="profilePicPreview imageModalUpdate">
                                            <button type="button" class="remove-image">
                                                <i class="fa fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="avatar-edit">
                                        <input type="file" class="profilePicUpload" name="image" id="fileUploader3" accept=".png, .jpg, .jpeg">
                                        <label for="fileUploader3" class="bg--primary text--white">@lang('Upload Image')</label>

                                        <p class="text-center">@lang('Supported Files:') <span class="text-black">@lang('.png, .jpg, .jpeg')</span>.
                                            @lang('Image will be resized into') <span class="text-black">{{ getFileSize('platform') }} @lang('px')</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>


                    </div>
                    <div class="modal-footer">
                        <button class="btn btn--primary w-100 h-45" id="editBtn" type="submit">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <x-confirmation-modal></x-confirmation-modal>
@endsection


@push('script')
    <script>
        (function ($) {
            'use strict';
            $(document).on('click', '.editBtn', function() {
                var modal = $('#editModal');
                modal.find('.modal-title').text("@lang('Update Platform')");
                modal.find('form').attr('action', $(this).data('action'));
                modal.find('.image-upload .avatar-preview .profilePicPreview.imageModalUpdate').css('background-image', 'url(' + $(this).data('image') + ')');
                modal.find('#editBtn').text("@lang('Update')");
                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush
