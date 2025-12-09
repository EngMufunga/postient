@extends('admin.layouts.app')
@section('panel')
    <div class="row gy-4 justify-content-start mb-none-30">
        <div class="col-xxl-3 col-xl-3 col-lg-12">
            @include('admin.components.navigate_sidebar')
        </div>

        <div class="col-xxl-9 col-xl-9 col-lg-12 mb-30">
            <div class="row gy-4">
                <div class="col-xxl-12 col-xl-12">
                    <div class="card bg--white br--solid radius--base p-16">
                        <h5 class="mb-3">@lang('Manage Templates')</h5>

                        <div class="row gy-4">
                            @foreach($templates as $temp)
                            <div class="col-xl-4 col-md-6">
                                <div class="card">
                                    <div class="card-header bg--warning d-flex justify-content-between flex-wrap">
                                        <h4 class="card-title text-white"> {{ __(keyToTitle($temp['name'])) }} </h4>
                                        @if($general->active_template == $temp['name'])
                                        <button type="submit" name="name" value="{{$temp['name']}}" class="btn btn--primary">
                                            @lang('SELECTED')
                                        </button>
                                        @else
                                        <form action="" method="post">
                                            @csrf
                                            <button type="submit" name="name" value="{{$temp['name']}}" class="btn btn--primary w-100">
                                                @lang('SELECT')
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                    <div class="card-body p-0">
                                        <img src="{{$temp['image']}}" alt="@lang('Template')" class="w-100 mb-3">
                                    </div>
                                </div>
                            </div>
                            @endforeach

                            @if($extra_templates)
                                @foreach($extra_templates as $temp)
                                <div class="col-lg-3">
                                    <div class="card">
                                        <div class="card-header bg--warning">
                                            <h4 class="card-title text-white"> {{ __(keyToTitle($temp['name'])) }} </h4>
                                        </div>
                                        <div class="card-body">
                                            <img src="{{$temp['image']}}" alt="@lang('Template')" class="w-100">
                                        </div>
                                        <div class="card-footer text-end">
                                            <a href="{{$temp['url']}}" target="_blank" class="btn btn--primary">@lang('Get This')</a>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            @endif
                        </div>


                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection



