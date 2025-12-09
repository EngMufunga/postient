@extends('Admin::layouts.app')
@section('panel')
    <div class="row gy-4">

        <div class="col-xxl-3 col-xl-3 col-lg-12">
            @include('Admin::components.navigate_sidebar')
        </div>

        <div class="col-xxl-9 col-xl-9 col-lg-12 mb-30">
            <div class="row">
                <div class="col">
                    <ul class="nav nav-tabs">
                        <li class="nav-item">
                            <a class="nav-link"
                                href="{{ route('admin.setting.notification.templates') }}">@lang('All Templates')</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link"
                                href="{{ route('admin.setting.notification.global') }}">@lang('Global Template')</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active"
                                href="{{ route('admin.setting.notification.email') }}">@lang('Email Config')
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
                <div class="col-md-12">
                    <div class="card p-16 br--solid radius--base bg--white">
                        <form action="" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="mb-4">@lang('Email Send Method')</label>
                                        <select name="email_method" class="form-control form-select">
                                            <option value="php" @if ($general?->mail_config?->name == 'php') selected @endif>
                                                @lang('PHP Mail')</option>
                                            <option value="smtp" @if ($general?->mail_config?->name == 'smtp') selected @endif>
                                                @lang('SMTP')</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="mb-4">@lang('Send Email As')</label>
                                        <input class="form-control" placeholder="notify@example.com" name="email_from" value="{{ $general->email_from ?? '' }}">
                                    </div>
                                </div>
                            </div>



                            <div class="row mt-4 d-none configForm" id="smtp">
                                <div class="col-md-12">
                                    <h6 class="mb-4">@lang('SMTP Configuration')</h6>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="fw--500">@lang('Host') </label>
                                        <input type="text" class="form-control" placeholder="e.g. @lang('smtp.googlemail.com')"
                                            name="host" value="{{ $general->mail_config->host ?? '' }}" />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="fw--500">@lang('Port') </label>
                                        <input type="text" class="form-control" placeholder="@lang('Available port')"
                                            name="port" value="{{ $general->mail_config->port ?? '' }}" />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="fw--500">@lang('Encryption')</label>
                                        <select class="form-control form-select" name="enc">
                                            <option value="ssl" {{ isset($general->mail_config->enc) && $general?->mail_config?->enc == 'ssl' ? 'selected' : '' }}>@lang('SSL')</option>
                                            <option value="tls" {{ isset($general->mail_config->enc) && $general?->mail_config?->enc == 'tls' ? 'selected' : '' }}>@lang('TLS')</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="fw--500">@lang('Username') </label>
                                        <input type="text" class="form-control" placeholder="@lang('Normally your email') address"
                                            name="username" value="{{ $general->mail_config->username ?? '' }}" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="fw--500">@lang('Password') </label>
                                        <input type="text" class="form-control" placeholder="@lang('Normally your email password')"
                                            name="password" value="{{ $general->mail_config->password ?? '' }}" />
                                    </div>
                                </div>
                            </div>
                            <div class="text-end">
                                <button type="submit" class="btn btn--primary">@lang('Save')</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- TEST MAIL MODAL --}}
    <div id="testMailModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Send Test Email')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <form action="{{ route('admin.setting.notification.email.test') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>@lang('Sent to') </label>
                                    <input type="text" name="email" class="form-control"
                                        placeholder="@lang('Email Address')">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary">@lang('Send')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <button type="button" data-bs-target="#testMailModal" data-bs-toggle="modal" class="btn btn-sm btn--primary">@lang('Test Mail')</button>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            var method = '{{ $general->mail_config->name }}';
            emailMethod(method);
            $('select[name=email_method]').on('change', function() {
                var method = $(this).val();
                emailMethod(method);
            });

            function emailMethod(method) {
                $('.configForm').addClass('d-none');
                if (method != 'php') {
                    $(`#${method}`).removeClass('d-none');
                }
            }

        })(jQuery);
    </script>
@endpush
