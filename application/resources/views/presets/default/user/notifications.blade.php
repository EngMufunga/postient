@extends('Template::layouts.master')
@section('content')
    <div class="notify__area">
        @forelse($notifications as $notification)
        <div class="notify__item @if($notification->is_read == Status::NO) unread--notification @endif">
            <div class="notify__content">
                <h6 class="title"><a href="{{ route('user.notification.read',$notification->id) }}">{{ __($notification->title) }}</a></h6>
                <span class="date"><i class="fa-regular fa-clock"></i> {{ $notification->created_at->diffForHumans() }}</span>
            </div>
            <div class="d-flex flex-wrap align-items-center justify-content-end gap-2">
                @if($notification->is_read == Status::NO)
                <a href="{{ route('user.notification.read',$notification->id) }}" class="btn btn--sm btn--info">@lang('Read')</a>
                @endif
                <a href="{{ route('user.single.notification.delete', $notification->id) }}" class="btn btn--sm btn--danger">@lang('Delete')</a>
            </div>
        </div>
        @empty
        <div class="card">
            <div class="card-body">
                <h3 class="text-center">{{ __($emptyMessage) }}</h3>
            </div>
        </div>
        @endforelse
        <div class="mt-3">
            {{ paginateLinks($notifications) }}
        </div>
    </div>

    <x-confirmation-modal></x-confirmation-modal>
@endsection
@push('breadcrumb-plugins')
    <button type="button" class="btn btn--md btn--danger confirmationBtn" data-action="{{ route('user.all.notification.delete') }}" data-question="@lang('Are you sure to delete all the notifications?')">@lang('Delete All')</button>

    <button type="button" class="btn btn--md btn--base confirmationBtn" data-action="{{ route('user.notifications.readAll') }}" data-question="@lang('Are you sure to read all the notifications?')">@lang('Mark All as Read')</button>
@endpush



