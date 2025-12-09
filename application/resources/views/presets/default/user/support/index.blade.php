@extends('Template::layouts.master')
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="dashboard__table">
                <table class="table table--responsive--md">
                    <thead>
                        <tr>
                            <th>@lang('S.L')</th>
                            <th>@lang('Subject')</th>
                            <th>@lang('Status')</th>
                            <th>@lang('Priority')</th>
                            <th>@lang('Last Reply')</th>
                            <th>@lang('Action')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($supports as $loop=>$support)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <a href="{{ route('ticket.view', $support->ticket) }}" class="fw--500"> [@lang('Ticket')#{{ $support->ticket }}] {{ __($support->subject) }} </a>
                            </td>
                            <td>
                                @php echo $support->statusBadge; @endphp
                            </td>
                            <td>
                                @php echo $support->priorityBadge; @endphp
                            </td>
                            <td>{{ diffForHumans($support->last_reply) }}</td>
                            <td>
                                <div class="table__action">
                                    <div class="dropdown">
                                        <button class="dropdown-toggle" type="button"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fa-solid fa-ellipsis-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="{{ route('ticket.view', $support->ticket) }}">@lang('View')</a></li>
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
        </div>

        @if($supports->hasPages())
        <div class="row">
            <div class="col-lg-12">
                 {{ $supports->links() }}
            </div>
        </div>
        @endif

    </div>
</div>
@endsection

@push('breadcrumb-plugins')
    <form>
        <div class="search__box">
            <input type="text" class="form-control" name="search" placeholder="@lang('Search by ticket, number')">
            <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
        </div>
    </form>
    <a href="{{route('ticket.open') }}" class="btn btn--base mb-2"><i class="fa-solid fa-plus"></i> @lang('Open Ticket')</a>
@endpush
