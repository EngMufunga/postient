@extends('Template::layouts.frontend')
@section('content')

    <!--==========================  Blog Section Start  ==========================-->
    <section class="blog__area my-120">
        <div class="container">
            <div class="row gy-5 justify-content-center">
                @includeIf('Template::components.blog', ['blogs' => $blogs])
            </div>

            @if($blogs->hasPages())
            <div class="row pt-3 gy-3 justify-content-center align-item-center">
                <div class="col-lg-12">
                    {{ $blogs->links() }}
                </div>
            </div>
            @endif
        </div>
    </section>
    <!--==========================  Blog Section End  ==========================-->

@if($sections->secs != null)
    @foreach(json_decode($sections->secs) as $sec)
        @includeIf('Template::sections.'.$sec)
    @endforeach
@endif
@endsection


