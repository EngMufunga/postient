@extends('Template::layouts.frontend')
@section('content')

    <!--==========================  Blog Details Section Start  ==========================-->
    <section class="blog__details my-120">
        <div class="container">
            <div class="row">
                <div class="col-xl-9 col-lg-8">
                    <div class="blog__details__content">
                        <img src="{{ getImage(getFilePath('blog') .'/' . $blog->data_values->blog_image, getFileSize('blog')) }}" alt="@lang('Image')">
                        <div class="blog__date">
                            <span class="badge badge--pink">{{ showDateTime($blog->created_at, 'd M, Y') }}</span>
                        </div>
                        <h1>{{ __($blog->data_values->title) }}</h1>


                        @php
                            $description = $blog->data_values->description;
                            $paragraphs = preg_split('/(<\/p>)/i', $description, -1, PREG_SPLIT_DELIM_CAPTURE);
                            $output = '';
                            $inserted = false;

                            foreach ($paragraphs as $i => $paragraph) {
                                $output .= $paragraph;

                                if (!$inserted && strip_tags(trim($paragraph)) !== '') {
                                    $output .= '<div class="blog__quote bg--img" data-background-image="' . getImage($activeTemplateTrue . 'images/dots.svg') . '">';
                                    $output .= '<p>' . e(__($blog->data_values->blockquote)) . '</p>';
                                    $output .= '<span><i class="fa-solid fa-quote-right"></i></span>';
                                    $output .= '</div>';
                                    $inserted = true;
                                }
                            }
                        @endphp
						<div class="wyg">
							@php echo $output @endphp
						</div>
                    </div>
                    <div class="blog__share">
                        <h6><i class="fa-solid fa-share-nodes"></i> @lang('Share This post')</h6>
                        <ul class="social__icon">
                            <li><a href="https://www.facebook.com/share.php?u={{ Request::url() }}&title={{ slug($blog->data_values->title) }}" target="_blank"><i class="fa-brands fa-facebook-f"></i></a></li>
                            <li><a href="https://twitter.com/intent/tweet?status={{ slug($blog->data_values->title) }}+{{ Request::url() }}" target="_blank"><i class="fa-brands fa-x-twitter"></i></a></li>
                            <li><a href="https://www.linkedin.com/shareArticle?mini=true&url={{ Request::url() }}&title={{ slug($blog->data_values->title) }}&source=propertee" target="_blank"><i class="fa-brands fa-linkedin-in"></i></a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-4">
                    <div class="blog__sidebar">
                    <form id="blogSearchForm" action="javascript:void(0);">
                        <div class="search__box position-relative">
                            <input type="text" id="searchInput" class="form-control" placeholder="Search blog...">
                            <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>

                            <!-- Search results will appear here -->
                            <div id="searchResults"
                                class="recent__blog__wrap mt-1 p-2 bg-white border rounded shadow-sm position-absolute w-100"
                                style="max-height: 350px; overflow-y: auto; display: none; z-index: 999;">
                            </div>
                        </div>
                    </form>


                        <div class="recent__blog mt-40">
                            <div class="recent__blog__title">
                                <h5>@lang('Recent Blog Post')</h5>
                            </div>
                            <div class="recent__blog__wrap">
                                @forelse($recentBlogs as $item)
                                <div class="recent__blog__single">
                                    <a href="{{ route('blog.details', ['slug' => slug($item->data_values->title), 'id' => $item->id])}}"><img src="{{ getImage(getFilePath('blog') . '/thumb_' . $item->data_values->blog_image) }}" alt="@lang('Blog Thumb')"></a>
                                    <div>
                                        <h6><a href="{{ route('blog.details', ['slug' => slug($item->data_values->title), 'id' => $item->id])}}">{{ __($item->data_values->title) }}</a></h6>
                                        <p><i class="fa-regular fa-calendar"></i> {{ showDateTime($item->created_at, 'd M, Y') }}</p>
                                    </div>
                                </div>
                                @empty

                                @endforelse

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--==========================  Blog Details Section End  ==========================-->



@endsection


@push('script')
    <script>
        $(document).ready(function() {
            'use strict';
            $('#searchInput').on('keyup', function() {
                let query = $(this).val().trim();
                if (query.length < 2) {
                    $('#searchResults').hide().empty();
                    return;
                }

                $.ajax({
                    url: "{{ route('blog.search') }}", // route we'll define below
                    type: "GET",
                    data: { q: query },
                    success: function(response) {
                        let results = response.data;
                        let html = '';

                        if (results.length > 0) {
                            results.forEach(function(blog) {
                                html += `
                                    <div class="recent__blog__single d-flex mb-2">
                                        <a href="${blog.url}">
                                            <img src="${blog.image}" alt="Blog Thumb" style="width:70px; height:70px; object-fit:cover; border-radius:5px;">
                                        </a>
                                        <div class="ms-2">
                                            <h6><a href="${blog.url}">${blog.title}</a></h6>
                                            <p><i class="fa-regular fa-calendar"></i> ${blog.date}</p>
                                        </div>
                                    </div>
                                `;
                            });
                        } else {
                            html = `<p class="text-muted text-center py-2">No results found</p>`;
                        }

                        $('#searchResults').html(html).show();
                    }
                });
            });

            // Hide results when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('#blogSearchForm').length) {
                    $('#searchResults').hide();
                }
            });
        });
    </script>
@endpush


