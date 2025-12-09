@extends('admin.layouts.app')
@section('panel')
    <div class="row gy-4">
        <div class="col-xxl-3 col-xl-3 col-lg-12">
            @include('admin.components.navigate_sidebar')
        </div>

        <div class="col-xxl-9 col-xl-9 col-lg-12">

            <div class="row">
                <div class="col">
                    <ul class="nav nav-tabs">
                        <li class="nav-item">
                            <a class="nav-link active" href="{{ route('admin.setting.notification.templates') }}">@lang('All Templates')</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link " href="{{ route('admin.setting.notification.global') }}">@lang('Global Template')</a>
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

            <div class="table-responsive--sm table-responsive">
                <table class="table table--light style--two custom-data-table">
                    <thead>
                        <tr>
                            <th>@lang('Name')</th>
                            <th>@lang('Subject')</th>
                            <th>@lang('Action')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($templates as $template)
                            <tr>
                                <td>{{ __($template->name) }}</td>
                                <td>{{ __($template->subj) }}</td>
                                <td>
                                    <a title="@lang('Edit')"
                                        href="{{ route('admin.setting.notification.template.edit', $template->id) }}"
                                        class="btn btn-sm editGatewayBtn">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
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
    </div>

    </div>
@endsection
