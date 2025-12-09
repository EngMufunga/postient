@props([
    'link'       => null,
    'title'      => null,
    'value'      => null,
    'icon'       => '',
    'bg'         => 'primary',
    'outline'    => false,
    'heading'    => null,
    'subheading' => null,
    'viewMoreIcon'   => true
])

<a href="{{ $link }}">
    <div class="widget-seven common__bg @if($outline) outline @endif">
        <div class="widget-seven__content">
            <span class="widget-seven__content-icon">
                <span class="icon">
                    <i class="{{ $icon }}"></i>
                </span>
            </span>
            <div class="widget-seven__description">
                <h3 class="widget-seven__content-amount">{{ $value || $value === "0" || $value === 0 ? $value : __($heading) }}</h3>
                @if($title)
                <p class="widget-seven__content-title">{{ __($title) }}</p>
                @endif
                @if($subheading)
                <p class="widget-seven__content-subheading">{{ __($subheading) }}</p>
                @endif
            </div>
        </div>
    </div>
</a>
