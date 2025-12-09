@extends('Template::layouts.master')
@section('content')

@if($subscription)
<div class="row gy-4 justify-content-center">
    <div class="col-lg-12">
        <div class="card custom--card">
            <div class="card-header">
                <h5 class="card-title">@lang('Subscription Information')</h5>
            </div>
            <div class="plan--details">
                <ul>
                    <li>
                        <span><i class="fa-solid fa-circle-check text--success"></i> {{ $subscription->schedule_post ?? 0 }} @lang('Social Post')</span>
                        <span>@lang('Remaining') - {{ max(0, (int)$subscription->schedule_post - auth()->user()->post_count) }}</span>
                    </li>

                    <li>
                        <span>
                            <i class="fa-solid fa-circle-check text--success"></i>
                            {{ $subscription->connected_profile ?? 0 }} @lang('Social Profile Connection')
                        </span>
                        <span>
                            @lang('Remaining') - {{ max(0, (int)$subscription->connected_profile - auth()->user()->connected_profile) }}
                        </span>
                    </li>

                    @php
                    $platformsName = getPlatformNames($subscription->platform_access ?? []);
                    $classes = ['warning', 'success', 'danger', 'info', 'primary'];
                    @endphp

                    @if(count($platformsName) > 0)
                    <li>
                        <span><i class="fa-solid fa-circle-check text--success"></i> @lang('Platform Access')</span>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($platformsName as $index => $data)
                            <span class="badge badge--{{ $classes[$index % count($classes)] }} custom--badge">
                                {{ ucfirst($data) }}
                            </span>
                            @endforeach
                        </div>
                    </li>
                    @else
                    <li>
                        <i class="fa-solid fa-circle-xmark text--danger"></i> @lang('No platforms available')
                    </li>
                    @endif

                    @if(isset($subscription->schedule_status) && $subscription->schedule_status == Status::YES)
                    <li>
                        <span><i class="fa-solid fa-circle-check text--success"></i> @lang('Schedule posting')</span>
                        <span class="badge badge--success">@lang('Available')</span>
                    </li>
                    @else
                    <li>
                        <span><i class="fa-solid fa-circle-xmark text--danger"></i> @lang('Schedule posting')</span>
                        <span class="badge badge--warning">@lang('Not available')</span>
                    </li>
                    @endif

                    @if(isset($subscription->ai_assistant_status) && $subscription->ai_assistant_status == Status::YES)
                    <li>
                        <span><i class="fa-solid fa-circle-check text--success"></i> @lang('AI Assistant')</span>
                        <span class="badge badge--success">@lang('Generate ') {{ $subscription->generated_content_count ?? 0 }} @lang('content via AI')</span>
                    </li>
                    @else
                    <li>
                        <span><i class="fa-solid fa-circle-xmark text--danger"></i> @lang('AI Assistant')</span>
                        <span class="badge badge--warning">@lang('Not available')</span>
                    </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
    <div class="col-lg-12">
        <div class="row g-4 justify-content-center">
            @forelse($platforms as $platform)
            @if(checkPlanPlatform($platform->name))
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="add__account {{ checkPlanPlatform($platform->name) ? '' : 'premium-locked' }}">
                    <img class="dc__img" src="{{ getImage($activeTemplateTrue . 'images/star-outline.png') }}" alt="image">
                    <div class="add__account__topbar">
                        <img src="{{ getImage(getFilePath('platform') . '/' . $platform->image, getFileSize('platform')) }}" alt="@lang('Image')">
                        <h4>{{ __($platform->name) }}</h4>
                    </div>
                    <div class="add__account__btn">
                        @if(checkPlanPlatform($platform->name))
                        <a href="{{ route('user.posts.create', strtolower($platform->name)) }}" class="btn btn--base">
                            @lang('Create Post')
                        </a>
                        @else
                        <a href="{{ route('user.plans') }}" class="btn btn--premium ">
                            <i class="fas fa-crown me-1"></i>
                            @lang('Upgrade Plan to Unlock')
                        </a>
                        @endif
                    </div>
                </div>
            </div>
            @endif
            @empty

            @endforelse
        </div>
    </div>
</div>

@else
<div class="row gy-4 justify-content-center">
    <div class="col-lg-12">
        <div class="card custom--card">
            <div class="card-header">
                <h5 class="card-title">@lang('Subscription Information')</h5>
            </div>
            <div class="card-body">
                <ul class="list-group">
                    <li><i class="fa-solid fa-circle-xmark text--danger"></i> @lang('Currently you have no subscription plan. Please choose a plan to unlock all features')</li>
                </ul>

                <div class="choose_plan">
                    <a href="{{ route('user.plans') }}" class="btn btn--base mt-3">@lang('Choose Plan')</a>
                </div>
            </div>
        </div>
    </div>
</div>

@endif



@endsection



