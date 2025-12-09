@extends('admin.layouts.app')
@section('panel')
<div class="row">
    <div class="col-md-12 mb-30">
        <div class="card bg--white br--solid radius--base p-16">
            <form action="{{ route('admin.frontend.sections.content', $key) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="type" value="element">

                @if (isset($data))
                    <input type="hidden" name="id" value="{{ $data->id }}">
                @endif

                <div class="row">
                    @php
                        $imgCount = 0;
                    @endphp

                    @foreach ($section->element as $k => $content)
                        @if ($k == 'images')
                            @php
                                $imgCount = collect($content)->count();
                            @endphp
                            @foreach ($content as $imgKey => $image)
                                <div class="col-md-4">
                                    <input type="hidden" name="has_image[]" value="1">
                                    <div class="form-group">
                                        <label>{{ __(keyToTitle($imgKey)) }}</label>

                                        <x-image-uploader name="image_input[{{ $imgKey }}]" :imagePath="getImage('assets/images/frontend/' . $key . '/'.($data->data_values->$imgKey ??''), $section->element->images->$imgKey->size)" :size="$section->element->images->$imgKey->size" class="w-100" id="image-upload-input{{ $loop->index }}" :required="false"/>
                                    </div>
                                </div>
                            @endforeach
                            <div class="@if ($imgCount > 1) col-md-12 @else col-md-8 @endif">
                                @push('divend')
                            </div>
                        @endpush
                    @elseif($content == 'icon')
                        @if (isset($data?->data_values))
                            <div class="form-group">
                                <label>{{ keyToTitle($k) }}</label>
                                <div class="input-group">
                                    <input type="text" class="form-control iconPicker icon" name="{{ $k }}" value="{{ $data->data_values->$k ?? '' }}" required />

                                    <span class="input-group-text  input-group-addon" data-icon="las la-home" role="iconpicker">@php echo $data->data_values->$k @endphp </span>
                                </div>
                            </div>
                        @else
                            <div class="form-group">
                                <label>{{ keyToTitle($k) }}</label>
                                <div class="input-group">
                                    <input type="text" class="form-control iconPicker icon" autocomplete="off" name="{{ $k }}" required>
                                    <span class="input-group-text  input-group-addon" data-icon="las la-home" role="iconpicker"></span>
                                </div>
                            </div>
                        @endif
                    @else
                        @if ($content == 'textarea')
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>{{ __(keyToTitle($k)) }}</label>
                                    <textarea rows="10" class="form-control" name="{{ $k }}" required>{{ $data->data_values->$k ?? '' }}</textarea>
                                </div>
                            </div>
                        @elseif($content == 'textarea-rich')
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>{{ __(keyToTitle($k)) }}</label>
                                    <textarea rows="10" class="form-control trumEdit" name="{{ $k }}">{{ $data->data_values->$k ?? '' }}</textarea>
                                </div>
                            </div>
                    @elseif($k == 'select')
                        @php
                            $selectName = $content->name;
                        @endphp
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>{{ __(keyToTitle($selectName)) }}</label>
                                <select class="form-control form-select" name="{{ $selectName }}" required>
                                    @foreach ($content->options as $selectItemKey => $selectOption)
                                        <option value="{{ $selectItemKey }}" @if (isset($data) && $data->data_values?->$selectName == $selectItemKey) selected @endif>{{ __($selectOption) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    @else
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>{{ __(keyToTitle($k)) }}</label>
                                <input type="text" class="form-control" name="{{ $k }}" value="{{ $data->data_values->$k ?? '' }}" required />
                            </div>
                        </div>
                    @endif
                    @endif
                    @endforeach

                    @stack('divend')
                </div>

                <div class="form-group text-end">
                    <button type="submit" class="btn btn--primary">@lang('Save')</button>
                </div>
            </form>
        </div>
    </div>
</div>
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
            $('.iconPicker').iconpicker().on('iconpickerSelected', function(e) {
                $(this).closest('.form-group').find('.iconpicker-input').val(`<i class="${e.iconpickerValue}"></i>`);
            });

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

        })(jQuery);
    </script>
@endpush


