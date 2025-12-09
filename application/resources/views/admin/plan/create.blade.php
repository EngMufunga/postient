@extends('admin.layouts.app')
@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card br--solid radius--base bg--white mb-4">
            <form action="{{ route('admin.plan.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="name">@lang('Name')</label>
                                <input type="text" name="name" class="form-control" id="name" placeholder="@lang('Enter name')" required>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="type">@lang('Type')</label>
                                <select name="type" id="type" class="form-control form-select">
                                    <option value="1">@lang('Monthly')</option>
                                    <option value="2">@lang('Yearly')</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="price">@lang('Price')</label>
                                <input type="number" name="price" class="form-control" id="price" placeholder="@lang('Ex'). 12.50" step="any" min="0" required>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="total_profile">@lang('Connected Profile')</label>
                                <input type="number" name="connected_profile" class="form-control" id="total_profile" placeholder="@lang('Ex'). 1" step="1" min="1" required>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="total_post">@lang('Schedule Post')</label>
                                <input type="number" name="schedule_post" class="form-control" id="total_post" placeholder="@lang('Ex'). 12" step="1" min="1" required>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="platform_access">@lang('Platform Access')</label>
                                <select name="platform_access[]" id="platform_access" class="form-control form-select select2-auto-tokenize" multiple>
                                    @foreach($platforms as $key => $value)
                                        <option value="{{ $key }}">{{ ucfirst($value) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="short_desc">@lang('Short Description')</label>
                                <input type="text" name="short_description" class="form-control" id="short_desc" placeholder="@lang('Enter short description')" required>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                    <label for="name" class="font-weight-bold">@lang('Content')</label>
                                    <input type="text" name="contents[]" id="content" value="{{ old('contents.0') }}" class="form-control" placeholder="@lang('Content')">
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div id="planContent"></div>
                                </div>
                                <div class="col-12 text-end my-3">
                                    <button type="button" class="btn btn--primary addPlanContent ms-0"><i class="fa fa-plus"></i></button>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="d-flex align-items-center flex-wrap gap-3">
                                <div class="form-group mb-0 d-flex justify-content-start align-items-center">
                                    <span class="fw--500 text--dark mb-1">@lang('Feature Plan')</span>
                                    <label class="switch m-0">
                                        <input type="checkbox" class="toggle-switch" name="feature_status">
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                                <div class="form-group mb-0 d-flex justify-content-start align-items-center">
                                    <span class="fw--500 text--dark mb-1">@lang('Schedule Post')</span>
                                    <label class="switch m-0">
                                        <input type="checkbox" class="toggle-switch" name="schedule_status">
                                        <span class="slider round"></span>
                                    </label>
                                </div>

                                <div class="form-group mb-0 d-flex justify-content-start align-items-center">
                                    <span class="fw--500 text--dark mb-1">@lang('AI Assistant')</span>
                                    <label class="switch m-0">
                                        <input type="checkbox" class="toggle-switch" name="ai_assistant_status">
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group d-none">
                                <label for="generated_content_count">@lang('Generated Content')</label>
                                <input type="number" name="generated_content_count" class="form-control" id="generated_content_count" min="0" step="1">
                            </div>
                        </div>

                        <div class="col-lg-12 text-end">
                            <button type="submit" class="btn btn--primary">@lang('Save')</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('admin.plan.index') }}" class="btn btn-sm btn--primary"><i class="fa-solid fa-arrow-left"></i> @lang('Back') </a>
@endpush



@push('script-lib')
    <script src="{{asset('assets/common/js/select2.min.js')}}"></script>
@endpush

@push('style-lib')
    <link rel="stylesheet" href="{{asset('assets/common/css/select2.min.css')}}">
@endpush

@push('script')

<script>
    (function ($) {
        "use strict";

        function toggleGeneratedContent() {
            if ($('input[name="ai_assistant_status"]').is(':checked')) {
                $('#generated_content_count').closest('.form-group').removeClass('d-none');
                $('#generated_content_count').prop('required', true);
            } else {
                $('#generated_content_count').closest('.form-group').addClass('d-none');
                $('#generated_content_count').prop('required', false);
            }
        }

        toggleGeneratedContent();

        $('input[name="ai_assistant_status"]').change(function() {
            toggleGeneratedContent();
        });

        $('.select2-auto-tokenize').select2({
            dropdownParent: $('.card-body'),
            tags: true,
            tokenSeparators: [',']
        });

        var fileAdded = 0;
        $('.addPlanContent').on('click', function () {

            $("#planContent").append(`
                <div class="row">
                    <div class="col-11">
                        <div class="form-group">
                        <input type="text" name="contents[]" id="content" value="{{ old('contents.0') }}" class="form-control" placeholder="@lang('Content')">
                        </div>
                    </div>
                    <div class="col-1">
                        <button type="button" class="btn btn--danger planContentDelete"><i class="la la-times ms-0"></i></button>
                    </div>
                </div>
            `)
        });

        $(document).on('click', '.planContentDelete', function () {
            fileAdded--;
            $(this).closest('.row').remove();
        });
    })(jQuery);
</script>

@endpush






