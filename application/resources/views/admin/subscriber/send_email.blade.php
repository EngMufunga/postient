@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-xl-12">
            <div class="card br--solid radius--base p-16">
                <form action="{{ route('admin.subscriber.send.email') }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="form-group col-md-12">
                            <label class="fw--500">@lang('Subject')</label>
                            <input type="text" class="form-control" name="subject" required value="{{ old('subject') }}" />
                        </div>
                        <div class="form-group col-md-12">
                            <label class="fw--500">@lang('Body')</label>
                            <textarea name="body" rows="10" class="form-control trumEdit">{{ old('body') }}</textarea>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col text-end">
                            <button type="submit" class="btn btn--primary">@lang('Send Email')</button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('admin.subscriber.index') }}" class="btn btn-sm btn--primary">
        <i class="fa-solid fa-arrow-left"></i> @lang('Back')
    </a>
@endpush

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
