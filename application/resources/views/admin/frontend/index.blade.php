@extends('admin.layouts.app')
@section('panel')

    @if (isset($section->content))
        <div class="row">
            <div class="col-lg-12 col-md-12 mb-30">
                <div class="card bg--white radius--base br--solid p-16">
                    <div class="card-body">
                        <form action="{{ route('admin.frontend.sections.content', $key) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="type" value="content">
                            <div class="row">
                                @php
                                    $imgCount = 0;
                                @endphp
                                @foreach ($section->content as $k => $item)
                                    @if ($k == 'images')
                                        @php
                                            $imgCount = collect($item)->count();
                                        @endphp
                                        @foreach ($item as $imgKey => $image)
                                            <div class="col-md-4">
                                                <input type="hidden" name="has_image" value="1">
                                                <div class="form-group">
                                                    <label>{{ __(keyToTitle($imgKey)) }}</label>
                                                    <x-image-uploader name="image_input[{{ $imgKey }}]" :imagePath="getImage('assets/images/frontend/' . $key . '/' . ($content->data_values->$imgKey ?? ''), $section->content->images->$imgKey->size)" :size="$section->content->images->$imgKey->size" :required="false" class="w-100" id="image-upload-input{{ $loop->index }}" />
                                                </div>
                                            </div>
                                        @endforeach
                                            <div class="@if ($imgCount > 1) col-md-12 @else col-md-8 @endif">
                                                @push('divend')
                                            </div>
                                        @endpush
                                    @else
                                        @if ($k != 'images')
                                            @if ($item == 'icon')
                                                <div class="col-md-12">
                                                    <div class="form-group ">
                                                        <label>{{ __(keyToTitle($k)) }}</label>
                                                        <div class="input-group">
                                                            <input type="text" class="form-control iconPicker icon" autocomplete="off" name="{{ $k }}" value="{{ $content->data_values->$k ?? '' }}" required>
                                                            <span class="input-group-text  input-group-addon" data-icon="las la-home" role="iconpicker">@php echo $content?->data_values?->$k @endphp</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            @elseif($item == 'textarea')
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label>{{ __(keyToTitle($k)) }}</label>
                                                        <textarea rows="10" class="form-control" name="{{ $k }}" required>{{ $content->data_values->$k ?? '' }}</textarea>
                                                    </div>
                                                </div>
                                            @elseif($item == 'textarea-rich')
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label>{{ __(keyToTitle($k)) }}</label>
                                                        <textarea rows="10" class="form-control trumEdit" name="{{ $k }}">{{ $content->data_values->$k ?? '' }}</textarea>
                                                    </div>
                                                </div>
                                            @elseif($k == 'select')
                                                @php
                                                    $selectName = $item->name;
                                                @endphp
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label>{{ __(keyToTitle($selectName)) }}</label>
                                                        <select class="form-control form-select" name="{{ $selectName }}">
                                                            @foreach ($item->options as $selectItemKey => $selectOption)
                                                                <option value="{{ $selectItemKey }}"  @if (isset($content) && $content->data_values->$selectName == $selectItemKey) selected @endif>{{ $selectOption }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label>{{ __(keyToTitle($k)) }}</label>
                                                        <input type="text" class="form-control" name="{{ $k }}" value="{{ $content->data_values->$k ?? '' }}" required />
                                                    </div>
                                                </div>
                                            @endif
                                        @endif
                                    @endif
                                @endforeach
                                @stack('divend')
                            </div>

                            <div class="form-group text-end">
                                <button type="submit" class="mt-2 btn btn--primary">@lang('Save Changes')</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif


    @if (isset($section->element))
        <div class="d-flex flex-wrap justify-content-end mb-3">
            <div class="d-inline">
                <div class="input-group justify-content-end">
                    <input type="text" name="search_table" class="form-control bg--white" placeholder="@lang('Search')...">
                    <button class="btn btn--primary input-group-text"><i class="fa fa-search"></i></button>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body p-0">
                        <div class="table-responsive--sm table-responsive">
                            <table class="table table--light style--two custom-data-table">
                                <thead>
                                    <tr>
                                        <th>@lang('SL')</th>
                                        @if(isset($section->element->images))
                                            <th>@lang('Image')</th>
                                        @endif
                                        @foreach ($section->element as $k => $type)
                                            @if ($k != 'modal')
                                                @if ($type == 'text' || $type == 'icon')
                                                    <th>{{ __(keyToTitle($k)) }}</th>
                                                @elseif($k == 'select')
                                                    <th>{{ keyToTitle($section->element->$k->name) }}</th>
                                                @endif
                                            @endif
                                        @endforeach
                                        <th>@lang('Action')</th>
                                    </tr>
                                </thead>
                                <tbody class="list">
                                    @forelse($elements as $data)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                          @if(isset($section->element->images))
                                          @php $firstKey = collect($section->element->images)->keys()[0]; @endphp
                                                <td>
                                                    <div class="customer-details d-block">
                                                        <a href="javascript:void(0)" class="thumb">
                                                            <img src="{{ getImage('assets/images/frontend/' . $key . '/' . ($data?->data_values->$firstKey ?? ''), $section?->element?->images?->$firstKey->size ?? '') }}" alt="@lang('image')">
                                                        </a>
                                                    </div>
                                                </td>
                                            @endif
                                            @foreach ($section->element as $k => $type)
                                                @if ($k != 'modal')
                                                    @if ($type == 'text' || $type == 'icon')
                                                        @if ($type == 'icon')
                                                            <td>@php echo $data->data_values->$k ?? ''; @endphp</td>
                                                        @else
                                                            <td>{{ __($data->data_values?->$k ?? '') }}</td>
                                                        @endif
                                                    @elseif($k == 'select')
                                                        @php
                                                            $dataVal = $section->element?->$k->name ?? '';
                                                        @endphp
                                                        <td>{{ $data->data_values->$dataVal }}</td>
                                                    @endif
                                                @endif
                                            @endforeach
                                            <td>
                                                <div class="button--group d-flex align-items-center justify-content-end gap-3">
                                                    @if ($section->element->modal)
                                                        @php
                                                            $images = [];
                                                            if (!empty($section->element->images)) {
                                                                foreach ($section->element->images as $imgKey => $imgs) {
                                                                    $images[] = getImage('assets/images/frontend/' . $key . '/' . ($data?->data_values->$imgKey ?? ''),$section->element->images->$imgKey->size);
                                                                }
                                                            }
                                                        @endphp
                                                        <button title="@lang('Edit')" class="edit--btn updateBtn" data-id="{{ $data->id }}" data-all="{{ json_encode($data->data_values) }}" @if (!empty($section->element->images)) data-images="{{ json_encode($images) }}" @endif>
                                                            <i class="fa-solid fa-pen-to-square"></i>

                                                        </button>
                                                    @else
                                                        <a title="@lang('Edit')" href="{{ route('admin.frontend.sections.element', [$key, $data->id]) }}" class="edit--btn">
                                                            <i class="fa-solid fa-pen-to-square"></i>
                                                        </a>
                                                    @endif
                                                    <button title="@lang('Remove')" class="btn btn-sm btn--danger confirmationBtn" data-action="{{ route('admin.frontend.remove', $data->id) }}" data-question="@lang('Are you sure to remove this item?')">
                                                        <i class="fa-solid fa-trash-can"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Add METHOD MODAL --}}
        <div id="addModal" class="modal fade" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"> @lang('Add New') {{ __(keyToTitle($key)) }} @lang('Item')</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">

                        </button>
                    </div>
                    <form action="{{ route('admin.frontend.sections.content', $key) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="type" value="element">
                        <div class="modal-body">
                            @foreach ($section->element as $k => $type)
                                @if ($k != 'modal')
                                    @if ($type == 'icon')
                                        <div class="form-group">
                                            <label>{{ __(keyToTitle($k)) }}</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control iconPicker icon" autocomplete="off" name="{{ $k }}" required>
                                                <span class="input-group-text  input-group-addon" data-icon="las la-home" role="iconpicker"></span>
                                            </div>
                                        </div>
                                    @elseif($k == 'select')
                                        <div class="form-group">
                                            <label>{{ keyToTitle($section->element->$k->name) }}</label>
                                            <select class="form-control form-select" name="{{ $section->element->$k->name }}">
                                                @foreach ($section->element->$k->options as $selectKey => $options)
                                                    <option value="{{ $selectKey }}">{{ $options }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @elseif($k == 'images')
                                        @foreach ($type as $imgKey => $image)
                                            <input type="hidden" name="has_image" value="1">
                                            <div class="form-group">
                                                <label>{{ __(keyToTitle($k)) }}</label>
                                                <div class="image-upload">
                                                    <div class="thumb">
                                                        <div class="avatar-preview">
                                                            <div class="profilePicPreview" style="background-image: url({{ getImage('/', $section->element->images->$imgKey->size) }})">
                                                                <button type="button" class="remove-image">
                                                                    <i class="fa fa-times"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <div class="avatar-edit">
                                                            <input type="file" class="profilePicUpload" name="image_input[{{ $imgKey }}]" id="addImage{{ $loop->index }}" accept=".png, .jpg, .jpeg">
                                                            <label for="addImage{{ $loop->index }}" class="bg--primary text--white">{{ __(keyToTitle($imgKey)) }}</label>
                                                            <small class="mt-2 ">
                                                                @if ($section->element->images->$imgKey->size)
                                                                    @lang('Recomended size:'):
                                                                    <b>{{ $section->element->images->$imgKey->size }}</b>
                                                                    @lang('px').
                                                                @endif
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @elseif($type == 'textarea')
                                        <div class="form-group">
                                            <label>{{ __(keyToTitle($k)) }}</label>
                                            <textarea rows="4" class="form-control" name="{{ $k }}" required></textarea>
                                        </div>
                                    @elseif($type == 'textarea-rich')
                                        <div class="form-group">
                                            <label>{{ __(keyToTitle($k)) }}</label>
                                            <textarea rows="4" class="form-control trumEdit" name="{{ $k }}"></textarea>
                                        </div>
                                    @else
                                        <div class="form-group">
                                            <label>{{ __(keyToTitle($k)) }}</label>
                                            <input type="text" class="form-control" name="{{ $k }}" required />
                                        </div>
                                    @endif
                                @endif
                            @endforeach
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn--primary">@lang('Save')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Update METHOD MODAL --}}
        <div id="updateBtn" class="modal fade" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"> @lang('Update') {{ __(keyToTitle($key)) }} @lang('Item')</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">

                        </button>
                    </div>
                    <form action="{{ route('admin.frontend.sections.content', $key) }}" class="edit-route" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="type" value="element">
                        <input type="hidden" name="id">
                        <div class="modal-body">
                            @foreach ($section->element as $k => $type)
                                @if ($k != 'modal')
                                    @if ($type == 'icon')
                                        <div class="form-group">
                                            <label>{{ keyToTitle($k) }}</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control iconPicker icon" autocomplete="off" name="{{ $k }}" required>
                                                <span class="input-group-text  input-group-addon" data-icon="las la-home" role="iconpicker"></span>
                                            </div>
                                        </div>
                                    @elseif($k == 'select')
                                        <div class="form-group">
                                            <label>{{ keyToTitle($section->element->$k->name) }}</label>
                                            <select class="form-control form-select" name="{{ $section->element->$k->name }}">
                                                @foreach ($section->element->$k->options as $selectKey => $options)
                                                    <option value="{{ $selectKey }}">{{ $options }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @elseif($k == 'images')
                                        @foreach ($type as $imgKey => $image)
                                            <input type="hidden" name="has_image" value="1">
                                            <div class="form-group">
                                                <label>{{ __(keyToTitle($k)) }}</label>
                                                <div class="image-upload">
                                                    <div class="thumb">
                                                        <div class="avatar-preview">
                                                            <div class="profilePicPreview imageModalUpdate{{ $loop->index }}">
                                                                <button type="button" class="remove-image">
                                                                    <i class="fa fa-times"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <div class="avatar-edit">
                                                            <input type="file" class="profilePicUpload" name="image_input[{{ $imgKey }}]" id="fileUploader{{ $loop->index }}" accept=".png, .jpg, .jpeg">
                                                            <label for="fileUploader{{ $loop->index }}" class="bg--primary text--white">{{ __(keyToTitle($imgKey)) }}</label>
                                                            <small class="mt-2  ">
                                                                @if ($section->element->images->$imgKey->size)
                                                                    @lang('Recomended size:'):
                                                                    <b>{{ $section->element->images->$imgKey->size }}</b>
                                                                    @lang('px').
                                                                @endif
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @elseif($type == 'textarea')
                                        <div class="form-group">
                                            <label>{{ keyToTitle($k) }}</label>
                                            <textarea rows="4" class="form-control" name="{{ $k }}" required></textarea>
                                        </div>
                                    @elseif($type == 'textarea-rich')
                                        <div class="form-group">
                                            <label>{{ keyToTitle($k) }}</label>
                                            <textarea rows="4" class="form-control trumEdit" name="{{ $k }}"></textarea>
                                        </div>
                                    @else
                                        <div class="form-group">
                                            <label>{{ keyToTitle($k) }}</label>
                                            <input type="text" class="form-control" name="{{ $k }}" required />
                                        </div>
                                    @endif
                                @endif
                            @endforeach
                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn btn--primary">@lang('Save')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @push('breadcrumb-plugins')
            @if ($section->element->modal)
                <a href="javascript:void(0)" class="btn btn-sm btn--primary addBtn">
                    <i class="fa-solid fa-plus"></i> @lang('Add New')
                </a>
            @else
                <a href="{{ route('admin.frontend.sections.element', $key) }}" class="btn btn-sm btn--primary">
                    <i class="fa-solid fa-plus"></i> @lang('Add New')
                </a>
            @endif
        @endpush
    @endif


    <x-confirmation-modal></x-confirmation-modal>

@endsection

@push('style-lib')
    <link href="{{ asset('assets/admin/css/fontawesome-iconpicker.min.css') }}" rel="stylesheet">
@endpush

@push('script-lib')
    <script src="{{ asset('assets/admin/js/fontawesome-iconpicker.js') }}"></script>
    <script src="{{ asset('assets/common/js/ckeditor.js') }}"></script>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            document.querySelectorAll('.trumEdit').forEach(element => {
                ClassicEditor
                    .create(element)
                    .then(editor => {
                        window.editor = editor;
                    })
                    .catch(error => {
                        console.error(error);
                    });
            });


            $('.addBtn').on('click', function() {
                var modal = $('#addModal');

                modal.modal('show');
            });
            $('.updateBtn').on('click', function() {

                var modal = $('#updateBtn');
                modal.find('input[name=id]').val($(this).data('id'));
                var obj = $(this).data('all');
                var images = $(this).data('images');
                if (images) {
                    for (var i = 0; i < images.length; i++) {
                        var imgloc = images[i];
                        $(`.imageModalUpdate${i}`).css("background-image", "url(" + imgloc + ")");
                    }
                }
                $.each(obj, function(index, value) {
                    if (index.toLowerCase().includes('icon')) {
                        modal.find('.input-group.iconpicker-container .input-group-text.input-group-addon').html(value);
                    }
                    modal.find('[name=' + index + ']').val(value);
                });
                modal.modal('show');
            });
            $('#updateBtn').on('shown.bs.modal', function(e) {
                $(document).off('focusin.modal');
            });
            $('#addModal').on('shown.bs.modal', function(e) {
                $(document).off('focusin.modal');
            });
            $('.iconPicker').iconpicker().on('iconpickerSelected', function(e) {
                $(this).closest('.form-group').find('.iconpicker-input').val(`<i class="${e.iconpickerValue}"></i>`);
            });

        })(jQuery);
    </script>
@endpush

@push('style')
<style>
    .ck-content p{
        margin-bottom: 20px !important;
    }
</style>
@endpush
