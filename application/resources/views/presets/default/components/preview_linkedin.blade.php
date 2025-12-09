<div class="post-info">
    <div class="post-info__top">
        <div class="post-info__thumb">
            <img class="profile" src="{{ getImage(getFilePath('userProfile'), getFileSize('userProfile')) }}" alt="@lang('Image')">
        </div>
        <div class="post-info__content">
            <h6 class="post-info__name user_name"></h6>
            <span class="post-info__time"> @lang('Just Now') </span>
        </div>
    </div>
    <div class="post__preview__main">
        <p class="post-info__desc content-preview"></p>
        <span class="text--base hashtag-preview"></span>
        <div class="post__preview__file post_media_wrapper d-none">

        </div>
    </div>
    <div class="extra_elements">
        <div class="reaction-btn-wrapper">
            <span class="reaction-btn">
                <i class="fa-regular fa-thumbs-up"></i>
                @lang('Like')
            </span>
            <span class="reaction-btn">
                <i class="fa-regular fa-comment"></i>
                @lang('Comment')
            </span>
            <span class="reaction-btn">
                <i class="fa-regular fa-bookmark"></i>
                @lang('Save')
            </span>
            <span class="reaction-btn">
                <i class="fa-regular fa-share-from-square"></i>
                @lang('Share')
            </span>
        </div>
    </div>
</div>

<div class="post-restriction-wrapper mt-3">
    <h5 class="text--danger">@lang('Post Restriction')</h5>
    <div class="post-sidebar">
        <p>@lang('Linkedin you can post only 1 video or can be 9 images. Combining both is not allowed. So your video will be shown embedded as a linked')</p>
    </div>
</div>
