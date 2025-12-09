@extends('admin.layouts.app')

@section('panel')
<div class="row mb-none-30">
    <div class="col-xl-12">
        <div class="card bg--white br--solid radius--base p-16">
            <form action="{{ route('admin.users.notification.single', $user->id) }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label class="fw--500">@lang('Subject') </label>
                            <input type="text" class="form-control" placeholder="@lang('Email subject')" name="subject"
                                required />
                        </div>
                        <div class="form-group col-md-12">
                            <label class="fw--500">@lang('Message') </label>
                            <textarea name="message" rows="10" class="form-control trumEdit"></textarea>
                        </div>
                        <div class="col-lg-12 text-end">
                            <button type="submit" class="btn w-100 btn--primary">@lang('Send')</button>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>

@endsection
@push('breadcrumb-plugins')
<span class="text--primary">@lang('Notification will send via ') @if($general->en) <span
        class="badge badge--warning">@lang('Email')</span> @endif @if($general->sn) <span
        class="badge badge--warning">@lang('SMS')</span> @endif</span>
@endpush
