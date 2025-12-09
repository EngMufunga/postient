<div class="post-info">
    <div class="post-info__top">
        <div class="post-info__thumb">
            <img class="profile" src="{{ getImage('assets/images/avatar.png') }}" alt="@lang('Image')">
        </div>
        <div class="post-info__content">
            <h6 class="post-info__name">@lang('User Name')</h6>
            <span class="post-info__time"> @lang('Just Now') </span>
        </div>
    </div>

    <p>@lang("Your post will be displayed here")</p><br>
    <span class="text--base">@lang('#SampleTag #TestTag')</span>

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
