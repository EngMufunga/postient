@extends('Template::layouts.master')
@section('content')
<div class="row g-4 justify-content-center">
    @if(gs('free_post_status'))
    <div class="col-xl-4 col-md-6">
        <div class="pricing__card bg--white">
            <img class="sparkle__sp" src="{{ asset($activeTemplateTrue . 'images/sparkle-outline.svg') }}" alt="image">
            <div class="pricing__topbar">
                <span class="bg--pink"><img src="{{ asset($activeTemplateTrue . 'images/sparkle.svg') }}" alt="image"></span>
                <h5>@lang('Free Plan')</h5>
                <p>{{ gs('featured_text') }}</p>
            </div>
            <div class="pricing__include">
                <span>@lang('What\'s Included')</span>
            </div>
            <div class="pricing__content">
                <ul>
                    <li>
                        <i class="fa-solid fa-circle-check text--success"></i> {{ gs('schedule_post_count') }} @lang('Social Post')
                    </li>
                    <li>
                        <i class="fa-solid fa-circle-check text--success"></i> {{ gs('connected_profile') }} @lang('Social Profile Connection')
                    </li>

                    @if(count(getActivePlatformNames()) > 0)
                    <li>
                        <i class="fa-solid fa-circle-check text--success"></i> @lang('Platform Access')
                        <div class="d-flex flex-wrap gap-2 mt-2">
                            @forelse(getActivePlatformNames() as $index => $data)
                            <span class="badge badge--base custom--badge">
                                {{ ucfirst($data) }}
                            </span>
                            @empty
                            <span class="text-muted">@lang('No platforms available')</span>
                            @endforelse
                        </div>
                    </li>
                    @endif

                    @php
                        $gContents = is_array(gs('plan_contents')) ? gs('plan_contents') : json_decode(gs('plan_contents'), true);
                    @endphp

                    @forelse($gContents as $gdata)
                        <li><i class="fa-solid fa-circle-check text--success"></i> {{ __($gdata) }}</li>
                    @empty

                    @endforelse

                </ul>
                <a href="{{ auth()->user()->free_plan_used ? 'javascript:void(0)' : route('user.free.plan.subscription') }}" class="btn btn-outline--base w-100">{{ auth()->user()->free_plan_used ? __('SUBSCRIBED') : __('SUBSCRIBE NOW') }}</a>
            </div>
        </div>
    </div>
    @endif



    @forelse($plans as $index =>$plan)
        @php
            $colors = ['bg--success', 'bg--danger', 'bg--warning', 'bg--pink', 'bg--primary'];

            $dotClass = $colors[$index % count($colors)];
        @endphp

    <div class="col-xl-4 col-md-6">
        <div class="pricing__card bg--white">
            @if($plan->feature_status)
            <span class="pricing__label">@lang('Popular')</span>
            @endif
            <img class="sparkle__sp" src="{{ asset($activeTemplateTrue . 'images/sparkle-outline.svg') }}" alt="image">
            <div class="pricing__topbar">
                <span class="{{ $index  ? $dotClass : 'bg--info' }}"><img src="{{ asset($activeTemplateTrue . 'images/sparkle.svg') }}" alt="image"></span>
                <h5>{{ __($plan->name) }}</h5>
                <p>{{ __($plan->short_description) }}</p>
                <h3>{{ __($general->cur_sym) }}{{ showAmount($plan->price, 2) }}<span>/{{ $plan->type == 1 ? trans('mon') : trans('yer') }}</span></h3>
            </div>
            <div class="pricing__include">
                <span>@lang('What\'s Included')</span>
            </div>
            <div class="pricing__content">
                <ul>
                    <li>
                        <i class="fa-solid fa-circle-check text--success"></i> {{ $plan->schedule_post }} @lang('Social Post')
                    </li>
                    <li>
                        <i class="fa-solid fa-circle-check text--success"></i> {{ $plan->connected_profile }} @lang('Social Profile Connection')
                    </li>

                        @php
                            $platforms = getPlatformNames($plan->platform_access);
                        @endphp

                    @if(count($platforms) > 0)
                    <li>
                        <i class="fa-solid fa-circle-check text--success"></i> @lang('Platform Access')
                        <div class="d-flex flex-wrap gap-2 mt-2">
                            @forelse($platforms as $index => $data)
                            <span class="badge badge--base custom--badge">
                                {{ ucfirst($data) }}
                            </span>
                            @empty
                            <span class="text-muted">@lang('No platforms available')</span>
                            @endforelse
                        </div>
                    </li>
                    @endif

                    @if($plan->schedule_status)
                        <li><i class="fa-solid fa-circle-check text--success"></i> @lang('Schedule posting available')</li>
                    @else
                        <li><i class="fa-solid fa-circle-check text--danger"></i> @lang('Schedule posting not available')</li>
                    @endif

                    @if($plan->ai_assistant_status)
                        <li><i class="fa-solid fa-circle-check text--success"></i> @lang('Generate ') {{ $plan->generated_content_count }} @lang('content via AI')</li>
                    @else
                        <li><i class="fa-solid fa-circle-xmark text--danger"></i> @lang('AI Assistant not available')</li>
                    @endif

                    @php
                        $contents = is_array($plan->contents) ? $plan->contents : json_decode($plan->contents, true);
                    @endphp

                    @forelse($contents as $data)
                        <li><i class="fa-solid fa-circle-check text--success"></i> {{ __($data) }}</li>
                    @empty

                    @endforelse
                </ul>
                @if(auth()->user()->plan_id == $plan->id)
                <span class="btn btn-outline--base w-100">@lang('SUBSCRIBED')</span>
                @else
                <a href="{{ route('user.plan.payment', $plan->id) }}" class="btn btn-outline--base w-100">@lang('SUBSCRIBE NOW')</a>
                @endif
            </div>
        </div>
    </div>

    @empty

    @endforelse

</div>

@endsection

@push('breadcrumb-plugins')
<form>
    <div class="search__box">
        <input type="search" class="form-control" name="search" value="{{ request()->search }}" placeholder="@lang('Search by plan, price')">
        <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
    </div>
</form>
@endpush
