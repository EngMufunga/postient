@extends('Template::layouts.frontend')
@section('content')
    <section class="policy__area pt-120">
        <img class="policy__sp" src="{{ getImage($activeTemplateTrue . 'images/sparkle-outline.svg') }}" alt="@lang('Image')">
        <img class="policy__sp sp-2" src="{{ getImage($activeTemplateTrue . 'images/quote.svg') }}" alt="@lang('Image')">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="policy__content wyg">
                        @php echo $maintenance->data_values->description @endphp
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
