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
                                    <th>@lang('SL')</th>
                                    <th class="text-start">@lang('User')</th>
                                    <th>@lang('Plan')</th>
                                    <th>@lang('Started At')</th>
                                    <th>@lang('Expired At')</th>
                                    <th>@lang('Status')</th>
                                </tr>
                            </thead>
                            <tbody id="items_table__body">
                                @include('admin.components.tables.subscription_data')
                            </tbody>
                        </table>
                    </div>
                </div>

                <div id="pagination-wrapper"  class="pagination__wrapper py-4 {{ $subscriptions->hasPages() ? '' : 'd-none' }}">
                    @if ($subscriptions->hasPages())
                    {{ paginateLinks($subscriptions) }}
                    @endif
                </div>

            </div>
        </div>
    </div>

@endsection



