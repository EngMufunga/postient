@extends('admin.layouts.app')

@section('panel')
    <div class="row gy-4 justify-content-between mb-3 pb-3">
        <div class="col-xl-4 col-md-6">
            <div class="d-flex flex-wrap justify-content-start w-100">
                <form class="form-inline w-100">
                    <div class="search-input--wrap position-relative">
                        <input type="text" name="search" class="form-control" placeholder="@lang('Search')..." value="{{ request()->search ?? '' }}">
                        <button class="search--btn position-absolute"><i class="fa fa-search"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="row gy-4">
        <div class="col-md-12 mb-30">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two custom-data-table">
                            <thead>
                                <tr>
                                    <th>@lang('S.L')</th>
                                    <th class="text-start">@lang('Platforms')</th>
                                    <th>@lang('Title')</th>
                                    <th>@lang('Schedule')</th>
                                    <th>@lang('Status')</th>
                                </tr>
                            </thead>
                            <tbody id="items_table__body">
                                @forelse($posts as $loop=>$post)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>


                                    <td>
                                        <div class="d-flex gap-2 align-items-center">
                                            <div class="dc__account__img">
                                                <img src="{{ getImage(getFilePath('platform') . '/'. $post?->socialAccount?->platform->image, getFileSize('platform')) }}" class="fit-image" alt="@lang('Platform')">
                                                <img class="dc__platfrom" src="{{ $post?->socialAccount?->profile_image != null ? $post?->socialAccount?->profile_image : getImage('assets/images/general/avatar.png') }}" class="fit-image" alt="">
                                            </div>
                                            <div class="table__plt">
                                                <h4>{{ $post?->socialAccount?->platform?->name ?? '' }}</h4>
                                                <p>{{ $post->socialAccount->profile_name }}</p>
                                            </div>
                                        </div>
                                    </td>

                                    <td>{{ strLimit($post->title, 20) }}</td>

                                    <td>@php echo $post->scheduleStatusBadge; @endphp
                                        @if($post->is_schedule)
                                        <br>
                                        {{ showDateTime($post->schedule_datetime, 'd M Y, H:i:A') }}
                                        @endif
                                    </td>


                                    <td>@php echo $post->statusBadge; @endphp</td>
                                </tr>
                                @empty

                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div id="pagination-wrapper"  class="pagination__wrapper py-4 {{ $posts->hasPages() ? '' : 'd-none' }}">
                    @if ($posts->hasPages())
                    {{ paginateLinks($posts) }}
                    @endif
                </div>

            </div>
        </div>
    </div>


@endsection


