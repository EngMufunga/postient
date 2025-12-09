@extends('Template::layouts.auth')
@section('content')
@includeIf('Template::components.auth_content')
<div class="auth__main">
    <div class="auth__wrapper">
        <div class="d-flex justify-content-center">
            <div class="verification-code-wrapper border-0">
                <div class="verification-area border-0">
                    <h5 class="pb-3 text-center border-0">@lang('2FA Verification')</h5>
                    <form action="{{route('user.go2fa.verify')}}" method="POST" class="submit-form">
                        @csrf

                        @include('Template::components.verification_code')

                        <div class="form-group">
                            <button type="submit" class="btn btn--base w-100">@lang('Submit')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection


