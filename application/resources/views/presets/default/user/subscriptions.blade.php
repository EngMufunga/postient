@extends('Template::layouts.master')
@section('content')
<div class="dashboard__table">
    <table class="table table--responsive--md">
        <thead>
            <tr>
                <th>@lang('S.L')</th>
                <th>@lang('Plan')</th>
                <th>@lang('Amount')</th>
                <th>@lang('Started At')</th>
                <th>@lang('Expired At')</th>
                <th>@lang('Status')</th>
            </tr>
        </thead>
        <tbody>
            @forelse($subscriptions as $loop=>$item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->plan?->name ?? '' }}</td>
                <td>{{ showAmount($item->amount) }} {{ __($general->cur_text) }}</td>
                <td>{{ showDateTime($item->started_at) }}</td>
                <td>{{ showDateTime($item->expired_at) }}</td>
                <td>
                    @php echo $item->statusBadge; @endphp
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

@if($subscriptions->hasPages())
<div class="row">
    <div class="col-lg-12">
        {{ $subscriptions->links() }}
    </div>
</div>
@endif



@endsection


@push('breadcrumb-plugins')
    <form>
        <div class="search__box">
            <input type="search" class="form-control" name="search" value="{{ request()->search }}" placeholder="@lang('Search by plan')">
            <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
        </div>
    </form>
@endpush



