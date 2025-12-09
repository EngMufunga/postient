@extends('Template::layouts.master')
@section('content')

    <div class="row g-4 justify-content-center">
        @forelse($platforms as $platform)
        <div class="col-xl-3 col-md-4 col-sm-6">
            <div class="add__account {{ checkPlanPlatform($platform->name) == false ? 'premium-locked' : '' }}">
                <span class="premium--btn"><i class="fa-solid fa-crown"></i> @lang('Premium Feature')</span>
                <img class="dc__img" src="{{ getImage($activeTemplateTrue . 'images/star-outline.png') }}" alt="image">
                <div class="add__account__topbar">
                    <img src="{{ getImage(getFilePath('platform') . '/' . $platform->image, getFileSize('platform')) }}" alt="@lang('Image')">
                    <h4>{{ __($platform->name) }}</h4>
                </div>

                <div class="add__account__btn">
                    <a href="{{ route('user.social.account.connect', strtolower($platform->name == 'linkedin' ? 'linkedin-openid' : $platform->name)) }}" class="btn btn--base"><i class="fa-solid fa-plus"></i> @lang('Connect')</a>
                </div>
            </div>
        </div>
        @empty

        @endforelse
    </div>





    @if($accounts->isNotEmpty())
    <div class="row mt-3">
        <div class="col-lg-12">
            <div class="card">
                <h4>@lang('Connected Accounts')</h4>
                <div class="dashboard__table">
                    <table class="table table--responsive--lg">
                        <thead>
                            <tr>
                                <th>@lang('S.L')</th>
                                <th class="text-start">@lang('Account')</th>
                                <th>@lang('Total Post')</th>
                                <th>@lang('Connected At')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($accounts as $loop=>$data)
                            <tr>
                                <td>{{ $loop->iteration }}</td>

                                <td>
                                    <div class="d-flex gap-2 align-items-center">
                                        <div class="dc__account__img">
                                            <img class="fit--img" src="{{ getImage(getFilePath('platform') . '/'. $data?->platform?->image, getFileSize('platform')) }}" alt="@lang('Image')">
                                            <img class="fit--img dc__platfrom" src="{{ $data->profile_image != null ? $data->profile_image : getImage('assets/images/general/avatar.png') }}" alt="@lang('Image')">
                                        </div>
                                        <div class="table__plt">
                                            <h4>{{ $data->profile_name }}</h4>
                                            <p>{{ $data?->platform?->name ?? '' }}</p>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    {{ $data->posts_count }}
                                </td>

                                <td>{{ showDateTime($data->created_at, 'd M Y, H:i:A') }}</td>
                            </tr>
                            @empty

                            @endforelse

                        </tbody>
                    </table>
                </div>


                @if($accounts->hasPages())
                <div class="row">
                    <div class="col-lg-12">
                        {{ $accounts->links() }}
                    </div>
                </div>
                @endif


            </div>
        </div>
    </div>
    @endif

@endsection



@push('style')
<style>
    .add__account.premium-locked {
        position: relative;
        z-index: 1;
        overflow: hidden;
    }

    .add__account.premium-locked .btn{
        z-index: -1;
    }

    .premium--btn {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background-color: hsl(var(--warning));
        color: hsl(var(--white));
        padding: 10px 15px;
        border-radius: 6px;
        font-weight: bold;
        font-size: 0.9rem;
        z-index: 2;
        cursor: pointer;
        display: none;
        overflow: hidden;
    }

    .premium--btn::after {
        position: absolute;
        left: -10%;
        top: 50%;
        transform: translateY(-50%) rotate(-25deg);
        width: 10px;
        height: 150%;
        background-color: hsl(var(--white) / 0.6);
        content: "";
        z-index: -1;
        filter: blur(10px);
        animation: premium-shine 1.5s ease 0s infinite normal none;
    }

    @keyframes premium-shine {
        0% {
            left: -10%;
        }

        80% {
            left: 120%;
        }
        100% {
            left: 120%;
        }
    }

    .add__account.premium-locked .premium--btn{
        display: block;
    }

    .add__account.premium-locked::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: hsl(var(--base-d-700) / 0.2);
        backdrop-filter: blur(2px);
        z-index: 1;
        cursor: pointer;
    }

</style>
@endpush

@push('script')

<script>
    $(document).on('click', '.premium-locked', function(e) {
        // Optional: prevent accidental link clicks inside
        e.preventDefault();

        // Redirect to the upgrade page
        window.location.href = "{{ route('user.plans') }}";
    });

</script>
@endpush
