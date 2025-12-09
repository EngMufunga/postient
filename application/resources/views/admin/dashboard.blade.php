@extends('admin.layouts.app')
@section('panel')

    @adminHas('dashboard')

        @if (isset($general->system_info) && !empty(json_decode($general->system_info)->message))
            @if(json_decode($general->system_info)->message)
            <div class="row">
                @foreach (json_decode($general->system_info)->message as $msg)
                    <div class="col-md-12">
                        <div class="alert border border--primary" role="alert">
                            <div class="alert__icon bg--primary"><i class="far fa-bell"></i></div>
                            <p class="alert__message">@php echo $msg; @endphp</p>
                            <button type="button" class="close" data-bs-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
            @endif
        @endif

        <div class="row gy-4">
            <div class="col-xl-12">

                <div class="row gy-4">
                     <div class="col-xxl-3 col-xl-4 col-md-6">
                        <a class="dashboard-widget--card position-relative" href="{{ route('admin.posts.index') }}">
                            <div class="dashboard-widget__icon">
                                <i class="dashboard-card-icon fa-solid fa-newspaper"></i>
                            </div>
                            <div class="dashboard-widget__content">
                                <span class="title">@lang('Total Post')</span>
                                <h5 class="number">{{ $widget['total_post'] }}</h5>
                            </div>
                        </a>
                    </div>

                    <div class="col-xxl-3 col-xl-4 col-md-6">
                        <a class="dashboard-widget--card position-relative" href="{{ route('admin.posts.index') }}">
                            <div class="dashboard-widget__icon">
                                <i class="dashboard-card-icon fa-solid fa-blog"></i>
                            </div>

                            <div class="dashboard-widget__content">
                                <span class="title">@lang('Total Schedule Post')</span>
                                <h5 class="number">{{ $widget['scheduled_post'] }}</h5>
                            </div>
                        </a>
                    </div>
                    <div class="col-xxl-3 col-xl-4 col-md-6">
                        <a class="dashboard-widget--card position-relative" href="{{ route('admin.users.all', 'active') }}">
                            <div class="dashboard-widget__icon">
                                <i class="dashboard-card-icon fa-solid fa-user-check"></i>
                            </div>
                            <div class="dashboard-widget__content">
                                <span class="title">@lang('Verified Users')</span>
                                <h5 class="number">{{ $widget['verified_users'] }}</h5>
                            </div>
                            <span class="badge badge--success position-absolute">
                                <i class="fa-solid fa-arrow-trend-up"></i> +{{ $widget['verified_percent'] }}%
                            </span>
                        </a>
                    </div>

                    <div class="col-xxl-3 col-xl-4 col-md-6">
                        <a class="dashboard-widget--card position-relative" href="{{ route('admin.users.all', 'banned') }}">

                            <div class="dashboard-widget__icon">
                                <i class="dashboard-card-icon fa-solid fa-ban"></i>
                            </div>

                            <div class="dashboard-widget__content">
                                <span class="title">@lang('Banned Users')</span>
                                <h5 class="number">{{ $widget['banned_users']  }}</h5>
                            </div>

                            <span class="badge badge--danger position-absolute">
                                <i class="fa-solid fa-arrow-trend-down"></i>{{ $widget['banned_percent'] }}%
                            </span>
                        </a>
                    </div>

                    <div class="col-xxl-3 col-xl-4 col-md-6">
                        <a class="dashboard-widget--card position-relative" href="{{ route('admin.users.all', 'email_unverified') }}">
                            <div class="dashboard-widget__icon">
                                <i class="dashboard-card-icon fa-solid fa-envelope-open-text"></i>
                            </div>
                            <div class="dashboard-widget__content">
                                <span class="title">@lang('Email Unverified')</span>
                                <h5 class="number">{{ $widget['email_unverified_users'] }}</h5>
                            </div>
                            <span class="badge badge--success position-absolute">
                                <i class="fa-solid fa-arrow-trend-up"></i> + {{ $widget['email_unverified_percent'] }}%
                            </span>
                        </a>
                    </div>

                    <div class="col-xxl-3 col-xl-4 col-md-6">
                        <a class="dashboard-widget--card position-relative" href="{{ route('admin.users.all', 'mobile_unverified') }}">

                            <div class="dashboard-widget__icon">
                                <i class="dashboard-card-icon fa-solid fa-mobile-screen-button"></i>
                            </div>

                            <div class="dashboard-widget__content">
                                <span class="title">@lang('Mobile Unverified')</span>
                                <h5 class="number">{{ $widget['mobile_unverified_users'] }}</h5>
                            </div>

                            <span class="badge badge--success position-absolute"><i class="fa-solid fa-arrow-trend-up"></i> +{{ $widget['mobile_unverified_percent'] }}%</span>
                        </a>
                    </div>

                    <div class="col-xxl-3 col-xl-4 col-md-6">
                        <a class="dashboard-widget--card position-relative" href="{{ route('admin.deposit.log') }}">
                            <div class="dashboard-widget__icon">
                                <i class="dashboard-card-icon fa-solid fa-hand-holding-dollar"></i>
                            </div>
                            <div class="dashboard-widget__content">
                                <span class="title">@lang('Total Deposit')</span>
                                <h5 class="number">{{ $general->cur_sym }}{{ showAmount($widget['total_deposit_amount'], 2) }}</h5>
                            </div>
                        </a>
                    </div>

                    <div class="col-xxl-3 col-xl-4 col-md-6">
                        <a class="dashboard-widget--card position-relative" href="{{ route('admin.deposit.log') }}">

                            <div class="dashboard-widget__icon">
                                <i class="dashboard-card-icon fa-solid fa-circle-dollar-to-slot"></i>
                            </div>

                            <div class="dashboard-widget__content">
                                <span class="title">@lang('Deposit Charge')</span>
                                <h5 class="number">{{ $general->cur_sym }}{{ showAmount($widget['deposit_change'], 2) }}</h5>
                            </div>
                        </a>
                    </div>

                </div>
            </div>




            <div class="col-xl-6">
                <div class="card bg--white br--solid">
                    <div class="card-body position-relative">
                        <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-3">
                            <h5 class="card-title mb-0">@lang('Monthly Deposit Report')</h5>
                        </div>
                        <div id="account-chart" data-deposits="{{ base64_encode(json_encode($depositsChart)) }}"></div>
                    </div>
                </div>
            </div>

            <div class="col-xl-6">
                <div class="card bg--white br--solid">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-3">
                            <h5 class="card-title mb-0">@lang('Recent Transactions')</h5>
                        </div>
                        <div class="table-responsive table-responsive--sm">
                            <table class="table align-items-center style--three table--light">
                                <thead>
                                    <tr>
                                        <th>@lang('Customer')</th>
                                        <th>@lang('Trx')</th>
                                        <th>@lang('Date')</th>
                                        <th>@lang('Amount')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($transactions as $key=>$trx)
                                    <tr>
                                        <td class="user--td">
                                            <div class="d-flex justify-content-between justify-content-lg-start gap-3">
                                                <div class="user--info d-flex gap-3 flex-shrink-0 align-items-center flex-wrap flex-md-nowrap">
                                                    <div class="user--thumb">
                                                        @if(!empty($trx->user->image))
                                                            <img src="{{ getImage(getFilePath('userProfile') . '/' . $trx?->user?->image ) }}" alt="@lang('Image')">
                                                        @else
                                                            <img src="{{ getImage('assets/images/general/avatar.png') }}" alt="@lang('Image')">
                                                        @endif
                                                    </div>
                                                    <div class="user--content">
                                                        <a class="text-start" href="{{ route('admin.report.transaction') . '?search=' . $trx->user->username }}">
                                                            {{ $trx->user->fullname }}
                                                        </a>
                                                        <p class="text-start">{{ $trx?->user?->email }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <td>
                                            {{ $trx->trx }}
                                        </td>

                                        <td>
                                            <h6>{{ showDateTime($trx->created_at, 'd M Y') }}</h6>
                                        </td>
                                        <td>
                                            <h6>{{ showAmount($trx->post_balance) }} {{ __($general->cur_text) }}</h6>
                                        </td>
                                    </tr>
                                    @empty

                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>



            <div class="col-xl-9 col-md-8">
                <div class="card bg--white br--solid">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-3">
                            <h5 class="card-title mb-0">@lang('Recent Support Tickets')</h5>
                        </div>

                        <div class="table-responsive table-responsive--sm">
                            <table class="table align-items-center style--three table--light">
                                <thead>
                                    <tr>
                                        <th>@lang('Customer')</th>
                                        <th>@lang('Subject')</th>
                                        <th>@lang('Priority')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($tickets as $key=>$ticket)
                                    <tr>
                                        <td>
                                            <div class="d-flex flex-wrap flex-md-nowrap justify-content-end justify-content-lg-start align-items-center gap-2">
                                                <div class="user--thumb">
                                                    @if(!empty($ticket->user->image))
                                                        <img src="{{ getImage(getFilePath('userProfile') . '/' . $ticket?->user?->image ) }}" alt="@lang('Image')">
                                                    @else
                                                        <img src="{{ getImage('assets/images/general/avatar.png') }}" alt="@lang('Image')">
                                                    @endif
                                                </div>
                                                <div class="user--content">
                                                    @if($ticket->user_id)
                                                    <a class="text-start" href="{{ route('admin.users.detail', $ticket->user->id) }}">
                                                        {{ $ticket->user->fullname }}
                                                    </a>
                                                    <p class="text-start">{{ $ticket?->user?->email }}</p>
                                                    @else

                                                    <p class="text-start">{{ $ticket?->name }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>

                                        <td>
                                            <a href="{{ route('admin.ticket.view', $ticket->id) }}" class="fw--500">
                                                @lang('Ticket')#{{ $ticket->ticket }} - {{ strLimit($ticket->subject,15) }}
                                            </a>
                                        </td>

                                        <td>
                                            @php echo $ticket->priorityBadge; @endphp
                                        </td>
                                    </tr>
                                    @empty

                                    @endforelse


                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-xl-3 col-md-4">
                <div class="card bg--white br--solid">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-3">
                            <h5 class="card-title mb-0">@lang('Transactions')</h5>
                        </div>

                        <div class="d-flex justify-content-center align-items-center gap-5 py-4">
                            <div class="order-info--item d-flex gap-4 flex-column justify-content-center align-items-center">
                                <div class="d-flex flex-column justify-content-center align-items-center gap-2">
                                    <div class="number--wrap one d-flex justify-content-center align-items-center flex-shrink-0">
                                        <h2 class="m-0 text--white">{{ getAmount($widget['plus_transactions']) }}</h2>
                                    </div>
                                    <p class="fs-6">@lang('Plus Transactions')</p>
                                </div>
                                <div class="d-flex flex-column justify-content-center align-items-center gap-2">
                                    <div class="number--wrap two d-flex justify-content-center align-items-center flex-shrink-0">
                                        <h2 class="m-0 text--white">{{ getAmount($widget['minus_transactions']) }}</h2>
                                    </div>
                                    <p class="fs-6">@lang('Minus Transactions')</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="row">
            <div class="col-lg-12">
                <p>@lang('You have no permission to view the page content!')</p>
            </div>
        </div>
    @endadminHas


    {{-- cron modal --}}
    <div class="modal fade" id="cronModal" role="dialog" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">@lang('Cron Job Setting Instruction')</h5>
                    <button class="btn-close" data-bs-dismiss="modal" type="button" aria-label="Close">

                    </button>
                </div>
                <div class="modal-body">
                    <h3 class="text--danger text-center">@lang('Please Set Cron Job Now')</h3>
                    <p class="lead">
                        @lang('To automate key system processes such as social account updates, subscription renewals, token refreshes, and scheduled post processing, please set up the cron jobs below. Ensure that your cron jobs are configured and running correctly. For optimal performance, set the cron interval as frequently as possible — once every 15–30 minutes is recommended, while every minute is ideal.')
                    </p>
                    <label class="font-weight-bold">@lang('Primary Cron Command')</label>

                    <div class="input-group w-100 mb-2">
                        <input class="form--control w-75" id="cron" name="text" type="text" value="curl -s {{ route('cron') }}" readonly>
                        <span class="input-group-text copytext btn btn--primary cronCopy pt-2 w-25">
                            @lang('Copy')
                        </span>
                    </div>
                    <p>
                       <span class="text-danger font-weight-bold">@lang('Note'): </span>@lang('Ensure this cron job is running properly. It is responsible for handling subscription updates, social account synchronization, and access token refreshes.')
                    </p>

                    <label class="font-weight-bold mt-3">@lang('Scheduled Post Cron Command')</label>

                    <div class="input-group w-100">
                        <input class="form--control w-75" id="cronSchedule" name="text" type="text" value="curl -s {{ route('cron.schedule.post') }}" readonly>
                        <span class="input-group-text copytext btn btn--primary cronSchedule pt-2 w-25">
                            @lang('Copy')
                        </span>
                    </div>
                    <p>
                        <span class="text-danger font-weight-bold">@lang('Note'): </span>@lang('Ensure this cron job is active and functioning correctly. It handles automated posting and updates for scheduled content.')
                    </p>
                </div>
            </div>
        </div>
    </div>

@endsection



@adminHas('dashboard')

    @push('breadcrumb-plugins')
        @php
            $lastCron = Carbon\Carbon::parse($general->last_cron)->diffInSeconds();

        @endphp
        <span class="badge badge--success @if ($lastCron < 300)  @elseif($lastCron < 900) text--warning @else  @endif">
            @lang('Last Cron Run')
            <strong>{{ diffForHumans(gs('last_cron')) }}</strong>
        </span>
    @endpush


    @push('script-lib')
        <script src="{{ asset('assets/admin/js/apexcharts.min.js') }}"></script>
    @endpush

    @push('script')
        <script>
            (function($) {
                "use strict";
                @if (Carbon\Carbon::parse($general->last_cron)->diffInMinutes() > 30)
                window.onload = () => {
                    $('#cronModal').modal('show');
                }
                @endif

                $('.cronCopy').on('click', function() {
                    var copyText = document.getElementById("cron");
                    copyText.select();
                    copyText.setSelectionRange(0, 99999);
                    document.execCommand("copy");

                    notify('success', `Copied: ${copyText.value}`);
                });
                $('.cronSchedule').on('click', function() {
                    var copyText = document.getElementById("cronSchedule");
                    copyText.select();
                    copyText.setSelectionRange(0, 99999);
                    document.execCommand("copy");

                    notify('success', `Copied: ${copyText.value}`);
                });
            })(jQuery);
        </script>

        <script>
            (function ($) {
                'use strict';

                const $chartData = $('#account-chart');
                const depositsEncoded = $chartData.data('deposits');

                const depositsChart = JSON.parse(atob(depositsEncoded));

                var options = {
                    chart: {
                        type: 'bar',
                        height: '333px'
                    },
                    grid: {
                        show: true,
                        strokeDashArray: 4,
                        borderColor: '#e0e0e0',
                        position: 'back'
                    },
                    dataLabels: {
                        enabled: false
                    },
                    plotOptions: {
                        bar: {
                            columnWidth: '50%',
                            horizontal: false
                        }
                    },
                    colors: ['#00A86B'], // green for deposits
                    series: [
                        {
                            name: 'Deposits',
                            data: depositsChart.values
                        }
                    ],
                    labels: depositsChart.labels,
                    xaxis: {
                        categories: depositsChart.labels,
                        title: {
                            text: 'Months',
                            style: {
                                fontSize: '14px',
                                fontWeight: 'bold',
                                color: '#333'
                            }
                        }
                    },
                    yaxis: {
                        min: 0,
                        title: {
                            text: 'Amount',
                            style: {
                                fontSize: '14px',
                                fontWeight: 'bold',
                                color: '#333'
                            }
                        }
                    },
                    tooltip: {
                        y: {
                            formatter: function (y) {
                                return typeof y !== "undefined" ? "{{ __($general->cur_sym) }}" + y.toFixed(0) : y;
                            }
                        }
                    }
                };

                var chart = new ApexCharts(document.querySelector("#account-chart"), options);
                chart.render();
            })(jQuery)
        </script>
    @endpush
@endadminHas
