@extends('admin.layouts.app')
@section('panel')
    @php
        $sessionData = session('SEND_NOTIFICATION') ?? [];
        $viaName = $sessionData['via'] ?? 'email';
        $viaText = ucfirst($viaName);
    @endphp

    @empty(!$sessionData)
        <div class="notification-data-and-loader">
            <div class="row justify-content-center mb-4">
                <div class="col-xl-7 col-12">
                    <div class="row gy-4 justify-content-center">
                        <div class="col-sm-6">
                               <a class="dashboard-widget--card position-relative" href="javascript:void(0)">
                                    <div class="dashboard-widget__icon">
                                        <i class="fa-solid fa-envelope-circle-check"></i>
                                    </div>
                                    <div class="dashboard-widget__content">
                                        <span class="title">{{ $viaText . ' should be sent' }}</span>
                                        <h5 class="number">{{ $sessionData['total_user'] ?? 0 }}</h5>
                                    </div>
                                </a>
                        </div>


                        <div class="col-sm-6">

                            <a class="dashboard-widget--card position-relative" href="javascript:void(0)">
                                    <div class="dashboard-widget__icon">
                               <i class="fa-solid fa-paper-plane"></i>
                                    </div>
                                    <div class="dashboard-widget__content">
                                        <span class="title">{{ $viaText . ' has been sent' }}</span>
                                        <h5 class="number">{{ $sessionData['total_sent'] ?? 0 }}</h5>
                                    </div>
                                </a>
                        </div>


                        <div class="col-sm-6">
                                <a class="dashboard-widget--card position-relative" href="javascript:void(0)">
                                    <div class="dashboard-widget__icon">
                                    <i class="fa-regular fa-envelope"></i>
                                    </div>
                                    <div class="dashboard-widget__content">
                                        <span class="title">{{ $viaText . ' has yet to be sent' }}</span>
                                        <h5 class="number"> {{ ($sessionData['total_user'] ?? 0) - ($sessionData['total_sent'] ?? 0) }}</h5>
                                    </div>
                                </a>
                        </div>


                        <div class="col-sm-6">
                             <a class="dashboard-widget--card position-relative" href="javascript:void(0)">
                                    <div class="dashboard-widget__icon">
                                        <i class="fa-solid fa-list"></i>
                                    </div>
                                    <div class="dashboard-widget__content">
                                        <span class="title">{{ $viaText . ' per batch' }}</span>
                                        <h5 class="number"> {{ $sessionData['batch'] ?? 0 }}</h5>
                                    </div>
                                </a>
                        </div>


                        <div class="col-12">
                            <div class="card bg--white br--solid radius--base p-16">
                                <div class="card-body p-5 text-center">
                                    <div class="coaling-loader flex-column d-flex justify-content-center">
                                        <div class="countdown">
                                            <div class="coaling-time">
                                                <span class="coaling-time-count">{{ $sessionData['cooling_time'] ?? 0 }}</span>
                                            </div>
                                            <div class="svg-count">
                                                <svg viewBox="0 0 100 100">
                                                    <circle id="animate-circle" r="45" cx="50" cy="50"></circle>
                                                </svg>
                                            </div>
                                        </div>
                                        <p class="mt-2">
                                            @lang("$viaText will be sent again with a") <span class="coaling-time-count"></span>
                                            @lang(' second delay. Avoid closing or refreshing the browser.')
                                        </p>
                                        <p class="text__primary">
                                            @lang(' ' . ($sessionData['total_sent'] ?? 0) . ' out of ' . ($sessionData['total_user'] ?? 0) . ' ' . $viaName . ' were successfully transmitted')
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endempty

    <div class="row @empty(!$sessionData) d-none @endempty">
        <div class="col-xl-12">
            <div class="card bg--white br--solid p-16 radius--base">
                <form class="notify-form" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input name="via" type="hidden" value="{{ $viaName }}">
                    <div class="row">
                        <div class="col-12">
                            <div class="row">
                                @if (gs('en'))
                                    <div class="col-xxl-2 col-xl-3 col-lg-4 col-sm-6">
                                        <div class="notification-via btn btn-outline--primary d-flex align-items-center justify-content-center @if ($viaName == 'email') active @endif mb-4"
                                            data-method="email">

                                            <div class="send-via-method d-flex align-items-center gap-2">
                                                <i class="fa-regular fa-envelope"></i>
                                                <h5>@lang('Send Via Email')</h5>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                @if (gs('sn'))
                                    <div class="col-xxl-2 col-xl-3 col-lg-4 col-sm-6">
                                        <div class="notification-via btn btn-outline--primary d-flex align-items-center justify-content-center @if ($viaName == 'sms') active @endif mb-4"
                                            data-method="sms">

                                            <div class="send-via-method d-flex align-items-center gap-2">
                                                <i class="fa-solid fa-mobile-screen"></i>
                                                <h5>@lang('Send Via SMS')</h5>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>@lang('Being Sent To') </label>
                                <select class="form-control form-select select2" name="being_sent_to"
                                    data-minimum-results-for-search="1" required>
                                    @foreach ($notifyToUser as $key => $toUser)
                                        <option value="{{ $key }}" @selected(old('being_sent_to', $sessionData['being_sent_to'] ?? '') == $key)>
                                            {{ __($toUser) }}</option>
                                    @endforeach
                                </select>
                                <small class="text__info d-none userCountText"> <i class="fa-solid fa-circle-info"></i> <strong
                                        class="userCount">0</strong> @lang('active users found to send the notification')</small>
                            </div>
                            <div class="input-append">
                            </div>
                        </div>
                        <div class="form-group col-md-12 subject-wrapper">
                            <label>@lang('Subject') <span class="text__danger">*</span> </label>
                            <input class="form-control" name="subject" type="text"
                                value="{{ old('subject', $sessionData['subject'] ?? '') }}"
                                placeholder="@lang('Subject / Title')">
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>@lang('Message') <span class="text--danger">*</span> </label>
                                <textarea class="form-control trumEdit"  name="message" rows="10">{{ old('message', $sessionData['message'] ?? '') }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-4 start-from-col">
                                    <div class="form-group">
                                        <label>@lang('Start Form') </label>
                                        <input class="form-control" name="start" type="number"
                                            value="{{ old('start', $sessionData['start'] ?? '') }}"
                                            placeholder="@lang('Start form user id. e.g. 1')" required>
                                    </div>
                                </div>
                                <div class="col-md-4 per-batch-col">
                                    <div class="form-group">
                                        <label>@lang('Per Batch') </label>
                                        <div class="input-group">
                                            <input class="form-control" name="batch" type="number"
                                                value="{{ old('batch', $sessionData['batch'] ?? '') }}"
                                                placeholder="@lang('How many user')" required>
                                            <span class="input-group-text">
                                                @lang('User')
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 cooling-period-col">
                                    <div class="form-group">
                                        <label>@lang('Cooling Period') </label>
                                        <div class="input-group">
                                            <input class="form-control" name="cooling_time" type="number"
                                                value="{{ old('cooling_time', $sessionData['batch'] ?? '') }}"
                                                placeholder="@lang('Waiting time')" required>
                                            <span class="input-group-text">
                                                @lang('Seconds')
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-4">
                        <button class="btn btn--primary" type="submit">@lang('Submit')</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endsection

@push('script-lib')
    <script src="{{ asset('assets/common/js/ckeditor.js') }}"></script>
    <script src="{{asset('assets/common/js/select2.min.js')}}"></script>
@endpush

@push('style-lib')
    <link rel="stylesheet" href="{{asset('assets/common/css/select2.min.css')}}">
@endpush





@push('script')

    <script>
        (function($) {
            "use strict";
                window.editors = {};

                document.querySelectorAll('.trumEdit').forEach(element => {
                    ClassicEditor
                        .create(element)
                        .then(editor => {
                            window.editors[element.name] = editor;
                        })
                        .catch(error => {
                            console.error(error);
                        });
                });

        })(jQuery);
    </script>


    <script>
        let formSubmit = false;
        (function($) {
            "use strict"
            $('select[name=being_sent_to]').on('change', function(e) {
                let methodName = $(this).val();
                if (!methodName) return;
                getUserCount(methodName);
                methodName = methodName.toUpperCase();
                if (methodName == 'SELECTEDUSERS') {
                    $('.input-append').html(`
                    <div class="form-group" id="user_list_wrapper">
                        <label class="required">@lang('Select User')</label>
                        <select name="user[]"  class="form-control form-select" id="user_list" required multiple >
                            <option disabled>@lang('Select One')</option>
                        </select>
                    </div>
                    `);
                    fetchUserList();
                    return;
                }

                $('.input-append').empty();
            }).change();
            function fetchUserList() {
                $('.row #user_list').select2({
                    ajax: {
                        url: "{{ route('admin.users.get') }}",
                        type: "get",
                        dataType: 'json',
                        delay: 1000,
                        data: function(params) {
                            return {
                                search: params.term,
                                page: params.page,
                            };
                        },
                        processResults: function(response, params) {
                            params.page = params.page || 1;
                            let data = response.users.data;
                            return {
                                results: $.map(data, function(item) {
                                    return {
                                        text: item.email,
                                        id: item.id
                                    }
                                }),
                                pagination: {
                                    more: response.more
                                }
                            };
                        },
                        cache: false,
                    },
                    dropdownParent: $('.input-append #user_list_wrapper')
                });
            }
            function getUserCount(methodName) {
                var methodNameUpper = methodName.toUpperCase();
                if (methodNameUpper == 'SELECTEDUSERS' || methodNameUpper == 'LIST' || methodNameUpper == 'TOPDEPOSITEDUSERS' ||
                    methodNameUpper == 'NOTLOGINUSERS') {
                    $('.userCount').text(0);
                    $('.userCountText').addClass('d-none');
                    return;
                }
                var route = "{{ route('admin.users.segment.count', ':methodName') }}"
                route = route.replace(':methodName', methodName)
                $.get(route, function(response) {
                    $('.userCount').text(response);
                    $('.userCountText').removeClass('d-none');
                });
            }




            $('.notification-via').on('click', function() {
                $('.notification-via').removeClass('active');
                $(this).addClass('active');
                $('[name=via]').val($(this).data('method'));


                let editor = window.editors['message'];
                let method = $(this).data('method');

                // let ckEditorWrapper = editor.ui.view.editable.element.parentElement.parentElement;
                let originalTextarea = document.querySelector('[name="message"]');




                if (method === 'email') {
                    $(originalTextarea).css('display', 'none');
                    // ckEditorWrapper.classList.remove('d-none');

                    if (!editor) {
                        // Re-initialize if needed
                        ClassicEditor
                            .create(originalTextarea)
                            .then(newEditor => {
                                window.editors['message'] = newEditor;
                                newEditor.setData(originalTextarea.value);
                            });
                    } else {
                        editor.setData(originalTextarea.value);
                    }

                } else {
                    // Sync CKEditor to textarea (in case needed)
                    originalTextarea.value = editor.getData();

                    // Destroy CKEditor
                    editor.destroy().then(() => {
                        window.editors['message'] = null;

                        // Show textarea
                        $(originalTextarea).css('display', 'block');
                    });

                    // Optional: Clear
                    originalTextarea.value = '';
                }






                if ( $(this).data('method') == 'email') {
                    $('.subject-wrapper').removeClass('d-none');
                } else {
                    $('.subject-wrapper').addClass('d-none')
                }
                $('.subject-wrapper').find('input').val('');
            });








            $(".notify-form").on("submit", function(e) {
                formSubmit = true;
            });
            @empty(!$sessionData)
                $(document).ready(function() {
                    'use strict';
                    const coalingTimeOut = setTimeout(() => {
                        let coalingTime = Number("{{ $sessionData['cooling_time'] }}");
                        $("#animate-circle").css({
                            "animation": `countdown ${coalingTime}s linear infinite forwards`
                        });
                        let $coalingCountElement = $('.coaling-time-count');
                        let $coalingLoaderElement = $(".coaling-loader");
                        $coalingCountElement.text(coalingTime);
                        const coalingIntVal = setInterval(function() {
                            coalingTime--;
                            $coalingCountElement.text(coalingTime);
                            if (coalingTime <= 0) {
                                formSubmit = true;
                                $("#animate-circle").css({
                                    "animation": `unset`
                                });
                                clearInterval(coalingIntVal);
                                clearTimeout(coalingTimeOut);
                                $(".notify-form").submit();
                            }
                        }, 1000);
                    }, 1000);
                });
            @endif
        })(jQuery);
        @if (!empty($sessionData) && @request()->email_sent && @request()->email_sent = 'yes')
            window.addEventListener('beforeunload', function(event) {
                if (!formSubmit) {
                    event.preventDefault();
                    event.returnValue = '';
                    var confirmationMessage = 'Are you sure you want to leave this page?';
                    (event || window.event).returnValue = confirmationMessage;
                    return confirmationMessage;
                }
            });
        @endif
    </script>
@endpush



@push('style')
    <style>
        .countdown {
            position: relative;
            height: 150px !important;
            width: 150px !important;
            text-align: center;
            margin: 0 auto;
        }

        .coaling-time {
            color: yellow;
            position: absolute;
            z-index: 999999;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 30px;
        }

        .coaling-loader svg {
            position: absolute;
            top: 0;
            right: 0;
            width: 150px !important;
            height: 150px !important;
            transform: rotateY(-180deg) rotateZ(-90deg);
            position: relative;
            z-index: 1;
        }

        .coaling-loader svg circle {
            stroke-dasharray: 314px;
            stroke-dashoffset: 0px;
            stroke-linecap: round;
            stroke-width: 6px;
            stroke: hsl(var(--primary));
            fill: transparent;
        }

        .coaling-loader .svg-count {
            width: 150px !important;
            height: 150px !important;
            position: relative;
            z-index: 1;
        }

        .coaling-loader .svg-count::before {
            content: '';
            position: absolute;
            outline: 5px solid #f3f3f9;
            z-index: -1;
            width: calc(100% - 16px) !important;
            height: calc(100% - 16px) !important;
            left: 8px;
            top: 8px;
            z-index: -1;
            border-radius: 100%
        }

        .coaling-time-count {
            color: hsl(var(--primary));
        }

        @keyframes countdown {
            from {
                stroke-dashoffset: 0px;
            }

            to {
                stroke-dashoffset: 314px;
            }
        }
    </style>
@endpush
