@extends('admin.layouts.app')
@section('panel')
    <div class="row gy-4 mb-none-30">

        <div class="col-xxl-3 col-xl-3 col-lg-12">
            @include('admin.components.navigate_sidebar')
        </div>

        <div class="col-xxl-9 col-xl-9 col-lg-12 mb-30">
            <div class="card p-16 radius--base br--solid bg--white">
                <h6 class="mb-3">@lang('Warning: Please do it carefully. This might break the design.')</h6>
                <form action="{{ route('admin.setting.custom.css.update') }}" method="post">
                    @csrf
                    <div class="form-group custom-css">
                        <textarea class="customCss" rows="20" name="css">{{ $file_content }}</textarea>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn--primary">@lang('Save Changes')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('style')
    <style>
        .customCss {
            background-color: black;
            color: white;
            font-size: 15px !important;
        }
    </style>
@endpush
