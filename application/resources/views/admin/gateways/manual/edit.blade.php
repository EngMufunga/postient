@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card br--solid radius--base bg--white mb-4">
                <div class="card-body">
                    <form action="{{ route('admin.gateway.manual.update', $method->code) }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="payment-method-item">
                            <div class="row mb-5">
                                <div class="col-lg-4 col-sm-12">
                                    <h5 class="mb-4">@lang('Gateway Image')</h5>
                                    <div class="logo-upload--box">
                                        <x-image-uploader name="image" :imagePath="getImage(
                                            getFilePath('paymentGateway') . '/' . $method->image,
                                            getFileSize('paymentGateway'),
                                        )" :size="getFileSize('paymentGateway')" :isImage="true"
                                            class="w-100" id="uploadLogo3" :required="false" />
                                    </div>
                                </div>

                                <div class="col-md-8 col-sm-12">
                                    <div class="row">
                                        <div class="col-md-4 col-sm-12">
                                            <div class="form-group">
                                                <label>@lang('Name')</label>
                                                <input type="text" class="form-control" name="name"
                                                    value="{{ $method->name }}" required />
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-12">
                                            <div class="form-group">
                                                <label>@lang('Currency')</label>
                                                <div class="input-group">
                                                    <input type="text" name="currency"
                                                        class="form-control border-radius-5"
                                                        value="{{ $method->singleCurrency->currency ?? '' }}" required />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-12">
                                            <div class="form-group">
                                                <label>@lang('Dollar Rate')</label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg--primary text--white">1
                                                        {{ __($general->cur_text) }} =</span>
                                                    <input type="text" class="form-control" name="rate"
                                                        value="{{ getAmount($method->singleCurrency->rate ?? 0) }}"
                                                        required />
                                                    <span class="currency_symbol input-group-text bg--primary text--white"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="card border mb-2">
                                                <h5 class="card-header">@lang('Limit')</h5>
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <label>@lang('Min')</label>
                                                        <div class="input-group">
                                                            <input type="number" class="form-control" name="min_limit"
                                                                value="{{ getAmount($method->singleCurrency->min_amount ?? 0) }}"
                                                                min="0" step="1" required />
                                                            <span class="input-group-text bg--primary text--white">
                                                                {{ __($general->cur_text) }} </span>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>@lang('Max')</label>
                                                        <div class="input-group">
                                                            <input type="number" class="form-control" name="max_limit"
                                                                value="{{ getAmount($method->singleCurrency->max_amount ?? 0) }}"
                                                                min="0" step="1" required />
                                                            <span class="input-group-text bg--primary text--white">
                                                                {{ __($general->cur_text) }} </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="card border">
                                                <h5 class="card-header">@lang('Transaction Charge')</h5>
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <label>@lang('Fixed')</label>
                                                        <div class="input-group">
                                                            <input type="number" class="form-control" name="fixed_charge"
                                                                value="{{ getAmount($method->singleCurrency->fixed_charge ?? 0) }}"
                                                                required min="0" step="any" />
                                                            <span class="input-group-text bg--primary text--white">
                                                                {{ __($general->cur_text) }} </span>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>@lang('Percentage')</label>
                                                        <div class="input-group">
                                                            <input type="number" class="form-control" name="percent_charge"
                                                                value="{{ getAmount($method->singleCurrency->percent_charge ?? 0) }}"
                                                                min="0" step="any" required>
                                                            <span class="input-group-text bg--primary text--white">%</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="payment-method-body">
                                <div class="row gy-4">

                                    <div class="col-lg-12">
                                        <h5 class="mb-4">@lang('Special Instructions') </h5>
                                        <div class="form-group">
                                            <textarea rows="3" class="form-control trumEdit border-radius-5" name="instruction">{{ $method->description }}</textarea>
                                        </div>
                                    </div>

                                    <div class="col-lg-12">
                                        <div class="d-flex justify-content-between mb-4">
                                            <h5>@lang('User Input Fields')</h5>
                                            <button type="button"
                                                class="btn btn-sm btn--primary float-end form-generate-btn">
                                                <i class="fa-solid fa-plus"></i> @lang('Add New')
                                            </button>
                                        </div>
                                        <div class="row gy-4 addedField">
                                            @if ($form)
                                                @foreach ($form->form_data as $formData)
                                                    <div class="col-md-4">
                                                        <div class="card radius--base br--solid" id="{{ $loop->index }}">
                                                            <input type="hidden"
                                                                name="form_generator[is_required][]"
                                                                value="{{ $formData->is_required }}">
                                                            <input type="hidden"
                                                                name="form_generator[extensions][]"
                                                                value="{{ $formData->extensions }}">
                                                            <input type="hidden" name="form_generator[options][]"
                                                                value="{{ implode(',', $formData->options) }}">
                                                            @php
                                                                $jsonData = json_encode([
                                                                    'type' => $formData->type,
                                                                    'is_required' => $formData->is_required,
                                                                    'label' => $formData->name,
                                                                    'extensions' =>
                                                                        explode(',', $formData->extensions) ??
                                                                        'null',
                                                                    'options' => $formData->options,
                                                                    'old_id' => '',
                                                                ]);
                                                            @endphp
                                                            <div class="card-body">
                                                                <div class="button--group d-flex align-items-center justify-content-end gap-2 gap-2">
                                                                    <button type="button"
                                                                        class="edit--btn editFormData"
                                                                        data-form_item="{{ $jsonData }}"
                                                                        data-update_id="{{ $loop->index }}"><i
                                                                            class="fa-solid fa-pen-to-square"></i></button>
                                                                    <button type="button"
                                                                        class="btn btn--danger removeFormData"><i class="fa-regular fa-trash-can"></i></button>
                                                                </div>

                                                                <div class="form-group">
                                                                    <label>@lang('Label')</label>
                                                                    <input type="text"
                                                                        name="form_generator[form_label][]"
                                                                        class="form-control"
                                                                        value="{{ $formData->name }}" readonly>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label>@lang('Type')</label>
                                                                    <input type="text"
                                                                        name="form_generator[form_type][]"
                                                                        class="form-control"
                                                                        value="{{ $formData->type }}" readonly>
                                                                </div>

                                                            </div>

                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn--primary">@lang('Save')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <x-form-generator></x-form-generator>
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('admin.gateway.manual.index') }}" class="btn btn-sm btn--primary"><i
            class="fa-solid fa-arrow-left"></i> @lang('Back') </a>
@endpush

@push('style')
    <style>
        .trumbowyg-box,
        .trumbowyg-editor {
            min-height: 239px !important;
            height: 239px;
        }

        .btn-sm {
            line-height: 5px;
        }
    </style>
@endpush

@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/common/css/select2.min.css') }}">
@endpush

@push('script-lib')
    <script src="{{ asset('assets/common/js/select2.min.js') }}"></script>
@endpush


@push('script')
    <script>
        "use strict"
        var formGenerator = new FormGenerator();
        formGenerator.totalField = {{ $form ? count((array) $form->form_data) : 0 }}
    </script>

    <script src="{{ asset('assets/common/js/form_actions.js') }}"></script>
@endpush




@push('script')
    <script src="{{ asset('assets/common/js/ckeditor.js') }}"></script>

    <script>
        (function($) {
            "use strict";

            $('input[name=currency]').on('input', function() {
                $('.currency_symbol').text($(this).val());
            });
            $('.currency_symbol').text($('input[name=currency]').val());

            @if (old('currency'))
                $('input[name=currency]').trigger('input');
            @endif

            document.querySelectorAll('.trumEdit').forEach(element => {
                ClassicEditor
                    .create(element)
                    .then(editor => {
                        window.editor = editor;
                    })
                    .catch(error => {
                        console.error(error);
                    });
            });
        })(jQuery);
    </script>
@endpush
