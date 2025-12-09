@extends('admin.layouts.app')
@section('panel')
    <div class="row gy-4">
        <div class="col-lg-12">
            <div class="card br--solid p-16 radius--base">
                <div class="card-header border-0 d-flex justify-content-between align-items-center px-0 pt-0 pb-3">
                    <h6 class="fs-5 fw-medium">@lang('Main Pages')</h6>

                    <button type="button" class="btn btn-sm btn--primary addBtn"><i class="fa-solid fa-plus"></i> @lang('Add New')</button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two custom-data-table">
                            <thead>
                                <tr>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Slug')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pdata as $k => $data)
                                    <tr>
                                        <td>{{ __($data->name) }}</td>
                                        <td>{{ __($data->slug) }}</td>

                                        <td>
                                            <div class="button--group d-flex align-items-center justify-content-end gap-2 pe-3">
                                                <a title="@lang('Edit')" href="{{ route('admin.frontend.manage.section', $data->id) }}" class="edit--btn">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </a>

                                                @if ($data->is_default == 0)
                                                    <button title="@lang('Delete')"
                                                        class="btn btn-sm btn--danger ms-3 confirmationBtn"
                                                        data-action="{{ route('admin.frontend.manage.pages.delete', $data->id) }}"
                                                        data-question="@lang('Are you sure to remove this page?')">
                                                        <i class="fa-regular fa-trash-can"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table><!-- table end -->
                    </div>
                </div>
            </div><!-- card end -->
        </div>
        <div class="col-lg-12">
            <div class="card br--solid p-16 radius--base">

                <div class="card-header border-0 d-flex justify-content-between align-items-center px-0 pt-0 pb-3">
                    <h6 class="fs-5 fw-medium">@lang('Policy Pages')</h6>
                    <a href="{{ route('admin.frontend.sections.element', $key) }}" class="btn btn-sm btn--primary">
                        <i class="fa-solid fa-plus"></i> @lang('Add New')
                    </a>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two custom-data-table">
                            <thead>
                                <tr>
                                    <th>@lang('SL')</th>
                                    @if ($section->element?->images ?? false)
                                        <th>@lang('Image')</th>
                                    @endif
                                    @foreach ($section->element as $k => $type)
                                        @if ($k != 'modal')
                                            @if ($type == 'text' || $type == 'icon')
                                                <th>{{ __(keyToTitle($k)) }}</th>
                                            @elseif($k == 'select')
                                                <th>{{ keyToTitle($section?->element?->$k?->name ?? '') }}</th>
                                            @endif
                                        @endif
                                    @endforeach
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody class="list">
                                <tr>
                                    <td>1</td>
                                    <td>@lang('Cookie Policy')</td>
                                    <td><a title="@lang('Edit')" href="{{ route('admin.setting.cookie') }}"
                                            class="edit--btn"><i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                    </td>
                                </tr>
                                @forelse($elements as $data)
                                    <tr>
                                        <td>{{ $loop->iteration + 1 }}</td>
                                        @if(optional($section->element)->images)
                                            @php $firstKey = collect($section->element->images)->keys()[0]; @endphp
                                            <td>
                                                <div class="customer-details d-block">
                                                    <a href="javascript:void(0)" class="thumb">
                                                        <img src="{{ getImage('assets/images/frontend/' . $key . '/' . $data->data_values->$firstKey, $section->element->images->$firstKey->size) }}"
                                                            alt="@lang('image')">
                                                    </a>
                                                </div>
                                            </td>
                                        @endif
                                        @foreach ($section->element as $k => $type)
                                            @if ($k != 'modal')
                                                @if ($type == 'text' || $type == 'icon')
                                                    @if ($type == 'icon')
                                                        <td>@php echo $data->data_values->$k; @endphp</td>
                                                    @else
                                                        <td>{{ __($data->data_values->$k) }}</td>
                                                    @endif
                                                @elseif($k == 'select')
                                                    @php
                                                        $dataVal = $section->element->$k->name;
                                                    @endphp
                                                    <td>{{ $data->data_values->$dataVal }}</td>
                                                @endif
                                            @endif
                                        @endforeach
                                        <td>
                                            <div class="button--group">
                                                @if ($section->element->modal)
                                                    @php
                                                        $images = [];
                                                        if ($section->element->images) {
                                                            foreach ($section->element->images as $imgKey => $imgs) {
                                                                $images[] = getImage('assets/images/frontend/' . $key .'/' .$data->data_values->$imgKey, $section->element->images->$imgKey->size);
                                                            }
                                                        }
                                                    @endphp
                                                    <button title="@lang('Edit')"
                                                        class="edit--btn updateBtn"
                                                        data-id="{{ $data->id }}"
                                                        data-all="{{ json_encode($data->data_values) }}"
                                                        @if(optional($section->element)->images) data-images="{{ json_encode($images) }}" @endif>
                                                        <i class="fa-solid fa-pen-to-square"></i>
                                                    </button>
                                                @else
                                                    <a title="@lang('Edit')"
                                                        href="{{ route('admin.frontend.sections.element', [$key, $data->id]) }}"
                                                        class="edit--btn"><i class="fa-solid fa-pen-to-square"></i>
                                                    </a>
                                                @endif
                                                <button title="@lang('Remove')"
                                                    class="btn btn-sm btn--danger confirmationBtn"
                                                    data-action="{{ route('admin.frontend.remove', $data->id) }}"
                                                    data-question="@lang('Are you sure to remove this item?')">
                                                    <i class="fa-regular fa-trash-can"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty

                                @endforelse
                            </tbody>
                        </table><!-- table end -->
                    </div>
                </div>
            </div><!-- card end -->
        </div>
    </div>


    {{-- Add METHOD MODAL --}}
    <div id="addModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"> @lang('Add New Page')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">

                    </button>
                </div>
                <form action="{{ route('admin.frontend.manage.pages.save') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label> @lang('Page Name')</label>
                            <input type="text" class="form-control" name="name" value="{{ old('name') }}" required>
                        </div>
                        <div class="form-group">
                            <label> @lang('Slug')</label>
                            <input type="text" class="form-control" name="slug" value="{{ old('slug') }}" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary">@lang('Save')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <x-confirmation-modal></x-confirmation-modal>
@endsection

@push('breadcrumb-plugins')
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            $('.addBtn').on('click', function() {
                var modal = $('#addModal');
                modal.find('input[name=id]').val($(this).data('id'))
                modal.modal('show');
            });

        })(jQuery);
    </script>
@endpush
