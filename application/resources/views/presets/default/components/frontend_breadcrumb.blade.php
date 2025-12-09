<!-- ==================== Breadcrumb Start Here ==================== -->
<section class="breadcrumb">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb__wrapper bg--img" data-background-image="{{ getImage($activeTemplateTrue . 'images/dots.svg') }}">
                    <h2 class="breadcrumb__title">{{ __($pageTitle) }}</h2>
                    <ul class="breadcrumb__list">
                        <li class="breadcrumb__item"><a href="{{ route('home') }}" class="breadcrumb__link">@lang('Home')</a></li>
                        <li class="breadcrumb__item">//</li>
                        <li class="breadcrumb__item">{{ __($pageTitle) }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- ==================== Breadcrumb End Here ==================== -->
