@extends('admin.layouts.app')
@section('panel')
    <div class="row gy-4 justify-content-start mb-none-30">
        <div class="col-xxl-3 col-xl-3 col-lg-12">
            @include('admin.components.navigate_sidebar')
        </div>

        <div class="col-xxl-9 col-xl-9 col-lg-12 mb-30">
            <div id="app">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">

                                <div class="row justify-content-between mt-3">
                                    <div class="col-md-5">
                                        <ul>
                                            <li>
                                                <h5>@lang('Language Keywords of') {{ __($lang->name) }}</h5>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="col-md-7 mt-md-0 mt-3">
                                        <div
                                            class="d-flex flex-wrap justify-content-start justify-content-md-end align-items-end gap-2">
                                            <button type="button" data-bs-toggle="modal" data-bs-target="#searchModal"
                                                class="btn btn-sm btn--primary float-end"><i class="fas fa-search"></i>
                                                @lang('Search Key') </button>

                                            <button type="button" data-bs-toggle="modal"
                                                data-bs-target="#searchAndReplaceModal"
                                                class="btn btn-sm btn--primary float-end"><i
                                                    class="fas fa-exchange-alt"></i> @lang('Search And Replace') </button>

                                            <button type="button" data-bs-toggle="modal" data-bs-target="#addModal"
                                                class="btn btn-sm btn--primary float-end"><i class="fa fa-plus"></i>
                                                @lang('Add New Key') </button>
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-4">
                                <div class="table-responsive--sm table-responsive mb-4">
                                    <table class="table table--light style--two custom-data-table white-space-wrap"
                                        id="myTable">
                                        <thead>
                                            <tr>
                                                <th>
                                                    @lang('Key')
                                                </th>
                                                <th>
                                                    {{ __($lang->name) }}
                                                </th>

                                                <th class="w-85">@lang('Action')</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            @forelse($paginatedData as $k => $language)
                                                <tr>
                                                    <td class="white-space-wrap">{{ $k }}</td>
                                                    <td class="text-left white-space-wrap">{{ $language }}</td>


                                                    <td>
                                                        <div>
                                                            <a title="@lang('Edit')" href="javascript:void(0)"
                                                                data-bs-target="#editModal" data-bs-toggle="modal"
                                                                data-title="{{ $k }}"
                                                                data-key="{{ $k }}"
                                                                data-value="{{ $language }}"
                                                                class="editModal btn btn-sm">
                                                                <i class="fa-solid fa-pen-to-square"></i>
                                                            </a>

                                                            <a title="@lang('Remove')" href="javascript:void(0)"
                                                                data-key="{{ $k }}"
                                                                data-value="{{ $language }}" data-bs-toggle="modal"
                                                                data-bs-target="#DelModal"
                                                                class="btn btn-sm btn--danger deleteKey">
                                                                <i class="fa-solid fa-trash-can"></i>
                                                            </a>
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
                                <div id="pagination-wrapper"
                                    class="pagination__wrapper py-4 {{ $paginatedData->hasPages() ? '' : 'd-none' }}">
                                    @if ($paginatedData->hasPages())
                                        {{ paginateLinks($paginatedData) }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="addModalLabel"> @lang('Add New')</h4>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">

                                </button>
                            </div>

                            <form action="{{ route('admin.language.store.key', $lang->id) }}" method="post">
                                @csrf
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label for="key" class="fw--500">@lang('Key')</label>
                                        <input type="text" class="form-control" id="key" name="key"
                                            value="{{ old('key') }}" required>

                                    </div>
                                    <div class="form-group">
                                        <label for="value" class="fw--500">@lang('Value')</label>
                                        <input type="text" class="form-control" id="value" name="value"
                                            value="{{ old('value') }}" required>

                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn--primary w-100"> @lang('Save')</button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>


                <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="editModalLabel">@lang('Edit')</h4>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>

                            <form action="{{ route('admin.language.update.key', $lang->id) }}" method="post">
                                @csrf
                                <div class="modal-body">
                                    <div class="form-group ">
                                        <label for="inputName" class="fw--500 form-title"></label>
                                        <input type="text" class="form-control" name="value" required>
                                    </div>
                                    <input type="hidden" name="key">
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn--primary w-100">@lang('Save Change')</button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>


                <!-- Modal for DELETE -->
                <div class="modal fade" id="DelModal" tabindex="-1" role="dialog" aria-labelledby="DelModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="DelModalLabel"> @lang('Confirmation Alert!')</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>@lang('Are you sure to delete this key from this language?')</p>
                            </div>
                            <form action="{{ route('admin.language.delete.key', $lang->id) }}" method="post">
                                @csrf
                                <input type="hidden" name="key">
                                <input type="hidden" name="value">
                                <div class="modal-footer">
                                    <button type="button" class="btn btn--dark"
                                        data-bs-dismiss="modal">@lang('No')</button>
                                    <button type="submit" class="btn btn--primary">@lang('Yes')</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- Import Modal --}}
    <div class="modal fade" id="importModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">@lang('Import Language')</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-center text--danger">@lang('If you import keywords, Your current keywords will be removed
                                                                                and replaced by imported keyword')</p>
                    <div class="form-group">
                        <select class="form-control form-select select_lang" required>
                            <option value="">@lang('Select One')</option>
                            @foreach ($list_lang as $data)
                                <option value="{{ $data->id }}"
                                    @if ($data->id == $lang->id) class="d-none" @endif>{{ __($data->name) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--primary import_lang w-100"> @lang('Import Now')</button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="searchModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="addModalLabel"> @lang('Search Keyword')</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">

                    </button>
                </div>

                <form action="{{ route('admin.language.manage.search') }}">
                    <input type="hidden" name="id" value="{{ $lang->id }}">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="key" class="fw--500">@lang('SearchKey')</label>
                            <input type="text" class="form-control" id="search_key" name="search_key"
                                value="{{ old('search_key') }}" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary w-100"> @lang('Search')</button>
                    </div>
                </form>

            </div>
        </div>
    </div>


    <div class="modal fade" id="searchAndReplaceModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="addModalLabel"> @lang('Search and Replace ')</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">

                    </button>
                </div>

                <form action="{{ route('admin.language.manage.search.replace') }}">
                    <input type="hidden" name="id" value="{{ $lang->id }}">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="key" class="fw--500">@lang('Search Keyword')</label>
                            <input type="text" class="form-control" id="key" name="key"
                                value="{{ old('key') }}" required>

                        </div>
                        <div class="form-group">
                            <label for="value" class="fw--500">@lang('Replace Value')</label>
                            <input type="text" class="form-control" id="value" name="value"
                                value="{{ old('value') }}" required>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary w-100"> @lang('Replace')</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endsection


@push('breadcrumb-plugins')
    <button type="button" class="btn btn-sm btn--primary box--shadow1 importBtn">
        <i class="fa-solid fa-file-import me-2"></i>@lang('Import Keywords')
    </button>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";
            $(document).on('click', '.deleteKey', function() {
                var modal = $('#DelModal');
                modal.find('input[name=key]').val($(this).data('key'));
                modal.find('input[name=value]').val($(this).data('value'));
            });

            $(document).on('click', '.editModal', function() {
                var modal = $('#editModal');
                modal.find('.form-title').text($(this).data('title'));
                modal.find('input[name=key]').val($(this).data('key'));
                modal.find('input[name=value]').val($(this).data('value'));
            });

            $(document).on('click', '.importBtn', function() {
                $('#importModal').modal('show');
            });

            $(document).on('click', '.import_lang', function(e) {
                var id = $('.select_lang').val();

                if (id == '') {
                    notify('error', 'Invalide selection');

                    return 0;
                } else {
                    $.ajax({
                        type: "post",
                        url: "{{ route('admin.language.import.lang') }}",
                        data: {
                            id: id,
                            toLangid: "{{ $lang->id }}",
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(data) {
                            if (data == 'success') {
                                $('#importModal').modal('hide');
                                notify('success', 'Import Data Successfully');
                                setTimeout(() => {
                                    window.location.href = "{{ url()->current() }}"
                                }, 1500);
                            }
                        }
                    });
                }

            });
        })(jQuery);
    </script>
@endpush
