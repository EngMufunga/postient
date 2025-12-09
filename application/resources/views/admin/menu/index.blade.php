@extends('admin.layouts.app')
@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card b-radius--10 ">
            <div class="card-body p-0">
                <div class="table-responsive--md  table-responsive">
                    <table class="table table--light style--two">
                        <thead>
                            <tr>
                                <th>@lang('S.L')</th>
                                <th>@lang('Menu')</th>
                                <th>@lang('Menu Items')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody id="items_table__body">
                            @forelse($menus as $data)
                            <tr>
                                <td class="user--td">
                                    {{ $loop->iteration }}
                                </td>
                                <td>
                                    {{ __($data->name) }}
                                </td>
                                <td>
                                    {{ $data->items->count() }}
                                </td>
                                <td>
                                    <div class="d-flex justify-content-end align-items-center gap-2">
                                        <a href="{{ route('admin.menu.assign.item', $data->id) }}" class="btn btn-sm" title="@lang('Assign Items')">
                                            <i class="fa-solid fa-indent"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


