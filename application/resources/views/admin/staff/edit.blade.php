@extends('admin.layouts.app')
@section('panel')
    <div class="row gy-4">
        <div class="col-md-12 mb-30">
            <div class="card">
                <div class="card-body p-0">
                    <form action="{{ route('admin.staff.update', $staff->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <x-image-uploader name="image" :imagePath="getImage(getFilePath('adminProfile').'/'.$staff->image,getFileSize('adminProfile'))" :size="getFileSize('adminProfile')" :isImage="true" class="w-100" id="uploadLogo3" :required="false" />
                            </div>

                            <div class="col-md-8">
                                <div class="card bg--white radius--base p-16">
                                    <div class="form-group">
                                        <label>@lang('Name')</label>
                                        <input class="form-control" type="text" name="name" value="{{ $staff->name }}" required>
                                    </div>

                                    <div class="form-group">
                                        <label>@lang('Role')</label>
                                        <select class="form-control form-select" name="role_id" required>
                                            @foreach($roles as $role)
                                            <option value="{{ $role->id }}" @selected($role->id == $staff->role_id)>{{ $role->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>@lang('Email')</label>
                                        <input class="form-control" type="email" name="email" value="{{ $staff->email }}" required>
                                    </div>

                                    <div class="form-group">
                                        <label>@lang('Username')</label>
                                        <input class="form-control" type="text" name="username" value="{{ $staff->username }}" required>
                                    </div>

                                    <div class="form-group">
                                        <label>@lang('Password')(<span class="text--danger ms-2">@lang('Leave empty if you don\'t want to change')</span>)</label>
                                        <input class="form-control" type="text" name="password">
                                    </div>

                                    <div class="text-end">
                                        <button type="submit" class="btn btn--primary">@lang('Update')</button>
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
