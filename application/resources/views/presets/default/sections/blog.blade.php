@php
    $blogContent = getContent('blog.content', true);
    $blogs = getContent('blog.element')->take(3);
@endphp

    <!--==========================  Blog Section Start  ==========================-->
<section class="blog__area my-120">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="section__heading mb-60 text-center">
                    <span class="text--base">{{ __($blogContent->data_values->title) }}</span>
                    <h2>{{ __($blogContent->data_values->heading) }}</h2>
                    <p>{{ __($blogContent->data_values->subheading) }}</p>
                </div>
            </div>
        </div>
        <div class="row gy-5 justify-content-center">
            @includeIf('Template::components.blog', ['blogs' => $blogs])
        </div>
    </div>
</section>
<!--==========================  Blog Section End  ==========================-->
