@extends('admin.layouts.app')

@section('panel')
    <div class="row gy-4 justify-content-between mb-3 pb-3">

        <div class="col-lg-5">
            <form>
                <div class="row gy-4">
                    <div class="col-md-6">
                        <div class="search-input--wrap position-relative">
                            <input type="search" name="search" class="form-control" placeholder="@lang('Search by subject, ticket ID')..."
                                value="{{ request()->search }}">
                            <button class="search--btn position-absolute" type="submit"><i
                                    class="fa fa-search"></i></button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="search-input--wrap position-relative">
                            <input name="date" type="text" data-range="true" data-multiple-dates-separator=" - "
                                data-language="en" class="datepicker-here form-control" data-position='bottom right'
                                placeholder="@lang('Date from - to')" autocomplete="off" value="{{ request()->date }}">
                            <button class="search--btn position-absolute" type="submit"><i
                                    class="fa fa-search"></i></button>
                        </div>
                    </div>
                </div>
            </form>
        </div>



        <div class="col-xl-2 col-lg-6">
            <div class="d-flex justify-content-end">
                <select id="ticket-filter" name="status" class="form-control form-select bg--transparent outline">
                    <option value="all" {{ request()->status == 'all' ? 'selected' : '' }}>@lang('All')</option>
                    <option value="pending" {{ request()->status == 'pending' ? 'selected' : '' }}>@lang('Pending')
                    </option>
                    <option value="closed" {{ request()->status == 'closed' ? 'selected' : '' }}>@lang('Closed')</option>
                    <option value="answered" {{ request()->status == 'answered' ? 'selected' : '' }}>@lang('Answer')
                    </option>
                </select>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Subject')</th>
                                    <th>@lang('Opened By')</th>
                                    <th>@lang('Priority')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Created At')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody id="items-table-body">
                                @include('admin.components.tables.ticket_list')
                            </tbody>
                        </table>
                    </div>
                </div>
                <div id="pagination-wrapper" class="pagination__wrapper py-4 {{ $items->hasPages() ? '' : 'd-none' }}">
                    @if ($items->hasPages())
                        {{ paginateLinks($items) }}
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection


@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/common/css/datepicker.min.css') }}">
@endpush


@push('script-lib')
    <script src="{{ asset('assets/common/js/datepicker.min.js') }}"></script>
    <script src="{{ asset('assets/common/js/datepicker.en.js') }}"></script>
@endpush


@push('script')
    <script>
        (function($) {
            'use strict';

            if (!$('.datepicker-here').val()) {
                $('.datepicker-here').datepicker();
            }


            let baseUrl = `{{ route('admin.ticket.index', ':status') }}`;

            $('#ticket-filter').on('change', function() {
                let status = $(this).val();

                let url = baseUrl.replace(':status', status);


                $.ajax({
                    url: url,
                    method: 'GET',
                    data: {
                        search: $('#search-box').val()
                    },
                    beforeSend: function() {
                        $('#items-table-body').html('<tr><td colspan="6">Loading...</td></tr>');
                    },
                    success: function(response) {
                        $('#items-table-body').html(response.html);
                        $('.card-footer').html(response.pagination);

                        if ($.trim(response.pagination) === '') {
                            $('#pagination-wrapper').addClass('d-none');
                        } else {
                            $('#pagination-wrapper').removeClass('d-none');
                        }
                    },
                    error: function() {
                        alert('Failed to load filtered tickets.');
                    }
                });
            });

        })(jQuery);
    </script>
@endpush
