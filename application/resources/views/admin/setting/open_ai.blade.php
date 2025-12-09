@extends('admin.layouts.app')
@section('panel')
    <div class="row gy-4 justify-content-start mb-none-30">
        <div class="col-xxl-3 col-xl-3 col-lg-12">
            @include('admin.components.navigate_sidebar')
        </div>

        <div class="col-xxl-9 col-xl-9 col-lg-12 mb-30">
            <form action="{{ route('admin.setting.openai.update') }}" method="POST">
                @csrf
                <div class="row gy-4">
                    <div class="col-xxl-12">
                        <div class="card bg--white br--solid radius--base p-16">
                            <h5 class="mb-3">@lang('Open Ai Setup')</h5>

                            <div class="row gy-4 mb-4 pb-3">
                                <div class="col-md-12 col-xs-12">
                                    <label class="required">@lang('API Key')</label>
                                    <input class="form-control" type="text" name="api_key" required value="{{ $general->api_key }}">
                                </div>
                                <div class="col-md-6 col-xs-12">
                                    <label class="required">@lang('Max Result Length')</label>
                                    <input class="form-control" type="number" name="gpt_max_result_length" required value="{{ $general->gpt_max_result_length }}" step="1" min="100">
                                </div>
                                <div class="col-md-6 col-xs-12 time-zone">
                                    <label> @lang('GPT Model')</label>
                                    <select class="form-control form-select" name="gpt_model">
                                        @foreach ($gpt_models as $key => $data)
                                            <option value="{{ $data->name }}" @selected($general->gpt_model)>
                                                {{ $data->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col text-end">
                                    <button type="submit" class="btn btn--primary">@lang('Save Changes')</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <a class="btn btn--primary" href="{{ route('admin.setting.openai.model') }}"><i class="fa-solid fa-wand-magic-sparkles"></i> @lang('Model Import')</a>
@endpush
