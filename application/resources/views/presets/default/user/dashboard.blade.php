@extends('Template::layouts.master')
@section('content')

    <div class="row g-4">
        <div class="col-xl-3 col-sm-6">
            <div class="dashboard__card card">
                <img class="dc__img" src="{{ getImage($activeTemplateTrue . 'images/star-outline.png') }}"
                    alt="@lang('Image')">
                <div class="dc__account">
                    <span><i class="fa-solid fa-blog"></i></span>
                    <div class="dc__account__content">
                        <h4>@lang('Total Posts')</h4>
                        <p>{{ $widget['total_post'] }} @lang('Posts')</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="dashboard__card card">
                <img class="dc__img" src="{{ getImage($activeTemplateTrue . 'images/star-outline.png') }}"
                    alt="@lang('Image')">
                <div class="dc__account">
                    <span><i class="fa-solid fa-clock"></i></span>
                    <div class="dc__account__content">
                        <h4>@lang('Scheduled Posts')</h4>
                        <p>{{ $widget['total_schedule_post'] }} @lang('Posts')</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="dashboard__card card">
                <img class="dc__img" src="{{ getImage($activeTemplateTrue . 'images/star-outline.png') }}"
                    alt="@lang('Image')">
                <div class="dc__account">
                    <span><i class="fa-solid fa-clipboard-list"></i></span>
                    <div class="dc__account__content">
                        <h4>@lang('Draft Posts')</h4>
                        <p>{{ $widget['draft_post'] }} @lang('Posts')</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="dashboard__card card">
                <img class="dc__img" src="{{ getImage($activeTemplateTrue . 'images/star-outline.png') }}"
                    alt="@lang('Image')">
                <div class="dc__account">
                    <span><i class="fa-solid fa-upload"></i></span>
                    <div class="dc__account__content">
                        <h4>@lang('Publish Posts')</h4>
                        <p>{{ $widget['publish_post'] }} @lang('Posts')</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 pt-4">
        <div class="col-lg-7">
            <div class="card">
                <div class="chart__topbar">
                    <h5>@lang('Total Posts by Platform')</h5>
                </div>
                <div class="row g-2">
                    <div class="col-xl-4 col-sm-6">
                        <div class="post__statistics">
                            <h4>{{ $widget['facebook'] }}</h4>
                            <p>@lang('Facebook Post')</p>
                            <a href="{{ route('user.posts.index') }}">@lang('View All')</a>
                        </div>
                    </div>
                    <div class="col-xl-4 col-sm-6">
                        <div class="post__statistics">
                            <h4>{{ $widget['instagram'] }}</h4>
                            <p>@lang('Instagram Post')</p>
                            <a href="{{ route('user.posts.index') }}">@lang('View All')</a>
                        </div>
                    </div>
                    <div class="col-xl-4 col-sm-6">
                        <div class="post__statistics">
                            <h4>{{ $widget['twitter'] }}</h4>
                            <p>@lang('Twitter Post')</p>
                            <a href="{{ route('user.posts.index') }}">@lang('View All')</a>
                        </div>
                    </div>
                    <div class="col-xl-4 col-sm-6">
                        <div class="post__statistics">
                            <h4>{{ $widget['linkedin'] }}</h4>
                            <p>@lang('Linkedin Post')</p>
                            <a href="{{ route('user.posts.index') }}">@lang('View All')</a>
                        </div>
                    </div>
                    <div class="col-xl-4 col-sm-6">
                        <div class="post__statistics">
                            <h4>{{ $widget['tiktok'] }}</h4>
                            <p>@lang('Tiktok Post')</p>
                            <a href="{{ route('user.posts.index') }}">@lang('View All')</a>
                        </div>
                    </div>
                    <div class="col-xl-4 col-sm-6">
                        <div class="post__statistics">
                            <h4>{{ $widget['youtube'] }}</h4>
                            <p>@lang('Youtube Post')</p>
                            <a href="{{ route('user.posts.index') }}">@lang('View All')</a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card">
                <div class="chart__topbar">
                    <h5>@lang('Published Posts by Platform')</h5>
                </div>
                <div class="post__by__platform">
                    <div id="post__statistics__chart"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-lg-12">
            <div class="card">
                <div class="dh__title">
                    <h5>@lang('Recent Post')</h5>
                </div>
                <div class="dashboard__table">
                    <table class="table table--responsive--lg">
                        <thead>
                            <tr>
                                <th>@lang('S.L')</th>
                                <th>@lang('Account')</th>
                                <th>@lang('Title')</th>
                                <th>@lang('Schedule')</th>
                                <th>@lang('Created At')</th>
                                <th>@lang('Status')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentPosts as $loop => $post)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>

                                    <td>
                                        <div class="d-flex gap-2 align-items-center">
                                            <div class="dc__account__img">
                                                <img class="fit--img"
                                                    src="{{ getImage(getFilePath('platform') . '/' . $post->socialAccount->platform->image, getFileSize('platform')) }}"
                                                    alt="image">
                                                <img class="fit--img dc__platfrom"
                                                    src="{{ $post->socialAccount->profile_image != null ? $post->socialAccount->profile_image : getImage('assets/images/general/avatar.png') }}"
                                                    alt="">
                                            </div>
                                            <div class="table__plt">
                                                <h4>{{ $post->socialAccount->profile_name }}</h4>
                                                <p>{{ $post?->socialAccount?->platform?->name ?? '' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ strLimit(__($post->title), 10) }}</td>

                                    <td>@php echo $post->scheduleStatusBadge; @endphp
                                        @if($post->is_schedule)
                                            <br>
                                            {{ showDateTime($post->schedule_datetime, 'd M Y, H:i:A') }}
                                        @endif
                                    </td>

                                    <td>{{ showDateTime($post->created_at, 'd M Y, H:i:A') }}</td>

                                    <td>@php echo $post->statusBadge; @endphp</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="100%" class="text-center">{{ __($emptyMessage) }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection


@push('script-lib')
    <script src="{{ asset('assets/admin/js/apexcharts.min.js') }}"></script>
@endpush

@push('script')
    <script>
        (function ($) {
            'use strict';
            if ($('#post__statistics__chart').length) {
                var options = {
                    series: {!! json_encode($series) !!},
                    chart: {
                        type: 'donut',
                        width: "100%",
                    },
                    labels: {!! json_encode($labels) !!},
                    colors: ['#1D4ED8', '#0EA5E9', '#F43F5E', '#10B981', '#F59E0B'],
                    legend: {
                        position: 'bottom',
                    },
                    dataLabels: {
                        enabled: true,
                        formatter: function (val) {
                            return val.toFixed(1) + "%";
                        }
                    }
                };

                var chart = new ApexCharts(document.querySelector("#post__statistics__chart"), options);
                chart.render();
            }
        })(jQuery)
    </script>
@endpush