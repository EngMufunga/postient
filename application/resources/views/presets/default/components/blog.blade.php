@forelse($blogs as $index =>$item)
<div class="col-lg-4 col-sm-6">
    <div class="blog__card">
        <div class="blog__img">
            <a href="{{ route('blog.details', ['slug' => slug($item->data_values->title), 'id' => $item->id])}}">
                <img src="{{ getImage(getFilePath('blog') . '/thumb_' . $item->data_values->blog_image, getFileSize('blog')) }}" alt="@lang('Image')">
            </a>
        </div>
        <div class="blog__content">

            @php
                $classes = ['warning', 'success', 'danger', 'info', 'primary'];
            @endphp

            <span class="badge badge--{{ $classes[$index % count($classes)] }}">{{ showDateTime($item->created_at, 'd M, Y') }}</span>
            <h5><a href="{{ route('blog.details', ['slug' => slug($item->data_values->title), 'id' => $item->id])}}">{{ __($item->data_values->title) }}</a>
            </h5>
            <a href="{{ route('blog.details', ['slug' => slug($item->data_values->title), 'id' => $item->id])}}">@lang('Continue Reading') <i class="fa-solid fa-angle-right"></i></a>
        </div>
    </div>
</div>
@empty

@endforelse
