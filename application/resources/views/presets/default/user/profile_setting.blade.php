@extends('Template::layouts.master')
@section('content')
    <div class="row g-4">
        <div class="col-xl-5">
            <div class="profile__left card p-4 mb-4">
                <form action="{{ route('user.profile.image.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="profile__wr">
                        <div class="profile__upload">
                            <label for="profile__change">
                                <img src="{{ getImage(getFilePath('userProfile') . '/' . $user->image, getFileSize('userProfile')) }}" alt="@lang('Image')">
                                <i class="fa-solid fa-image"></i>
                            </label>
                            <input type="file" id="profile__change" name="image" onchange="this.form.submit()" accept=".png, .jpeg, .jpg">
                        </div>
                        <h4>{{ $user->fullname }}</h4>
                    </div>
                </form>
                <ul>
                    <li>
                        <div class="profile__contact">
                            <p><i class="fa-solid fa-envelope"></i>@lang('Email')</p>
                            <a href="mailto:{{ $user->email }}">{{ $user->email }}</a>
                        </div>
                    </li>
                    <li>
                        <div class="profile__contact">
                            <p><i class="fa-solid fa-phone"></i>@lang('Mobile Number')</p>
                            <a href="tel:+{{$user->mobile}}">+{{$user->mobile}}</a>
                        </div>
                    </li>
                    <li>
                        <div class="profile__contact">
                            <p><i class="fa-solid fa-location-dot"></i>@lang('Address')</p>
                            <span>{{$user->address?->address ?? ''}}</span>
                        </div>
                    </li>
                </ul>
            </div>

            <div class="plan--spec card p-4">
                <h4>@lang('Your Subscription Plan Information')</h4>
                @if($user->free_plan_used && !$user->plan)
                    <p>@lang('You are using free plan for better service please upgrade your plan')</p>
                @endif
                <ul>
                    <li>
                        <span><i class="fa-solid fa-crown"></i> @lang('Subscription Plan'):</span><span>{{ __($user->plan?->name) }}</span>
                    </li>
                    <li>
                        <span><i class="fa-solid fa-share-nodes"></i> @lang('Connected Profile'):</span><span>{{$user->connected_profile}}</span>
                    </li>
                    <li>
                        <span><i class="fa-solid fa-blog"></i> @lang('Post Count'):</span><span>{{$user->post_count ?? ''}}</span>
                    </li>
                    <li>
                        <span><i class="fa-regular fa-clock"></i> @lang('Schedule Feature'):</span><span class="badge badge--{{ $user->schedule_status == 1 ? 'success' : 'danger' }}">{{ $user->schedule_status == 1 ? trans('YES') : trans('NO') }}</span>
                    </li>
                    <li>
                        <span><i class="fa-solid fa-wand-sparkles"></i> @lang('AI Feature'):</span><span class="badge badge--{{ $user->ai_assistant_status == 1 ? 'success' : 'danger' }}">{{ $user->ai_assistant_status == 1 ? trans('YES') : trans('NO') }}</span>
                    </li>
                    <li>
                        <span><i class="fa-solid fa-text"></i> @lang('Genderated Content'):</span><span class="badge badge--{{ $user->genderated_content == 1 ? 'success' : 'warning' }}">{{ $user->genderated_content ?? 0 }} @lang('Times')</span>
                    </li>
                    <li>
                        <span><i class="fa-solid fa-calendar-days"></i> @lang('Started Date'):</span><span>{{ showDateTime($user->started_at, 'd M, Y - h:i A')}}</span>
                    </li>
                    <li>
                        <span><i class="fa-solid fa-calendar-days"></i> @lang('End Date'):</span><span>{{ showDateTime($user->expired_at, 'd M, Y - h:i A') }}</span>
                    </li>
                </ul>
            </div>
        </div>
        <div class="col-xl-7">
            <div class="profile__wrap card p-4">
                <form class="register" action="{{ route('user.profile.update') }}" method="post">
                    @csrf
                    <div class="row g-4">
                        <div class="col-sm-6">
                            <div class="profile__form">
                                <label class="form-label">@lang('First Name')</label>
                                <input class="form-control" type="text" name="firstname" value="{{$user->firstname}}" required>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="profile__form">
                                <label class="form-label">@lang('Last Name')</label>
                                <input class="form-control" type="text" name="lastname" value="{{$user->lastname}}" required>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="profile__form">
                                <label class="form-label">@lang('Address')</label>
                                <input class="form-control" type="text" name="address" value="{{$user->address?->address ?? ''}}">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="profile__form">
                                <label class="form-label">@lang('State')</label>
                                <input class="form-control" type="text" name="state" value="{{$user->address?->state ?? ''}}">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="profile__form">
                                <label class="form-label">@lang('Zip Code')</label>
                                <input class="form-control" type="text" name="zip" value="{{$user->address?->zip ?? ''}}">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="profile__form">
                                <label class="form-label">@lang('City')</label>
                                <input class="form-control" type="text" name="city" value="{{$user->address?->city ?? ''}}">
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="profile__form">
                                <label class="form-label">@lang('Country')</label>
                                <input class="form-control" type="text" value="{{ $user->address?->country ?? ''}}" disabled>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="profile__form">
                                <button type="submit" class="btn btn--base w-100">@lang('Update')</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
