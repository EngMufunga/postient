@php
    $planContent = getContent('pricing.content', true);
    $plans = App\Models\Plan::where('status', Status::ENABLE)->latest()->get();
@endphp

<!--==========================  Pricing Section Start  ==========================-->
<section class="pricing__area my-120">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="section__heading mb-60 text-center">
                    <span class="text--info">{{ __($planContent->data_values->title) }}</span>
                    <h2>{{ __($planContent->data_values->heading) }}</h2>
                    <p>{{ __($planContent->data_values->subheading) }}</p>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-lg-12">
                <div class="pricing__wrap">
                    <ul class="nav nav-pills custom--tab" id="pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="pills-home-tab" data-bs-toggle="pill" data-bs-target="#pills-home" type="button" role="tab" aria-controls="pills-home" aria-selected="true">@lang('Monthly')</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false">@lang('Yearly')</button>
                        </li>
                    </ul>
                    <div class="tab-content" id="pills-tabContent">
                        <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab" tabindex="0">
                            <div class="row g-4 justify-content-center">

                                @if(gs('free_post_status'))
                                <div class="col-xl-4 col-md-6">
                                    <div class="pricing__card">
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
                                            @if(auth()->user())
                                            <a href="{{ auth()->user()->free_plan_used ? 'javascript:void(0)' : route('user.free.plan.subscription') }}" class="btn btn-outline--base w-100">{{ auth()->user()->free_plan_used ? __('SUBSCRIBED') : __('SUBSCRIBE NOW') }}</a>
                                            @else
                                            <a href="{{ route('user.free.plan.subscription') }}" class="btn btn-outline--base w-100">@lang('SUBSCRIBE NOW')</a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endif


                                @forelse($plans->where('type', Status::PLAN_MONTHLY) as $index =>$plan)
                                @php
                                $colors = ['bg--success', 'bg--danger', 'bg--warning', 'bg--pink', 'bg--primary'];

                                $dotClass = $colors[$index % count($colors)];
                                @endphp

                                <div class="col-xl-4 col-md-6">
                                    <div class="pricing__card">
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

                                            @if(auth()->check() && auth()->user()->plan_id == $plan->id)
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
                        </div>
                        <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab" tabindex="0">
                            <div class="row g-4 justify-content-center">
                                @forelse($plans->where('type', Status::PLAN_YEARLY) as $index =>$plan)

                                @php
                                    $colors = ['bg--success', 'bg--danger', 'bg--warning', 'bg--pink', 'bg--primary'];
                                    $dotClass = $colors[$index % count($colors)];
                                @endphp

                                <div class="col-xl-4 col-md-6">
                                    <div class="pricing__card">
                                        @if($plan->feature_status)
                                            <span class="pricing__label">@lang('Popular')</span>
                                        @endif
                                        <img class="sparkle__sp" src="{{ asset($activeTemplateTrue . 'images/sparkle-outline.svg') }}" alt="image">
                                        <div class="pricing__topbar">
                                            <span class="{{ $index  ? $dotClass : 'bg--info' }}"><img src="{{ asset($activeTemplateTrue . 'images/sparkle.svg') }}" alt="@lang('Image')"></span>
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
                                                    $classes = ['warning', 'success', 'danger', 'info', 'primary'];
                                                @endphp

                                                @if(count($platforms) > 0)
                                                <li>
                                                    <i class="fa-solid fa-circle-check text--success"></i> @lang('Platform Access')
                                                    <div class="d-flex flex-wrap gap-2 mt-2">
                                                        @forelse($platforms as $index => $data)
                                                        <span class="badge badge--{{ $classes[$index % count($classes)] }} custom--badge">
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
                                            @if(auth()->check() && auth()->user()->plan_id == $plan->id)
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!--==========================  Pricing Section End  ==========================-->
