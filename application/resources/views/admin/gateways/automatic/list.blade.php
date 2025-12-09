@extends('admin.layouts.app')
@section('panel')
    <div class="row gy-4 justify-content-between mb-3 pb-3">
        <div class="col-xl-4 col-md-6">
            <div class="d-flex flex-wrap justify-content-start w-100">
                <form class="form-inline w-100">
                    <div class="search-input--wrap position-relative">
                        <input type="search" name="search" class="form-control" placeholder="@lang('Search by gateway')..." value="{{ request()->search ?? '' }}">
                        <button class="search--btn position-absolute" type="submit"><i class="fa fa-search"></i></button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-xl-2 col-md-6">
            <div class="d-flex justify-content-end">
                <select id="table__data__filter" name="status" class="form-control form-select bg--transparent outline">
                    <option value="all" {{ request()->status == 'all' ? 'selected' : '' }}>@lang('All')</option>
                    <option value="1" {{ request()->status == '1' ? 'selected' : '' }}>@lang('Enable')</option>
                    <option value="0" {{ request()->status == '0' ? 'selected' : '' }}>@lang('Disable')</option>
                </select>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two custom-data-table">
                            <thead>
                                <tr>
                                    <th>@lang('Gateway')</th>
                                    <th>@lang('Currency')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>

                            <tbody id="item-table-body">
                                @include('admin.components.tables.automatic_data')
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

        <div id="currencyModal" class="modal fade" >
        <div class="modal-dialog" role="document">

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Supported Currency')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div id="currencyList" class="currency-list">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-confirmation-modal></x-confirmation-modal>
@endsection

@push('script')
    <script>
        (function ($) {
            'use strict';

            $('.supported_currency').on('click', function() {
                var currencies = $(this).data('supported_currency');
                $('#currencyList').empty();
                $.each(currencies, function(index, currency) {
                    $('#currencyList').append('<span class="badge badge--success me-2">' + currency + '</span>');
                });
                $('#currencyModal').css('display', 'block');
            });

            $('.enabled_currency').on('click', function() {
                var currencies = $(this).data('enabled_currency');
                $('#currencyList').empty();
                $.each(currencies, function(index, currency) {
                    $('#currencyList').append('<span class="badge badge--success me-2">' + currency + '</span>');
                });
                $('#currencyModal').css('display', 'block');
            });

            $('.close').on('click', function() {
                $('#currencyModal').css('display', 'none');
            });


            let baseUrl = `{{ route('admin.gateway.automatic.index', ':status') }}`;

            $('#table__data__filter').on('change', function () {
                let status = $(this).val();
                let url = baseUrl.replace(':status', status);


                $.ajax({
                    url: url,
                    method: 'GET',
                    data: {
                        search: $('#search-box').val()
                    },
                    beforeSend: function () {
                        $('#item-table-body').html('<tr><td colspan="4">Loading...</td></tr>');
                    },
                    success: function (response) {
                        $('#item-table-body').html(response.html);
                        $('.card-footer').html(response.pagination);

                        if ($.trim(response.pagination) === '') {
                            $('#pagination-wrapper').addClass('d-none');
                        } else {
                            $('#pagination-wrapper').removeClass('d-none');
                        }
                    },
                    error: function () {
                        alert('Failed to load filtered tickets.');
                    }
                });
            });
        })(jQuery);
    </script>
@endpush




