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
                            <a class="nav-link active"
                                href="{{ route('admin.setting.notification.templates') }}">@lang('All Templates')</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link"
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
                                <form action="{{ route('admin.setting.notification.template.update', $template->id) }}" method="post">
                                    @csrf
                                    <div class="row gy-4">
                                        <div class="col-md-6">
                                            <div class="card p-16 br--solid radius--base bg--white">
                                                <h5 class="card-title">@lang('Email Template')</h5>

                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="fw--500">@lang('Status')</label>
                                                            <label class="switch m-0">
                                                                <input type="checkbox" class="toggle-switch" name="email_status"
                                                                    {{ $template->email_status ? 'checked' : null }}>
                                                                <span class="slider round"></span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label class="fw--500">@lang('Subject')</label>
                                                            <input type="text" class="form-control form-control-lg" placeholder="@lang('Email subject')"
                                                                name="subject" value="{{ $template->subj }}" required />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group mb-0">
                                                            <label class="fw--500">@lang('Message') <span class="text-danger">*</span></label>
                                                            <textarea name="email_body" rows="10" class="form-control trumEdit" placeholder="@lang('Your message using short-codes')">{{ $template->email_body }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="card p-16 br--solid radius--base bg--white">
                                                <h5 class="card-title">@lang('SMS Template')</h5>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label class="fw--500">@lang('Status')</label>
                                                            <label class="switch m-0">
                                                                <input type="checkbox" class="toggle-switch" name="sms_status"
                                                                    {{ $template->sms_status ? 'checked' : null }}>
                                                                <span class="slider round"></span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group mb-0">
                                                            <label class="fw--500">@lang('Message')</label>
                                                            <textarea name="sms_body" rows="10" class="form-control" placeholder="@lang('Your message using short-codes')" required>{{ $template->sms_body }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-12 mb-4">
                                            <div class="row gy-4">
                                                <div class="col-lg-6 d-flex flex-wrap align-items-center gap-2">
                                                    <h6>@lang('Short Codes'):</h6>

                                                    <ul class="d-flex flex-wrap gap-2">
                                                        @forelse($template->shortcodes as $shortcode => $key)
                                                            <li>
                                                                <span class="short-codes">@php echo "{{". $shortcode ."}}" @endphp
                                                                    <span class="copy-icon">
                                                                        <i class="fa-regular fa-copy"></i>
                                                                    </span>
                                                                </span>
                                                            </li>
                                                        @empty
                                                        @endforelse
                                                    </ul>
                                                </div>
                                                <div class="col-lg-6 d-flex align-items-center flex-wrap flex-lg-nowrap gap-2">
                                                    <h6 class="flex-shrink-0">Default Short Codes:</h6>
                                                    <ul class="d-flex flex-wrap gap-2">
                                                        @foreach ($general->global_shortcodes as $shortCode => $codeDetails)
                                                            <li>
                                                                <span class="short-codes">@{{@php echo $shortCode @endphp}}
                                                                    <span class="copy-icon"><i class="fa-regular fa-copy"></i></span>
                                                                </span>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group text-end">
                                        <button type="submit" class="btn btn--primary mt-4">@lang('Save')</button>
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

@push('breadcrumb-plugins')
    <a href="{{ route('admin.setting.notification.templates') }}" class="btn btn-sm btn--primary"><i class="fa-solid fa-arrow-left"></i> @lang('Back') </a>
@endpush

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
