@extends('admin.layouts.app')

@section('panel')
    <div class="row gy-4 justify-content-start mb-none-30">
        <div class="col-xxl-3 col-xl-3 col-lg-12">
            @include('admin.components.navigate_sidebar')
        </div>

        <div class="col-xxl-9 col-xl-9 col-lg-12 mb-30">
            <div class="row">
                <div class="col-lg-12 col-md-12 mb-30">
                    <div class="card p-16 br--solid radius--base bg--white">
                        <div class="card-body">
                            <form action="{{ route('admin.frontend.sections.content', 'seo')}}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="type" value="data">
                                <input type="hidden" name="seo_image" value="1">
                                <div class="row">
                                    <div class="col-xl-4">
                                        <x-image-uploader name="image_input" :imagePath="getImage(getFilePath('seo').'/'. $seo->data_values->image,getFileSize('seo'))" :size="getFileSize('seo')" :isImage="true" class="w-100" id="uploadLogo3" :required="false" />
                                    </div>

                                    <div class="col-xl-8 mt-xl-0 mt-4">
                                        <div class="form-group ">
                                            <label>@lang('Meta Keywords')</label>
                                            <small class="ms-2 mt-2  ">@lang('Separate multiple keywords by')
                                                <code>,</code>(@lang('comma')) @lang('or') <code>@lang('enter')</code>
                                                @lang('key')</small>
                                            <select name="keywords[]" class="form-control select2-auto-tokenize" multiple="multiple"
                                                required>
                                                @if($seo->data_values->keywords)
                                                @foreach($seo->data_values->keywords as $option)
                                                <option value="{{ $option }}" selected>{{ __($option) }}</option>
                                                @endforeach
                                                @endif
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label>@lang('Meta Description')</label>
                                            <textarea name="description" rows="3" class="form-control"
                                                required>{{ $seo->data_values->description }}</textarea>
                                        </div>
                                        <div class="form-group">
                                            <label>@lang('Social Title')</label>
                                            <input type="text" class="form-control" name="social_title"
                                                value="{{ $seo->data_values->social_title }}" required>
                                        </div>
                                        <div class="form-group">
                                            <label>@lang('Social Description')</label>
                                            <textarea name="social_description" rows="3" class="form-control"
                                                required>{{ $seo->data_values->social_description }}</textarea>
                                        </div>
                                        <div class="form-group text-end">
                                            <button type="submit" class="btn btn--primary">@lang('Save Changes')</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('script-lib')
    <script src="{{asset('assets/common/js/select2.min.js')}}"></script>
@endpush

@push('style-lib')
    <link rel="stylesheet" href="{{asset('assets/common/css/select2.min.css')}}">
@endpush

@push('script')
    <script>
        (function ($) {
            "use strict";
            $('.select2-auto-tokenize').select2({
                dropdownParent: $('.card-body'),
                tags: true,
                tokenSeparators: [',']
            });
        })(jQuery);
    </script>
@endpush
