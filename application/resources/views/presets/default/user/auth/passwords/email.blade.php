@extends('Template::layouts.auth')
@section('content')
@includeIf('Template::components.auth_content')
<div class="auth__main">
    <div class="auth__wrapper">
        <div class="auth__title">
            <h3>{{ __($pageTitle) }}</h3>
            <p>@lang('To recover your account please provide your email or username to find your account.')</p>
        </div>
        <form method="POST" action="{{ route('user.password.email') }}">
            @csrf
            <div class="auth__form">
                <div class="row g-4">
                    <div class="col-lg-12">
                        <div class="auth__form__single">
                            <label class="form-label">@lang('Username or Email')</label>
                            <input type="text" class="form-control" name="value" value="{{ old('value') }}" required autofocus="off">
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="auth__form__single">
                            <button type="submit" class="btn btn--base w-100">@lang('Save')</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
