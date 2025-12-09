@extends('admin.layouts.app')
@section('panel')
    <div class="row gy-4">
        <div class="col-xxl-6">
            <div class="card br--solid radius--base p-16">
                <h5 class="card-title mb-4 border-bottom pb-3">@lang('Profile Information')</h5>
                <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-5">
                            <x-image-uploader name="image" :imagePath="getImage(getFilePath('adminProfile').'/'. $admin->image,getFileSize('adminProfile'))" :size="getFileSize('adminProfile')" :isImage="true" class="w-100" id="uploadLogo3" :required="false" />
                        </div>


                        <div class="col-md-7">
                            <div class="form-group ">
                                <label>@lang('Name')</label>
                                <input class="form-control" type="text" name="name" value="{{ $admin->name }}"
                                    required>
                            </div>
                            <div class="form-group">
                                <label>@lang('Email')</label>
                                <input class="form-control" type="email" name="email" value="{{ $admin->email }}"
                                    required>
                            </div>
                            <div class="text-end">
                                <button type="submit" class="btn btn--primary">@lang('Save Changes')</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

        </div>
        <div class="col-xxl-6">
            <div class="card br--solid radius--base p-16">
                <h5 class="card-title mb-4 border-bottom pb-3">@lang('Change Password')</h5>
                <form action="{{ route('admin.password.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label>@lang('Password')</label>
                        <input class="form-control" type="password" name="old_password" required>
                    </div>
                    <div class="form-group">
                        <label>@lang('New Password')</label>
                        <input class="form-control" type="password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label>@lang('Confirm Password')</label>
                        <input class="form-control" type="password" name="password_confirmation" required>
                    </div>
                    <button type="submit" class="btn btn--primary float-end">@lang('Change Password')</button>
                </form>
            </div>
        </div>
    </div>
@endsection

