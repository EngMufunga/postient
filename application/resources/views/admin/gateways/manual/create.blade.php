@extends('admin.layouts.app')
@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card br--solid radius--base bg--white mb-4">
            <form action="{{ route('admin.gateway.manual.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="payment-method-item">
                        <div class="row mb-4">
                            <div class="col-lg-4 col-sm-12">
                                <h5 class="mb-4">@lang('Gateway Image')</h5>
                                <div class="logo-upload--box">
                                    <x-image-uploader name="image" :imagePath="getImage(getFilePath('paymentGateway') . '/',getFileSize('paymentGateway'))" :size="getFileSize('paymentGateway')" :isImage="false" class="w-100" id="uploadLogo3" :required="true" />
                                </div>
                            </div>
                            <div class="col-md-8 col-sm-12">
                                <div class="row">
                                    <div class="col-md-4 col-sm-12">
                                        <div class="form-group">
                                            <label>@lang('Name')</label>
                                            <input type="text" class="form-control" name="name" required />
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-12">
                                        <div class="form-group">
                                            <label>@lang('Currency')</label>
                                            <div class="input-group">
                                                <input type="text" name="currency" class="form-control border-radius-5"
                                                    required />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-12">
                                        <div class="form-group">
                                            <label>@lang('Dollar Rate')</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg--primary text--white">1 {{ __($general->cur_text)}} =</span>
                                                <input type="text" class="form-control" name="rate" required />
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
                                                        <input type="text" class="form-control" name="min_limit"
                                                            required />
                                                        <span class="input-group-text bg--primary text--white"> {{
                                                            __($general->cur_text) }} </span>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label>@lang('Max')</label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" name="max_limit"
                                                            required />
                                                        <span class="input-group-text bg--primary text--white"> {{
                                                            __($general->cur_text) }} </span>
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
                                                        <input type="text" class="form-control" name="fixed_charge"
                                                            required />
                                                        <span class="input-group-text bg--primary text--white"> {{
                                                            __($general->cur_text) }} </span>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label>@lang('Percentage')</label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" name="percent_charge"
                                                            required>
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
                                        <textarea rows="3" class="form-control trumEdit border-radius-5"
                                            name="instruction"></textarea>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="card border mt-3">
                                        <div class="card-header d-flex justify-content-between">
                                            <h5>@lang('User Input Fields')</h5>
                                            <button type="button"
                                                class="btn btn-sm btn--primary float-end form-generate-btn"><i class="fa-solid fa-plus"></i> @lang('Add New')</button>
                                        </div>
                                        <div class="card-body">
                                            <div class="row addedField">

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer text-end">
                    <button type="submit" class="btn btn--primary">@lang('Save')</button>
                </div>
            </form>
        </div>
    </div>
</div>

<x-form-generator></x-form-generator>
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('admin.gateway.manual.index') }}" class="btn btn-sm btn--primary"><i class="fa-solid fa-arrow-left"></i>@lang('Back') </a>
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
    <link rel="stylesheet" href="{{asset('assets/common/css/select2.min.css')}}">
@endpush

@push('script-lib')
    <script src="{{asset('assets/common/js/select2.min.js')}}"></script>
@endpush

@push('script')
    <script>
        "use strict"
        var formGenerator = new FormGenerator();
    </script>

    <script src="{{ asset('assets/common/js/form_actions.js') }}"></script>
@endpush




@push('script')
    <script src="{{ asset('assets/common/js/ckeditor.js') }}"></script>

    <script>
        (function ($) {
            "use strict";
            $('input[name=currency]').on('input', function () {
                $('.currency_symbol').text($(this).val());
            });

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


