@extends('admin.layouts.app')
@section('panel')
    <div class="row gy-4 justify-content-start mb-3 pb-3">
        <div class="col-xl-4 col-md-8">
            <form>
                <div class="row gy-4">
                    <div class="col-sm-6">
                        <div class="search-input--wrap position-relative">
                            <input type="search" name="search" class="form-control" placeholder="@lang('Search Username')"
                                value="{{ request()->search }}">
                            <button class="search--btn position-absolute" type="submit"><i class="fa fa-search"></i></button>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="search-input--wrap position-relative">
                            <input name="date" type="text" data-range="true" data-multiple-dates-separator=" - "
                                data-language="en" class="datepicker-here form-control" data-position='bottom right'
                                placeholder="@lang('Date from - to')" autocomplete="off" value="{{ request()->date }}">
                            <button class="search--btn position-absolute" type="submit"><i class="fa fa-search"></i></button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>


    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('User')</th>
                                    <th>@lang('Sent')</th>
                                    <th>@lang('Sender')</th>
                                    <th>@lang('Subject')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                    <tr>
                                        <td class="user--td">
                                            @if ($log->user)
                                                <div class="d-flex justify-content-between justify-content-lg-start gap-3">
                                                    <div
                                                        class="user--info d-flex gap-3 flex-shrink-0 align-items-center flex-wrap flex-md-nowrap">
                                                        <div class="user--thumb-two">
                                                            @if (!empty($log->user->image))
                                                                <img src="{{ getImage(getFilePath('userProfile') . '/' . $log?->user?->image) }}"
                                                                    alt="@lang('Image')">
                                                            @else
                                                                <img src="{{ getImage('assets/images/general/avatar.png') }}"
                                                                    alt="@lang('Image')">
                                                            @endif
                                                        </div>

                                                        <div class="user--content">
                                                            <a class="text-start text-dark"
                                                                href="{{ appendQuery('search', $log?->user?->username) }}">
                                                                {{ $log->user?->fullname ?? '' }}
                                                            </a>
                                                            <br>
                                                            <a href="{{ route('admin.users.detail', $log->user_id) }}"
                                                                class="text-start">{{ '@' . $log->user?->username ?? '' }}</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="d-flex justify-content-between justify-content-lg-start gap-3">
                                                    <div
                                                        class="user--info d-flex gap-3 flex-shrink-0 align-items-center flex-wrap flex-md-nowrap">
                                                        <div class="user--thumb-two">
                                                            <img src="{{ getImage(getFilePath('userProfile') . '/avatar.png') }}"
                                                                alt="@lang('Image')">
                                                        </div>
                                                        <div class="user--content">
                                                            <a class="text-start" href="javascript:void(0)">
                                                                @lang('System')
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            {{ showDateTime($log->created_at) }}
                                        </td>
                                        <td>
                                            <span class="fw--500">{{ __($log->sender) }}</span>
                                        </td>
                                        <td>{{ __($log->subject) }}</td>
                                        <td>
                                            <button title="@lang('Details')"
                                                class="edit--btn bg--transparent notifyDetail btn btn--sm"
                                                data-type="{{ $log->notification_type }}"
                                                @if ($log->notification_type == 'email') data-message="{{ route('admin.report.email.details', $log->id) }}" @else
                                                data-message="{{ $log->message }}" @endif
                                                data-sent_to="{{ $log->sent_to }}" target="_blank"><i
                                                    class="fa-solid fa-eye"></i></button>
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
                @if ($logs->hasPages())
                    <div class="pagination__wrapper py-4">
                        {{ paginateLinks($logs) }}
                    </div>
                @endif
            </div>
        </div>
    </div>


    <div class="modal fade" id="notifyDetailModal" tabindex="-1" aria-labelledby="notifyDetailModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="notifyDetailModalLabel">@lang('Notification Details')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h3 class="text-center mb-3">@lang('To'): <span class="sent_to"></span></h3>
                    <div class="detail"></div>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('breadcrumb-plugins')
    @if (isset($user))
        <a href="{{ route('admin.users.notification.single', $user->id) }}" class="btn btn--primary btn-sm">
            <i class="fa-solid fa-paper-plane"></i> @lang('Send Notification')
        </a>
    @endif
@endpush



@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/common/css/datepicker.min.css') }}">
@endpush


@push('script-lib')
    <script src="{{ asset('assets/common/js/datepicker.min.js') }}"></script>
    <script src="{{ asset('assets/common/js/datepicker.en.js') }}"></script>
@endpush




@push('script')
    <script>
        (function($) {
            'use strict';

            if (!$('.datepicker-here').val()) {
                $('.datepicker-here').datepicker();
            }

            $('.notifyDetail').on('click', function() {
                var message = $(this).data('message');
                var sent_to = $(this).data('sent_to');
                var modal = $('#notifyDetailModal');
                if ($(this).data('type') == 'email') {
                    var message =
                        `<iframe src="${message}" height="500" width="100%" title="Iframe Example"></iframe>`
                }
                $('.detail').html(message)
                $('.sent_to').text(sent_to)
                modal.modal('show');
            });

        })(jQuery);
    </script>
@endpush
