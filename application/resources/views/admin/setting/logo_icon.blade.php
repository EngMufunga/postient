@extends('admin.layouts.app')
@section('panel')
    <div class="row gy-4 justify-content-start mb-none-30">
        <a id="refresh"></a>
        <div class="col-xxl-3 col-xl-3 col-lg-12">
            @include('admin.components.navigate_sidebar')
        </div>

        <div class="col-xxl-9 col-xl-9 col-lg-12">
            <form action="{{ route('admin.setting.logo.icon') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="card p-16 br--solid radius--base bg--white">
                    <div class="row gy-4 mb-4">
                        <div class="col-xxl-4 col-xl-6 col-md-6">
                            <h6 class="mb-3">@lang('White Logo')</h6>
                            <div class="logo-upload--box">
                                <x-image-uploader name="logo_white" :imagePath="siteLogo('white') . '?' . time()" :size="false" :whiteLogoClass="true" class="w-100" id="uploadLogo2" :required="false"/>
                            </div>
                        </div>


                        <div class="col-xxl-4 col-xl-6 col-md-6">
                            <h6 class="mb-3">@lang('Logo')</h6>
                            <div class="logo-upload--box">
                                <x-image-uploader name="logo" :imagePath="siteLogo() . '?' . time()" :size="false" class="w-100" id="uploadLogo1" :required="false"/>
                            </div>
                        </div>

                        <div class="col-xxl-4 col-xl-6 col-md-6">
                            <h6 class="mb-3">@lang('Favicon')</h6>
                            <div class="logo-upload--box">
                                <x-image-uploader name="favicon" :imagePath="siteFavicon() . '?' . time()" :size="false" class="w-100" id="uploadLogo3" accept=".png" :required="false" />
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-0 text-end">
                        <button type="submit" class="btn btn--primary">@lang('Save Changes')</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection


