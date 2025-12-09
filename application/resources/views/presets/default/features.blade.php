@extends('Template::layouts.frontend')
@section('content')

<!--==========================  Key Feature Section Start  ==========================-->
<section class="key__feature my-120">
    <div class="container">
        <div class="row gy-5 justify-content-center">
            @forelse($features as $item)
            <div class="col-md-4 col-sm-6">
                <div class="key__feature__single">
                    <img src="{{ getImage(getFilePath('feature') . '/'. $item->data_values->image) }}" alt="@lang('Image')">
                    <h4>{{ __($item->data_values->title) }}</h4>
                    <p>{{ __($item->data_values->description) }}</p>
                </div>
            </div>
            @empty

            @endforelse

        </div>

        @if($features->hasPages())
        <div class="row pt-3 gy-3 justify-content-center align-item-center">
            <div class="col-lg-12">
                {{ $features->links() }}
            </div>
        </div>
        @endif
    </div>
</section>
<!--==========================  Key Feature Section End  ==========================-->

    @if($sections->secs != null)
        @foreach(json_decode($sections->secs) as $sec)
            @includeIf('Template::sections.'.$sec)
        @endforeach
    @endif

@endsection
