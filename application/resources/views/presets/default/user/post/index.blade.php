@extends('Template::layouts.master')
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="dashboard__table">
                    <table class="table table--responsive--lg">
                        <thead>
                            <tr>
                                <th>@lang('S.L')</th>
                                <th class="text-start">@lang('Account')</th>
                                <th>@lang('Title')</th>
                                <th>@lang('Schedule')</th>
                                <th>@lang('Created At')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($posts as $loop => $post)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>

                                    <td>
                                        <div class="d-flex gap-2 align-items-center">
                                            <div class="dc__account__img">
                                                <img class="fit--img"
                                                    src="{{ getImage(getFilePath('platform') . '/' . $post?->socialAccount?->platform?->image, getFileSize('platform')) }}"
                                                    alt="image">
                                                <img class="fit--img dc__platfrom"
                                                    src="{{ $post?->socialAccount?->profile_image != null ? $post?->socialAccount?->profile_image : getImage('assets/images/general/avatar.png') }}"
                                                    alt="">
                                            </div>
                                            <div class="table__plt">
                                                <h4>{{ $post?->socialAccount?->profile_name }}</h4>
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

                                    <td>
                                        <div class="table__action">
                                            <div class="dropdown">
                                                <button class="dropdown-toggle" type="button" data-bs-toggle="dropdown"
                                                    aria-expanded="false">
                                                    <i class="fa-solid fa-ellipsis-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    @if($post->status != Status::PUBLISH)
                                                        <li><a class="dropdown-item"
                                                                href="{{ route('user.posts.edit', $post->id) }}">@lang('Edit')</a>
                                                        </li>
                                                    @endif
                                                    <li><a class="dropdown-item confirmationBtn" href="javascript:void(0)"
                                                            data-action="{{ route('user.posts.delete', $post->id) }}"
                                                            data-question="@lang('Are you sure to delete this post?')">@lang('Delete')</a>
                                                    </li>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="100%" class="text-center">{{ __($emptyMessage) }}</td>
                                </tr>
                            @endforelse

                        </tbody>
                    </table>
                </div>


                @if($posts->hasPages())
                    <div class="row">
                        <div class="col-lg-12">
                            {{ $posts->links() }}
                        </div>
                    </div>
                @endif


            </div>
        </div>
    </div>

    <x-confirmation-modal></x-confirmation-modal>
@endsection

@push('breadcrumb-plugins')
    <form>
        <div class="search__box">
            <input type="search" class="form-control" name="search" value="{{ request()->search }}"
                placeholder="@lang('Search by platform, account, title')">
            <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
        </div>
    </form>
@endpush