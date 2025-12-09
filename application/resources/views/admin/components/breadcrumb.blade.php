<div class="row gy-3 align-items-center justify-content-between">
    <div class="col-xl-6 ">
        <div class="content--wrap">
            <h6 class="page-title parent">@lang('Dashboard')</h6>
            @if(!Route::is('admin.dashboard')) <i class="fa-solid fa-chevron-right"></i> <h6 class="page-title">{{ __($pageTitle) }}</h6> @endif
        </div>
    </div>

    <div class="col-xl-6  text-sm-end right-part">
        @stack('breadcrumb-plugins')
    </div>
</div>
