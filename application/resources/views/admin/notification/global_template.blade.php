@extends('Admin::layouts.app')
@section('panel')
    <div class="row gy-4">

        <div class="col-xxl-3 col-xl-3 col-lg-12">
            @include('Admin::components.navigate_sidebar')
        </div>

        <div class="col-xxl-9 col-xl-9 col-lg-12">

            <div class="row">
                <div class="col">
                    <ul class="nav nav-tabs">
                        <li class="nav-item">
                            <a class="nav-link"
                                href="{{ route('admin.setting.notification.templates') }}">@lang('All Templates')</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active"
                                href="{{ route('admin.setting.notification.global') }}">@lang('Global Template')</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.setting.notification.email') }}">@lang('Email Config')
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.setting.notification.sms') }}">@lang('SMS Config')
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card p-16 br--solid radius--base bg--white">
                        <div class="row gy-4">
                            <div class="col-md-12">

                                    <form action="{{ route('admin.setting.notification.global.update') }}" method="POST">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-12 mb-4">
                                                <div class="form-group">
                                                    <label class="fw--500">@lang('Email Body') </label>
                                                    <textarea name="email_template" rows="10" class="form-control  trumEdit" placeholder="@lang('Your email template')">{{ $general->email_template }}</textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label class="fw--500">@lang('SMS Body') </label>
                                                    <textarea class="form-control" rows="4" placeholder="@lang('SMS Body')" name="sms_body" required>{{ $general->sms_body }}</textarea>
                                                </div>
                                            </div>
                                            <div class="col-lg-12 mb-4">
                                                <div class="row gy-4">
                                                    <div class="col-lg-6 d-flex flex-wrap align-items-center gap-2">
                                                        <h6>@lang('Short Code'):</h6>
                                                        <ul class="d-flex flex-wrap gap-2">
                                                            <li>
                                                                <span class="short-codes">@{{fullname}} <span class="copy-icon"><i class="fa-regular fa-copy"></i></span></span>
                                                            </li>
                                                            <li>
                                                                <span class="short-codes">@{{username}} <span class="copy-icon"><i class="fa-regular fa-copy"></i></span></span>
                                                            </li>
                                                            <li>
                                                                <span class="short-codes">@{{message}} <span class="copy-icon"><i class="fa-regular fa-copy"></i></span></span>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    <div class="col-lg-6 d-flex align-items-center flex-wrap flex-lg-nowrap gap-2">
                                                        <h6 class="flex-shrink-0">@lang('Global Short Code'):</h6>
                                                        <ul class="d-flex flex-wrap gap-2">
                                                            @foreach ($general->global_shortcodes as $shortCode => $codeDetails)
                                                                <li>
                                                                    <span class="short-codes">@{{@php echo $shortCode @endphp}} <span class="copy-icon"><i class="fa-regular fa-copy"></i></span></span>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 text-end">
                                                <button type="submit"
                                                    class="btn btn--primary">@lang('Save Changes')</button>
                                            </div>
                                        </div>
                                    </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ asset('assets/common/js/ckeditor.js') }}"></script>

    <script>
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
    </script>
@endpush
