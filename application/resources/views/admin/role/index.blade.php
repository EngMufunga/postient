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

        <div class="col-xl-2 col-md-6">
            <div class="d-flex justify-content-end">
                <select id="status-filter" name="status" class="form-control form-select bg--transparent outline">
                    <option value="all" {{ request()->status == 'all' ? 'selected' : '' }}>@lang('All')</option>
                    <option value="enable" {{ request()->status == 'enable' ? 'selected' : '' }}>@lang('Enable')</option>
                    <option value="disable" {{ request()->status == 'disable' ? 'selected' : '' }}>@lang('Disable')</option>
                </select>
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
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody id="items_table__body">
                                @include('admin.components.tables.role_data')
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

    {{-- ROLE MODAL --}}
    <div id="editModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Role')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <form method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label> @lang('Name')</label>
                            <input class="form-control" id="editName" name="name" type="text" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn--primary w-100 h-45" id="editBtn" type="submit">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <x-confirmation-modal></x-confirmation-modal>
@endsection

@push('breadcrumb-plugins')
    <a class="btn btn--primary add__btn" href="javascript:void(0)" data-action="{{ route('admin.role.store', ['id' => 0]) }}"><i class="fa-solid fa-user-plus"></i> @lang('Add Role')</a>
@endpush

@push('script')
    <script>
        (function ($) {
            'use strict';
            $(document).on('click', '.editBtn', function() {
                var modal = $('#editModal');
                modal.find('.modal-title').text('@lang('Update Role')');
                var name = $(this).data('name');
                $('#editName').val(name);
                modal.find('form').attr('action', $(this).data('action'));
                modal.find('#editBtn').text('@lang('Update')');
                modal.modal('show');
            });

            $('.add__btn').on('click', function() {
                var modal = $('#editModal');
                modal.find('.modal-title').text('@lang('Add New Role')');
                $('#editName').val('');
                modal.find('form').attr('action', $(this).data('action'));
                modal.find('#editBtn').text('@lang('Submit')');
                modal.modal('show');
            });

            let baseUrl = `{{ route('admin.role.index', ':status') }}`;

            $('#status-filter').on('change', function () {
                let status = $(this).val();
                let url = baseUrl.replace(':status', status);

                $.ajax({
                    url: url,
                    method: 'GET',
                    data: {
                        search: $('#search-box').val()
                    },
                    beforeSend: function () {
                        $('#items_table__body').html('<tr><td colspan="3">Loading...</td></tr>');
                    },
                    success: function (response) {
                        $('#items_table__body').html(response.html);
                        $('.card-footer').html(response.pagination);

                        if ($.trim(response.pagination) === '') {
                            $('#pagination-wrapper').addClass('d-none');
                        } else {
                            $('#pagination-wrapper').removeClass('d-none');
                        }
                    },
                    error: function (response) {
                        alert('Failed to load filtered tickets.');
                    }
                });
            });

        })(jQuery);
    </script>
@endpush
