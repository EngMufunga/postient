@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card bg--white br--solid radius--base p-16">
                <div class="card-body p-0">
                    <div class="row gy-4">
                        {{-- Selected Menu Items --}}
                        <div class="col-lg-6">
                            <div class="card bg--white br--solid radius--base p-16">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <div>
                                        <h3 class="card-title">{{ __($menu->name) }} @lang('Menu')</h3>
                                        <small>@lang('Drop menu items here to build or rearrange the menu.')</small>
                                    </div>
                                     <a class="btn btn--primary addModal" title="@lang('Add New Menu Item')" data-toggle="modal" data-action="{{ route('admin.menu.item.store', $menu->id) }}"><i class="fa-solid fa-plus"></i></a>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('admin.menu.assign.item.submit', $menu->id) }}" method="post">
                                        @csrf
                                        <ol class="simple_with_drop vertical" id="selected-sections">
                                            @foreach ($menu->items as $data)
                                                <li draggable="true" data-id="{{ $data->id }}" class="draggable-item">
                                                    <i class="fa-solid fa-arrows-up-down-left-right"></i>
                                                    <span class="ms-2">{{ __($data->title) }}</span>
                                                   <div class="edit-btn--wrap d-flex gap-2 align-items-center position-absolute">
                                                        <i class="fa-solid fa-pen-to-square text--primary editItemBtn" title="@lang('Edit Item')" data-title="{{$data->title}}" data-url="{{$data->url}}" data-linktype="{{$data->link_type}}" data-pageid="{{$data->page_id}}"  data-action="{{ route('admin.menu.item.update',$data->id) }}"></i>


                                                        <i class="fa-regular fa-trash-can text--danger confirmationBtn" title="@lang('Delete Item')" data-action="{{ route('admin.menu.item.delete',$data->id) }}" data-question="@lang('Are you sure to delete this item menu?')"></i>


                                                        <i class="fa-solid fa-xmark remove-icon text--warning" title="@lang('Remove Item')"></i>
                                                    </div>
                                                    <input type="hidden" name="menu_items[]" value="{{ $data->id }}">
                                                </li>
                                            @endforeach
                                        </ol>
                                        <div class="form-group text-end mt-3">
                                            <button type="submit" class="btn btn--primary">@lang('Update')</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- Available Menu Items --}}
                        <div class="col-lg-6">
                            <div class="card bg--white br--solid radius--base p-16">
                                <div class="card-header mb-4">
                                    <h3 class="card-title">@lang('Available Menu Items')</h3>
                                    <small>@lang('Drag items from the left and drop them into the menu area on the right.')</small>
                                </div>
                                <div class="card-body p-0">
                                    <ol class="simple_with_no_drop vertical" id="available-sections">
                                        @foreach ($items as $data)
                                            @php
                                                $selectedItems = $menu->items->pluck('id')->toArray();
                                            @endphp

                                            @if (!in_array($data->id, $selectedItems))
                                                <li draggable="true" data-id="{{ $data->id }}" class="draggable-item two">
                                                    <i class="fa-solid fa-arrows-up-down-left-right"></i>
                                                    <span class="ms-2">{{ __($data->title) }}</span>

                                                    {{-- remove icon removed --}}
                                                    <div class="edit-btn--wrap d-flex gap-2 align-items-center position-absolute">
                                                        <i class="fa-solid fa-pen-to-square text--primary editItemBtn" title="@lang('Edit Item')" data-title="{{$data->title}}" data-url="{{$data->url}}" data-linktype="{{$data->link_type}}" data-pageid="{{$data->page_id}}"  data-action="{{ route('admin.menu.item.update',$data->id) }}"></i>



                                                        <i class="fa-regular fa-trash-can text--danger confirmationBtn" title="@lang('Delete Item')" data-action="{{ route('admin.menu.item.delete',$data->id) }}" data-question="@lang('Are you sure to delete this item menu?')"></i>
                                                    </div>

                                                    <input type="hidden" name="menu_items[]" value="{{ $data->id }}">
                                                </li>
                                            @endif
                                        @endforeach
                                    </ol>

                                </div>
                            </div>
                        </div>
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
                        <div class="form-group">
                            <label> @lang('Link Type')</label>
                            <select class="select2-basic form-control form-select" name="link_type">
                                <option value="1" {{ old('1') ? 'selected' : '' }}>@lang('System Link')</option>
                                <option value="2" {{ old('2') ? 'selected' : '' }}>@lang('External URL Link')</option>
                                <option value="3" {{ old('3') ? 'selected' : '' }}>@lang('Page Link')</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="required">@lang('URL')</label>
                            <input class="form-control" type="text" name="url" required value="{{ old('url') }}">
                        </div>

                        <div class="form-group">
                            <label> @lang('Pages')</label>
                            <select class="select2-basic form-control form-select" name="page_id">
                                @foreach($pages as $page)
                                    <option value="{{ $page->id }}" {{ old('page_id') == $page->id ? 'selected' : '' }}>{{ __($page->name) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="required"> @lang('Title')</label>
                            <input class="form-control" type="text" name="title" required value="{{ old('title') }}">
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary w-100 submit_btn">@lang('Save')</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

<x-confirmation-modal></x-confirmation-modal>
@endsection



@push('script')
    <script>

        $(function () {
            'use strict';
            initDragDrop('#available-sections', '#selected-sections');
        });

        function initDragDrop(availableSelector, selectedSelector) {
            const $available = $(availableSelector);
            const $selected = $(selectedSelector);
            let draggedItem = null;
            let $placeholder = $('<li class="placeholder" style="height:60px;border:2px dashed #aaa;margin:8px 0;border-radius:6px;"></li>');

            // Drag start
            $(document).on('dragstart', '.draggable-item', function () {
                draggedItem = $(this);
                draggedItem.addClass('dragging');
            });

            // Drag end
            $(document).on('dragend', '.draggable-item', function () {
                draggedItem.removeClass('dragging');
                $placeholder.remove();
                draggedItem = null;
            });

            // Drag over on selected
            $selected.on('dragover', function (e) {
                e.preventDefault();
                if (!draggedItem) return;

                if ($selected.find('.draggable-item').length === 0) {
                    if (!$selected.find('.placeholder').length) {
                        $selected.append($placeholder);
                    }
                    return;
                }

                let $target = $(e.target).closest('.draggable-item');
                if ($target.length && $target[0] !== draggedItem[0]) {
                    if ($target.index() < draggedItem.index()) {
                        $target.before($placeholder);
                    } else {
                        $target.after($placeholder);
                    }
                } else {
                    if (!$selected.find('.placeholder').length) {
                        $selected.append($placeholder);
                    }
                }
            });

            // Drop handler
            $selected.on('drop', function (e) {
                e.preventDefault();
                if (!draggedItem) return;

                if ($placeholder.parent().length) {
                    $placeholder.replaceWith(draggedItem);
                } else {
                    $selected.append(draggedItem);
                }

                // Ensure remove icon exists
                if (!draggedItem.find('.remove-icon').length) {
                    let $wrap = draggedItem.find('.edit-btn--wrap');
                    if (!$wrap.length) {
                        $wrap = $('<div class="edit-btn--wrap d-flex gap-2 align-items-center position-absolute"></div>');
                        draggedItem.append($wrap);
                    }
                    $wrap.append('<i class="fa-solid fa-xmark remove-icon text--warning"></i>');
                }

                updateSelectedInputs();
            });

            // Available drop (send back)
            $available.on('dragover', function (e) { e.preventDefault(); });
            $available.on('drop', function (e) {
                e.preventDefault();
                if (!draggedItem) return;
                $available.append(draggedItem);
                draggedItem.find('.remove-icon').remove();
                updateSelectedInputs();
            });

            // Delete click
            $(document).on('click', '.remove-icon', function () {
                let $item = $(this).closest('li');
                $available.append($item);
                $item.find('.remove-icon').remove();
                updateSelectedInputs();
            });

            function updateSelectedInputs() {
                $selected.find('input[name="menu_items[]"]').remove();
                $selected.find('.draggable-item').each(function () {
                    $selected.append(`<input type="hidden" name="menu_items[]" value="${$(this).data('id')}">`);
                });
            }
        }

    </script>


    <script>
        (function($) {
            'use strict';

            $('.addModal').on('click', function () {
                let action = $(this).data('action');
                $('#menuForm')[0].reset();
                $('#menuForm').attr('action', action);
                $('#menuModal .modal-title').text("@lang('Add New Item')");
                $('#menuModal .submit_btn').text("@lang('Submit')");
                $('#menuModal').modal('show');
            });


            function toggleFields() {
                var linkType = $('select[name="link_type"]').val();

                if (linkType === '1' || linkType === '2') {
                    $('input[name="url"]').closest('.form-group').show().find('input').prop('required', true);
                    $('select[name="page_id"]').closest('.form-group').hide().find('select').prop('required', false).val('');

                    $('input[name="title"]').closest('.form-group').show().find('input').prop('required', true);
                } else if (linkType === '3') {
                    $('input[name="url"]').closest('.form-group').hide().find('input').prop('required', false).val('');
                    $('input[name="title"]').closest('.form-group').hide().find('input').prop('required', false).val('');

                    $('select[name="page_id"]').closest('.form-group').show().find('select').prop('required', true);
                }
            }

            toggleFields();

            $('select[name="link_type"]').on('change',function () {
                toggleFields();
            });

            $('.editItemBtn').on('click', function () {
                let modal = $('#menuModal');
                let action = $(this).data('action');

                $('#menuForm')[0].reset();
                $('#menuForm').attr('action', action);
                $('#menuModal .modal-title').text("@lang('Update Item')");
                $('#menuModal .submit_btn').text("@lang('Update')");

                modal.find('input[name=title]').val($(this).data('title'));
                modal.find('input[name=url]').val($(this).data('url'));
                modal.find('select[name=link_type]').val($(this).data('linktype')).trigger('change');
                modal.find('select[name=page_id]').val($(this).data('pageid')).trigger('change');

                $('#menuModal').modal('show');
            });

        })(jQuery);

    </script>

@endpush

@push('style')
    <style>
        ol {
            list-style: none;
            padding-left: 0;
            min-height: 100px;
        }

        ol li.draggable-item {
            background: #f8f9fa;
            padding: 16px 26px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 500;
            color: #373737 !important;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 16px;
            cursor: grab;
            transition: background 0.2s, transform 0.2s;
            position: relative;

            span {
                color: #373737 !important;
            }

            .fa-solid.fa-arrows-up-down-left-right {
                color: #373737 !important;
            }

            .edit-btn--wrap {
                top: 10px;
                right: 10px;
            }
        }

        ol li.draggable-item:hover {
            background: #e9ecef;
        }

        ol li.draggable-item.dragging {
            opacity: 0.7;
            transform: scale(0.98);
        }

        .remove-icon {
            color: #e53935;
            cursor: pointer;
            font-size: 18px;
        }

        .remove-icon:hover {
            color: #b71c1c;
        }

        .simple_with_drop {
            border: 2px dashed #ddd;
            padding: 10px;
            border-radius: 6px;
            display: flex;
            flex-direction: column;
            flex-wrap: wrap;
            justify-content: start;
            align-items: flex-start;
            gap: 16px;

            .draggable-item {
                width: 100%;

                &.two {
                    width: 100% !important;
                }

                .edit-btn--wrap {
                    top: 10px;
                    right: 10px;
                }
            }
        }

        .simple_with_no_drop {
            border: 2px dashed #ddd;
            padding: 10px;
            border-radius: 6px;
            display: flex;
            flex-wrap: wrap;
            justify-content: start;
            align-items: flex-start;
            gap: 16px;

            .draggable-item {
                width: 31.3%;
            }
        }

        @media screen and (max-width: 1440px) {
            ol li.draggable-item {
                &.two {
                    width: 47%;
                }
            }
        }
    </style>
@endpush
