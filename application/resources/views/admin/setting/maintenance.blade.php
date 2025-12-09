@extends('admin.layouts.app')
@section('panel')
    <div class="row gy-4 justify-content-start mb-none-30">
        <div class="col-xxl-3 col-xl-3 col-lg-12">
            @include('admin.components.navigate_sidebar')
        </div>

        <div class="col-xxl-9 col-xl-9 col-lg-12 mb-30">
            <div class="card p-16 radius--base br--solid bg--white">
                <form action="{{ route('admin.setting.maintenance.update') }}" method="post">
                    @csrf
                    <div class="row justify-content-between">
                        <div class="col-md-6">
                            <h5>@lang('Maintenance Status')</h5>
                        </div>
                        <div class="col-md-2 text-end">
                            <div class="form-group text-end">
                                <label>@lang('Status')</label>
                                <label class="switch m-0">
                                    <input type="checkbox" class="toggle-switch" name="status" {{ gs()->maintenance_mode ? 'checked' : null }}>
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>@lang('Description')</label>
                        <textarea class="form-control trumEdit" rows="10" name="description">@php echo $maintenance->data_values->description ?? '' @endphp</textarea>
                    </div>


                    <div class="text-end">
                        <button type="submit" class="btn btn--primary">@lang('Save Changes')</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ asset('assets/common/js/ckeditor.js') }}"></script>

    <script>
        "use strict";
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
    </script>
@endpush


