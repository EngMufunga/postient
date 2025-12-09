@extends('admin.layouts.app')

@section('panel')

    <div class="row gy-4">

        <div class="col-md-12 mb-30">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <form action="{{ route('admin.staff.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <x-image-uploader name="image" :imagePath="getImage(getFilePath('adminProfile').'/',getFileSize('adminProfile'))" :size="getFileSize('adminProfile')" :isImage="true" class="w-100" id="uploadLogo3" :required="false" />
                            </div>

                            <div class="col-md-8">
                                <div class="bg--white br--solid radius--base p-16">

                                    <div class="form-group ">
                                        <label>@lang('Name')</label>
                                        <input class="form-control" type="text" name="name" required>
                                    </div>

                                    <div class="form-group">
                                        <label>@lang('Role')</label>
                                        <select class="form-control form-select" name="role_id" required>
                                            @foreach($roles as $role)
                                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>@lang('Email')</label>
                                        <input class="form-control" type="email" name="email" required>
                                    </div>

                                    <div class="form-group">
                                        <label>@lang('Username')</label>
                                        <input class="form-control" type="text" name="username" required>
                                    </div>

                                    <div class="form-group">
                                        <label>@lang('Password')</label>
                                        <input class="form-control" type="text" name="password" required>
                                    </div>

                                    <div class="text-end">
                                        <button type="submit" class="btn btn--primary">@lang('Save Changes')</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection





