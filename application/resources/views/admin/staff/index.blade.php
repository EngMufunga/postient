@extends('admin.layouts.app')

@section('panel')

    <div class="row gy-4 justify-content-between mb-3 pb-3">
        <div class="col-xl-4 col-lg-6">
            <div class="d-flex flex-wrap justify-content-start">
                <form class="form-inline">
                    <div class="search-input--wrap position-relative">
                        <input type="text" name="search" class="form-control" placeholder="@lang('Search by name, username or email')..." value="{{ request()->search ?? '' }}">
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
                                    <th>@lang('Name')</th>
                                    <th>@lang('Email')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody id="items_table__body">
                                @include('admin.components.tables.staff_data')
                            </tbody>
                        </table>
                    </div>
                </div>

                <div id="pagination-wrapper"  class="pagination__wrapper py-4 {{ $items->hasPages() ? '' : 'd-none' }}">
                    @if ($items->hasPages())
                    {{ paginateLinks($items) }}
                    @endif
                </div>

            </div>
        </div>
    </div>

    <x-confirmation-modal></x-confirmation-modal>
@endsection


@push('breadcrumb-plugins')
    <a href="{{ route('admin.staff.create') }}" class="btn btn--primary"><i class="fa-solid fa-user-plus"></i> @lang('Add Staff')</a>
    @if(auth()->guard('admin')->user()->supperAdmin())
        <button type="button" class="btn btn--primary ms-2 confirmationBtn"  data-action="{{ route('admin.staff.seeder') }}" data-question="@lang('Are you sure to reset permission? If you reset permission, all permissions will be set to default.')"><i class="fa-solid fa-database"></i> @lang('Permission Reset')</button>
    @endif
@endpush



