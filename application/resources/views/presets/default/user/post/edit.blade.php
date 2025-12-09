@extends('Template::layouts.master')
@section('content')

<div class="row g-4">
    <div class="col-xl-8">
        <div class="card">
            <div class="create__post">
                @if (auth()->user()->connected_profile < auth()->user()->plan?->connected_profile)
                    <div class="connect--more mb-2">
                        <p>
                            @lang('You currently have')
                            <strong>{{ auth()->user()->connected_profile }}</strong>
                            @lang('connected profile(s). Your current subscription plan allows up to')
                            <strong>{{ auth()->user()->plan?->connected_profile }}</strong>
                            @lang('connected profile(s).')
                        </p>
                        <a class="btn btn--md btn--base" href="{{ route('user.social.account.index') }}">
                            <i class="fa-solid fa-plus"></i>
                            @lang('Connect')
                        </a>
                    </div>
                @endif

                <form id="postForm" action="{{ route('user.posts.update', $post->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-4">
                        <div class="col-lg-12">
                            <div class="template__radio">
                                <input type="hidden" name="platform_id" value="{{ platformName($platform) }}">
                                @foreach ($accounts as $key => $account)
                                    <div class="form-radio">

                                        <input class="form-check-input account-switch d-none" type="radio" name="account_id" value="{{ $account->id }}" id="account-{{ $account->name . '-' . $key }}" data-platform-id = "{{ $account->platform?->id ?? '' }}" data-platform-name="{{ $account->platform?->name }}" data-account="{{ $account }}" {{ in_array($post->social_account_id, $accounts->pluck('id')->toArray()) ? 'checked' : '' }}>

                                        <input class="form-check-input d-none platform-type" type="radio" name="radioDefault"
                                            id="account--{{ $account->name . '-' . $key }}" data-platform-id="{{ $account->platform?->id ?? '' }}" data-platform-name="{{ $account->platform?->name ?? '' }}" value="{{ $account->platform->id }}">


                                        <label class="form-check-label" for="account-{{ $account->name . '-' . $key }}">

                                            <img src="{{ getImage( getFilePath('platform') .'/'. $account->platform?->image ?? '') }}" width="30" height="30" alt="@lang('Image')">
                                            <span class="single-post__icon {{ strtolower($account->platform?->name ?? '') }}">
                                                <img src="{{ $account->profile_image != null ? $account->profile_image : getImage('assets/images/general/avatar.png') }}" alt="@lang('Image')">
                                            </span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="template__form">
                                <label for="post_title" class="form-label d-flex align-items-center justify-content-between">
                                    <span>
                                        @lang('Title')
                                        <small class="text-muted">(@lang('Optional'))</small>
                                    </span>
                                    <small class="text-muted">
                                        @lang('Max') 100 @lang('characters') — @lang('Not shown in post content')
                                    </small>
                                </label>

                                <input type="text" id="post_title" name="title" class="form-control post-title" placeholder="@lang('Enter a concise title')..." max="100" aria-describedby="titleHelpBlock" value="{{ old('title', $post->title) }}" required>

                                <div id="titleHelpBlock" class="form-text text--info">
                                    @lang('This title is for your reference only and won’t appear in the published post.')
                                </div>
                            </div>
                        </div>



                        <div class="col-lg-12">
                            <div class="template__form">
                                <label class="form-label">@lang('Post Content')</label>
                                <textarea class="form-control post-content emoji__area" name="post_content" placeholder="@lang('Write something here')..." required>{{ old('post_content', $post->post_content) }}</textarea>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="template__form">
                                <label class="form-label">@lang('Tags')</label>
                                <input type="text" name="tags" class="form-control" value="{{ old('tags', $post->tags) }}" hidden>
                                <span class="hashtag-preview text--base {{ $post->tags ? '' : 'd-none' }}">{{ $post->tags }}</span>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="create__post__widgets">
                                <div class="create__pro">
                                    <div class="create__file">
                                        <label for="post-file"><i class="fa-solid fa-image"></i>@lang('Image/Video')</label>
                                        <input type="file" class="d-none" id="post-file" name="media[]" multiple accept="image/*,video/*">
                                    </div>
                                    <div class="post__ai">
                                        <span class="openTagModal">
                                            <i class="fa-solid fa-hashtag"></i> @lang('Tags')
                                        </span>
                                    </div>
                                </div>


                                @if(auth()->user()->ai_assistant_status && auth()->user()->generated_content_count < auth()->user()->plan?->generated_content_count)
                                    <div class="create__pro">
                                        <div class="post__ai create__file">
                                            <span class="openAiTagModal">
                                                <i class="fa-solid fa-wand-magic-sparkles"></i> @lang('AI Tag')
                                            </span>
                                        </div>
                                        <div class="post__ai">
                                            <span data-bs-toggle="modal" data-bs-target="#exampleModal">
                                                <i class="fa-solid fa-wand-magic-sparkles"></i> @lang('AI Content')
                                            </span>
                                        </div>
                                    </div>
                                @endif


                                @if(gs('image_generate_status') && auth()->user()->image_credit >= gs('per_image_credit'))
                                <div class="create_pro create__date">
                                    <div class="post__ai">
                                        <span id="generate-ai-image" style="cursor:pointer;"
                                            data-bs-toggle="modal" data-bs-target="#aiImageModal">
                                            <i class="fa-solid fa-wand-magic-sparkles"></i> @lang('Generate AI Image')
                                        </span>
                                    </div>
                                </div>
                                @endif


                                @if(auth()->user()->schedule_status && auth()->user()->plan?->schedule_status)
                                    <div class="create__date">
                                        <span id="selected_datetime" class="text-muted d-block" style="cursor:pointer;">
                                            <i class="fa-regular fa-clock"></i> {{ $post->is_schedule ? showDateTime($post->schedule_time, 'd M, Y - h:m A') : '-' }}
                                        </span>
                                        <input type="hidden" id="schedule_datetime" name="schedule_datetime" value="{{ $post->is_schedule ? showDateTime($post->schedule_time, 'd M, Y - h:m A') : '' }}">
                                    </div>
                                @endif
                            </div>



                            <div class="create_post_widget_wrapper post__preview__file mt-3">
                                @foreach ($post->mediaAssets as $media)
                                    <div class="media-item position-relative existing-media confirmationBtn" data-action="{{ route('user.posts.image.delete', $media->id) }}" data-question="@lang('Are you sure to delete this post image?')">
                                        @if (str_contains($media->type, 1))
                                            <img src="{{ getImage(getFilePath('postMedia') . '/' . $media->filename) }}" class="preview-thumb" style="object-fit: cover;">
                                        @elseif (str_contains($media->type, 2))
                                            <video src="{{ asset(getFilePath('postMedia') . '/' . $media->filename) }}" class="preview-thumb" controls></video>
                                        @endif
                                        <span class="remove-media position-absolute top-0 end-0 bg-danger text-white px-2 rounded-circle" style="cursor:pointer;">×</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>


                        <div class="col-lg-12 text-end">
                            <div class="template__form">
                                <button type="submit" class="btn btn--base me-2 submit-btn" data-type="post_now">@lang('Post Now') <i class="fa-solid fa-paper-plane"></i></button>

                                @if(auth()->user()->schedule_status && auth()->user()->plan?->schedule_status)
                                    <button type="submit" class="btn btn--base me-2 submit-btn" data-type="schedule_post">@lang('Schedule Post') <i class="fa-solid fa-calendar-days"></i></button>
                                @endif

                                <button type="submit" class="btn btn--base submit-btn" data-type="draft">@lang('Save as Draft') <i class="fa-solid fa-paper-plane"></i></button>
                            </div>
                        </div>
                    </div>
                </form>


            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card default">
            <h5>@lang('Post Preview')</h5>
            <div class="post-sidebar platform-preview {{ $platform == 'facebook' ? '' : 'd-none' }}" data-preview="facebook">
                @includeIf($activeTemplate . 'components.preview_facebook')
            </div>

            <div class="post-sidebar platform-preview {{ $platform == 'instagram' ? '' : 'd-none' }}" data-preview="instagram">
                @includeIf($activeTemplate . 'components.preview_instagram')
            </div>

            <div class="post-sidebar platform-preview {{ $platform == 'youtube' ? '' : 'd-none' }}" data-preview="youtube">
                @includeIf($activeTemplate . 'components.preview_youtube')
            </div>

            <div class="post-sidebar platform-preview {{ $platform == 'linkedin' ? '' : 'd-none' }}" data-preview="linkedin">
                @includeIf($activeTemplate . 'components.preview_linkedin')
            </div>

            <div class="post-sidebar platform-preview {{ $platform == 'tiktok' ? '' : 'd-none' }}" data-preview="tiktok">
                @includeIf($activeTemplate . 'components.preview_tiktok')
            </div>

            <div class="post-sidebar platform-preview {{ $platform == 'twitter' ? '' : 'd-none' }}" data-preview="twitter">
                @includeIf($activeTemplate . 'components.preview_twitter')
            </div>

        </div>


    </div>
</div>



<!-- Tag / AI Hashtag Modal -->
<div class="modal fade" id="tagModal" tabindex="-1" aria-labelledby="tagModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="tagModalLabel">@lang('Add or Generate Tags')</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body row">
        <div class="col-lg-12 mb-3 prompt_field d-none">
            <div class="auth__form__single">
                <div class="input-group">
                    <input type="text" class="form-control prompt" placeholder="@lang('Enter a prompt to generate hashtags')...">
                    <button type="button" class="btn btn--base generateHashtag input-group-text" data-action="{{ route('user.posts.hashtag.generate') }}">
                        <i class="fa-solid fa-wand-magic-sparkles"></i>
                        <div class="spinner-border spinner-border-sm text-light d-none loader2" role="status"></div>
                    </button>
                </div>
            </div>
        </div>


        <div class="col-lg-12 tag_input_warp">
          <input type="text" class="form-control hashtag_input" placeholder="#tag1 #tag2" value="{{ $post->tags }}">
        </div>

        <div class="col-lg-6 generated_tags_content d-none">
          <div class="generated_tags_wrapper"></div>
          <button type="button" class="btn btn--base add_hashtag d-none mt-2">@lang('Add Selected Tags')</button>
        </div>

      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn--base hashtagSubmit">@lang('Add Tags')</button>
      </div>
    </div>
  </div>
</div>




<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title" id="exampleModalLabel">@lang('Generate Post Content')</h1>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="modal-body">
                <div class="template__wrap">
                    <div class="row g-4">
                        <div class="col-lg-12">
                            <div class="template__form">
                                <textarea id="prompts" class="form-control" placeholder="@lang('Write a prompt to generate content')"></textarea>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="template__form">
                                <label class="form-label">@lang('Output Language')</label>
                                <select class="form-select" name="language" id="language" aria-label="@lang('Output Language')" required>
                                    <option value="">@lang('Select Language')</option>
                                    <option value="Afrikaans">@lang('Afrikaans')</option>
                                    <option value="Albanian">@lang('Albanian')</option>
                                    <option value="Amharic">@lang('Amharic')</option>
                                    <option value="Arabic">@lang('Arabic')</option>
                                    <option value="Armenian">@lang('Armenian')</option>
                                    <option value="Azerbaijani">@lang('Azerbaijani')</option>
                                    <option value="Basque">@lang('Basque')</option>
                                    <option value="Belarusian">@lang('Belarusian')</option>
                                    <option value="Bengali">@lang('Bengali')</option>
                                    <option value="Bosnian">@lang('Bosnian')</option>
                                    <option value="Bulgarian">@lang('Bulgarian')</option>
                                    <option value="Catalan">@lang('Catalan')</option>
                                    <option value="Cebuano">@lang('Cebuano')</option>
                                    <option value="Chichewa">@lang('Chichewa')</option>
                                    <option value="Chinese">@lang('Chinese')</option>
                                    <option value="Chinese (Simplified)">@lang('Chinese (Simplified)')</option>
                                    <option value="Chinese (Traditional)">@lang('Chinese (Traditional)')</option>
                                    <option value="Corsican">@lang('Corsican')</option>
                                    <option value="Croatian">@lang('Croatian')</option>
                                    <option value="Czech">@lang('Czech')</option>
                                    <option value="Danish">@lang('Danish')</option>
                                    <option value="Dutch">@lang('Dutch')</option>
                                    <option value="English">@lang('English')</option>
                                    <option value="Esperanto">@lang('Esperanto')</option>
                                    <option value="Estonian">@lang('Estonian')</option>
                                    <option value="Filipino">@lang('Filipino')</option>
                                    <option value="Finnish">@lang('Finnish')</option>
                                    <option value="French">@lang('French')</option>
                                    <option value="Frisian">@lang('Frisian')</option>
                                    <option value="Galician">@lang('Galician')</option>
                                    <option value="Georgian">@lang('Georgian')</option>
                                    <option value="German">@lang('German')</option>
                                    <option value="Greek">@lang('Greek')</option>
                                    <option value="Gujarati">@lang('Gujarati')</option>
                                    <option value="Haitian Creole">@lang('Haitian Creole')</option>
                                    <option value="Hausa">@lang('Hausa')</option>
                                    <option value="Hawaiian">@lang('Hawaiian')</option>
                                    <option value="Hebrew">@lang('Hebrew')</option>
                                    <option value="Hindi">@lang('Hindi')</option>
                                    <option value="Hmong">@lang('Hmong')</option>
                                    <option value="Hungarian">@lang('Hungarian')</option>
                                    <option value="Icelandic">@lang('Icelandic')</option>
                                    <option value="Igbo">@lang('Igbo')</option>
                                    <option value="Indonesian">@lang('Indonesian')</option>
                                    <option value="Irish">@lang('Irish')</option>
                                    <option value="Italian">@lang('Italian')</option>
                                    <option value="Japanese">@lang('Japanese')</option>
                                    <option value="Javanese">@lang('Javanese')</option>
                                    <option value="Kannada">@lang('Kannada')</option>
                                    <option value="Kazakh">@lang('Kazakh')</option>
                                    <option value="Khmer">@lang('Khmer')</option>
                                    <option value="Kinyarwanda">@lang('Kinyarwanda')</option>
                                    <option value="Korean">@lang('Korean')</option>
                                    <option value="Kurdish (Kurmanji)">@lang('Kurdish (Kurmanji)')</option>
                                    <option value="Kyrgyz">@lang('Kyrgyz')</option>
                                    <option value="Lao">@lang('Lao')</option>
                                    <option value="Latin">@lang('Latin')</option>
                                    <option value="Latvian">@lang('Latvian')</option>
                                    <option value="Lithuanian">@lang('Lithuanian')</option>
                                    <option value="Luxembourgish">@lang('Luxembourgish')</option>
                                    <option value="Macedonian">@lang('Macedonian')</option>
                                    <option value="Malagasy">@lang('Malagasy')</option>
                                    <option value="Malay">@lang('Malay')</option>
                                    <option value="Malayalam">@lang('Malayalam')</option>
                                    <option value="Maltese">@lang('Maltese')</option>
                                    <option value="Maori">@lang('Maori')</option>
                                    <option value="Marathi">@lang('Marathi')</option>
                                    <option value="Mongolian">@lang('Mongolian')</option>
                                    <option value="Myanmar (Burmese)">@lang('Myanmar (Burmese)')</option>
                                    <option value="Nepali">@lang('Nepali')</option>
                                    <option value="Norwegian">@lang('Norwegian')</option>
                                    <option value="Odia (Oriya)">@lang('Odia (Oriya)')</option>
                                    <option value="Pashto">@lang('Pashto')</option>
                                    <option value="Persian">@lang('Persian')</option>
                                    <option value="Polish">@lang('Polish')</option>
                                    <option value="Portuguese">@lang('Portuguese')</option>
                                    <option value="Punjabi">@lang('Punjabi')</option>
                                    <option value="Romanian">@lang('Romanian')</option>
                                    <option value="Russian">@lang('Russian')</option>
                                    <option value="Samoan">@lang('Samoan')</option>
                                    <option value="Scots Gaelic">@lang('Scots Gaelic')</option>
                                    <option value="Serbian">@lang('Serbian')</option>
                                    <option value="Sesotho">@lang('Sesotho')</option>
                                    <option value="Shona">@lang('Shona')</option>
                                    <option value="Sindhi">@lang('Sindhi')</option>
                                    <option value="Sinhala">@lang('Sinhala')</option>
                                    <option value="Slovak">@lang('Slovak')</option>
                                    <option value="Slovenian">@lang('Slovenian')</option>
                                    <option value="Somali">@lang('Somali')</option>
                                    <option value="Spanish">@lang('Spanish')</option>
                                    <option value="Sundanese">@lang('Sundanese')</option>
                                    <option value="Swahili">@lang('Swahili')</option>
                                    <option value="Swedish">@lang('Swedish')</option>
                                    <option value="Tajik">@lang('Tajik')</option>
                                    <option value="Tamil">@lang('Tamil')</option>
                                    <option value="Tatar">@lang('Tatar')</option>
                                    <option value="Telugu">@lang('Telugu')</option>
                                    <option value="Thai">@lang('Thai')</option>
                                    <option selected value="Turkish">@lang('Turkish')</option>
                                    <option value="Turkmen">@lang('Turkmen')</option>
                                    <option value="Ukrainian">@lang('Ukrainian')</option>
                                    <option value="Urdu">@lang('Urdu')</option>
                                    <option value="Uyghur">@lang('Uyghur')</option>
                                    <option value="Uzbek">@lang('Uzbek')</option>
                                    <option value="Vietnamese">@lang('Vietnamese')</option>
                                    <option value="Welsh">@lang('Welsh')</option>
                                    <option value="Xhosa">@lang('Xhosa')</option>
                                    <option value="Yiddish">@lang('Yiddish')</option>
                                    <option value="Yoruba">@lang('Yoruba')</option>
                                    <option value="Zulu">@lang('Zulu')</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="template__form">
                                <label class="form-label">@lang('Results Length')</label>
                                <input type="number" class="form-control" max="{{ gs('gpt_max_result_length') }}" min="10" step="1">
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="template__form">
                                <label class="form-label">@lang('Tone of Voice')</label>
                                <select class="form-select" name="tone" aria-label="@lang('Tone of Voice')" required>
                                    <option value="">@lang('Select Tone')</option>
                                    <option value="Friendly">@lang('Friendly')</option>
                                    <option value="Luxury">@lang('Luxury')</option>
                                    <option value="Relaxed">@lang('Relaxed')</option>
                                    <option value="Professional">@lang('Professional')</option>
                                    <option value="Casual">@lang('Casual')</option>
                                    <option value="Excited">@lang('Excited')</option>
                                    <option value="Bold">@lang('Bold')</option>
                                    <option value="Masculine">@lang('Masculine')</option>
                                    <option value="Dramatic">@lang('Dramatic')</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="template__form">
                                <label class="form-label">@lang('AI Creativity Level')</label>
                                <select class="form-select" name="ai_creativity" aria-label="@lang('AI Creativity Level')" required>
                                    <option selected>@lang('Select Creativity')</option>
                                    <option value="1">@lang('High')</option>
                                    <option value="0.5">@lang('Medium')</option>
                                    <option value="0">@lang('Low')</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn--base generate-content" data-action="{{ route('user.posts.content.generate') }}">
                    <i class="fa-solid fa-wand-magic-sparkles"></i> @lang('Generate')
                    <div class="spinner-border spinner-border-sm text-light d-none loader3" role="status"></div>
                </button>
            </div>

        </div>
    </div>
</div>


<div class="modal fade" id="aiImageModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">@lang('AI Generate Image')</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="input-group prompt_field">
            <input type="text" class="form-control prompt" placeholder="@lang('Enter a prompt to generate image')...">
            <button type="button" class="btn btn--base generateAiImage" data-action="{{ route('user.posts.generate.image') }}">
                <i class="fa-solid fa-wand-magic-sparkles"></i>
                <span class="spinner-border spinner-border-sm text-light d-none loader2" role="status"></span>
            </button>
        </div>
      </div>
    </div>
  </div>
</div>

    <x-confirmation-modal></x-confirmation-modal>
@endsection


@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/common/css/daterangepicker.css') }}">
@endpush

@push('script-lib')
    <script src="{{ asset($activeTemplateTrue . 'js/moment.min.js') }}"></script>
    <script src="{{ asset('assets/common/js/daterangepicker.min.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue . 'js/confirmDate.js') }}"></script>
@endpush

@push('style')
    <style>
        .post__preview__file.post_media_wrapper .remove-media {
            display: none !important;
        }

        .form-loader-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.85);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            border-radius: 10px;
        }

        .form-loader-overlay .spinner {
            border: 4px solid #ddd;
            border-top: 4px solid #4b7bec;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
@endpush

@push('script')

    <script>
        (function ($) {
            "use strict";
            // Generate AI image
            $(document).on('click', '.generateAiImage', function() {
                const $btn = $(this);
                const prompt = $btn.closest('.prompt_field').find('.prompt').val();
                const url = $btn.data('action');

                if (!prompt) {
                    notify('error', '{{ __("Please enter a prompt") }}');
                }

                $btn.find('.loader2').removeClass('d-none');

                $.ajax({
                    url: url,
                    method: 'GET',
                    data: { prompt: prompt },
                    success: function(response) {

                        console.log(response);
                        $btn.find('.loader2').addClass('d-none');

                        if (response.status === 'success') {
                            const previewElement = `
                                <div class="media-item position-relative" data-name="${response.filename}">
                                    <img src="${response.url}" class="preview-thumb rounded" width="120" height="120" style="object-fit: cover;">
                                    <span class="remove-media position-absolute top-0 end-0 bg-danger text-white px-2 rounded-circle" style="cursor:pointer;">×</span>
                                    <input type="hidden" name="ai_media[]" value="${response.filename}">
                                </div>
                            `;
                            $('.create_post_widget_wrapper').append(previewElement);
                            $('#aiImageModal').modal('hide');
                        } else {
                            // alert(response.message || 'Failed to generate image');
                            notify('error', response.message || '{{ __("Failed to generate image") }}');
                        }
                    },
                    error: function(err) {
                        let message = err.responseJSON && err.responseJSON.message ? err.responseJSON.message : '{{ __("Error generating image") }}';
                        notify('error', message);
                        $btn.find('.loader2').addClass('d-none');
                    }
                });
            });

            // Remove image
            $(document).on('click', '.remove-media', function() {
                const $media = $(this).closest('.media-item');
                const filename = $media.data('name');

                $media.remove(); // remove from UI

                // Optional: remove from server
                $.ajax({
                    url: "{{ route('user.posts.remove.ai.image') }}",
                    method: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        filename: filename
                    },
                    success: function(resp) {
                        console.log('Image removed', filename);
                        notify('success', resp.message);
                    },
                    error: function(err) { console.error('Failed to remove image', err); }
                });
            });
        }(jQuery));
    </script>


    <script>
        (function ($) {
            "use strict";


            function updatePreview(platformName, account) {
                // Hide all previews
                $(".platform-preview").addClass("d-none");

                // Find the matching preview section
                let $preview = $(`.platform-preview[data-preview="${platformName.toLowerCase()}"]`);

                // Update profile + username
                $preview.find(".profile").attr("src", account.profile_image || '/default-profile.png');
                $preview.find(".user_name").text(account.profile_name || 'Unknown User');

                // Update content and hashtags (if any)
                let postContent = $(".post-content").val();
                let hashtags = $(".hashtag-preview").text();

                if (postContent) {
                    $preview.find(".content-preview").removeClass("d-none").html(postContent);
                } else {
                    $preview.find(".content-preview").addClass("d-none");
                }

                // Update media if any
                let $mediaWrapper = $preview.find(".post_media_wrapper");
                let $uploadedMedia = $(".create_post_widget_wrapper").html();
                if ($uploadedMedia.trim()) {
                    $mediaWrapper.removeClass("d-none").html($uploadedMedia);
                } else {
                    $mediaWrapper.addClass("d-none").html("");
                }

                // Finally, show the updated preview
                $preview.removeClass("d-none");
            }


            $(document).on('click', '.account-switch, .platform-type', function() {
                let platformName = $(this).data('platform-name');
                let account = $(this).data('account');

                if (!platformName || !account) return;

                updatePreview(platformName, account);
            });


            $('.account-switch:checked').trigger('click');
            $('.platform-type:checked').trigger('click');

            let hashtags = $(".hashtag-preview").text().trim();

            if (hashtags) {
                $(".platform-preview:visible .text--base.hashtag-preview").text(hashtags);
            }


            function initializePreviewForEdit() {
                let $checked = $(".account-switch:checked").first();
                if ($checked.length > 0) {
                    let platformName = $checked.data("platform-name");
                    let account = $checked.data("account");
                    if (platformName && account) updatePreview(platformName, account);
                }
            }


            // Live content update
            $(document).on("input", ".post-content", function() {
                let platform = $(".account-switch:checked").data("platform-name");
                let account = $(".account-switch:checked").data("account");
                if (platform && account) updatePreview(platform, account);
            });

            // Live hashtag update
            $(document).on("input", ".hashtag_input", function() {
                let platform = $(".account-switch:checked").data("platform-name");
                let account = $(".account-switch:checked").data("account");
                if (platform && account) updatePreview(platform, account);
            });

            $(document).on('click', '.submit-btn', function() {
                const text = $(this).text().trim().toLowerCase();
                const $form = $(this).closest('form');
                if (text.includes('schedule')) {
                    $form.data('submit-type', 'schedule');
                } else if (text.includes('draft')) {
                    $form.data('submit-type', 'draft');
                } else {
                    $form.data('submit-type', 'post_now');
                }
            });

            let filesArray = window.filesArray || [];

            $('#post-file').on('change', function(e) {
                const files = Array.from(e.target.files);

                files.forEach(file => {
                    const fileType = file.type;
                    const isImage = fileType.startsWith('image/');
                    const isVideo = fileType.startsWith('video/');

                    if (isVideo && filesArray.some(f => f.type.startsWith('video/'))) {
                        alert("Only one video can be uploaded per post.");
                        return;
                    }

                    filesArray.push(file);

                    const reader = new FileReader();
                    reader.onload = function(event) {
                        let previewElement;

                        if (isImage) {
                            previewElement = `
                                <div class="media-item position-relative" data-name="${file.name}">
                                    <img src="${event.target.result}" class="preview-thumb" style="object-fit: cover;">
                                    <span class="remove-media position-absolute top-0 end-0 bg-danger text-white px-2 rounded-circle" style="cursor:pointer;">×</span>
                                </div>`;
                        } else if (isVideo) {
                            previewElement = `
                                <div class="media-item position-relative" data-name="${file.name}">
                                    <video src="${event.target.result}" class="preview-thumb" controls></video>
                                    <span class="remove-media position-absolute top-0 end-0 bg-danger text-white px-2 rounded-circle" style="cursor:pointer;">×</span>
                                </div>`;
                        }

                        // Show preview in both places
                        $('.create_post_widget_wrapper').append(previewElement);
                        $('.post_media_wrapper').append(previewElement);
                        $('.post_media_wrapper').removeClass('d-none');
                    };
                    reader.readAsDataURL(file);
                });

                // Reset input for future uploads
                $(this).val('');
            });

            // Remove media
            $(document).on('click', '.remove-media', function() {
                const item = $(this).closest('.media-item');
                const fileName = item.data('name');

                // Remove from array
                filesArray = filesArray.filter(file => file.name !== fileName);

                // Remove from both preview areas
                $(`.media-item[data-name="${fileName}"]`).remove();

                if (filesArray.length === 0) {
                    $('.post_media_wrapper').addClass('d-none');
                }
            });

            // Handle form submission


            // 📨 Handle form submission
            $('#postForm').on('submit', function(e) {
                e.preventDefault();

                const $form = $(this);
                const formData = new FormData(this);
                filesArray.forEach(file => formData.append('media[]', file));

                const submitType = $form.data('submit-type') || 'post_now';
                formData.append('submit_type', submitType);

                const schedule = $('#schedule_datetime').val();
                if (schedule) formData.append('schedule_datetime', schedule);

                const $buttons = $form.find('button[type=submit]');
                $buttons.prop('disabled', true).addClass('disabled');

                // 🌀 Add loader overlay inside form
                if ($form.find('.form-loader-overlay').length === 0) {
                    $form.css('position', 'relative').append(`
                        <div class="form-loader-overlay">
                            <div class="spinner"></div>
                        </div>
                    `);
                }

                $.ajax({
                    url: $form.attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                // Disable buttons to prevent double-submit
                $buttons.prop('disabled', true).addClass('disabled');

                // Add loader overlay if it doesn’t already exist
                if ($form.find('.form-loader-overlay').length === 0) {
                    $form.append(`
                        <div class="form-loader-overlay"
                            style="
                                position: absolute;
                                top: 0;
                                left: 0;
                                width: 100%;
                                height: 100%;
                                background: rgba(255, 255, 255, 0.85);
                                z-index: 9999;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                backdrop-filter: blur(3px);
                            ">
                            <div class="loader-spinner"
                                style="
                                    border: 4px solid #f3f3f3;
                                    border-top: 4px solid #3498db;
                                    border-radius: 50%;
                                    width: 50px;
                                    height: 50px;
                                    animation: spin 1s linear infinite;
                                ">
                            </div>
                        </div>

                        <style>
                            @keyframes spin {
                                0% { transform: rotate(0deg); }
                                100% { transform: rotate(360deg); }
                            }
                        </style>
                    `);
                }

                // Notify user
                notify('info', 'Uploading your post...');




                    },
                    success: function(response) {
                        notify('success', 'Post updated successfully!');

                        // $('.create_post_widget_wrapper').html('<img src="/assets/images/post-widgets.png" alt="">');
                        $('.post_media_wrapper').addClass('d-none').html('');
                        filesArray = [];

                        $form.trigger('reset');
                        $('.hashtag-preview').addClass('d-none').text('');
                        $('#selected_datetime').html('<i class="fa-regular fa-clock"></i> —');
                        $('#schedule_datetime').val('');
                    },
                    error: function(xhr) {

                        if (xhr.responseJSON) {
                            const res = xhr.responseJSON;
                            let messages = [];

                            if (Array.isArray(res.message)) {
                                messages = res.message;
                            } else if (typeof res.message === 'object') {
                                messages = Object.values(res.message).flat();
                            } else if (typeof res.message === 'string') {
                                messages = [res.message];
                            } else {
                                messages = ['Something went wrong.'];
                            }

                            // Show each message one by one
                            messages.forEach(msg => notify('error', msg));

                        } else {
                            notify('error', 'Something went wrong.');
                        }
                    },
                    complete: function() {
                        $form.find('.form-loader-overlay').fadeOut(300, function() {
                            $(this).remove();
                        });

                        $buttons.prop('disabled', false).removeClass('disabled');
                    }
                });
            });



          })(jQuery);
    </script>




    <script>
        (function ($) {
            "use strict";

            const $displaySpan = $('#selected_datetime');
            const $hiddenInput = $('#schedule_datetime');

            $displaySpan.daterangepicker({
                timePicker: true,
                timePicker24Hour: false,
                showDropdowns: true,
                drops: 'up',
                autoUpdateInput: false,
                singleDatePicker: true,
                    buttonClasses: "btn btn--sm",
                        applyButtonClasses: "btn--base",
                        cancelClass: "btn--danger",
                minDate: moment(),
                locale: {
                    format: 'DD MMM, YYYY - hh:mm A',
                    cancelLabel: 'Clear'
                }
            });


            $displaySpan.on('apply.daterangepicker', function(ev, picker) {
                const startDate = picker.startDate.format('DD MMM, YYYY - hh:mm A');
                const endDate = picker.endDate.format('DD MMM, YYYY - hh:mm A');
                $displaySpan.html(`<i class="fa-regular fa-clock"></i> ${startDate}`);
                $hiddenInput.val(picker.startDate.format('YYYY-MM-DD HH:mm'));
            });


            $displaySpan.on('cancel.daterangepicker', function(ev, picker) {
                $displaySpan.html('<i class="fa-regular fa-clock"></i> —');
                $hiddenInput.val('');
            });


            let modalMode = "manual";


            $(document).on("click", ".openTagModal", function () {
                modalMode = "manual";
                $(".prompt_field").addClass("d-none");
                $("#tagModal").modal("show");
            });

            $(document).on("click", ".openAiTagModal", function () {
                modalMode = "ai";
                $(".prompt_field").removeClass("d-none");
                $("#tagModal").modal("show");
            });

            // ---- ADD SELECTED TAGS ----
            $(".add_hashtag").on("click", function () {
                let input = $(".hashtag_input");
                let oldVal = input.val();

                $(".hashtagCheck:checked").each(function () {
                let value = $(this).val();
                oldVal += (oldVal ? " " : "") + value;
                $(this).prop("checked", false);
                });

                input.val(oldVal);
                $(".generated_tags_wrapper").empty();
                $(this).addClass("d-none");
                $(".generated_tags_content").addClass("d-none");
            });

            // ---- FINAL SUBMIT ----
            $(".hashtagSubmit").on("click", function () {
                let modal = $("#tagModal");
                let hashtags = $(".hashtag_input").val();



                $("#tags").val(hashtags);
                $(".hashtag-preview").removeClass("d-none").text(hashtags);
                modal.modal("hide");
            });

            // ---- GENERATE HASHTAGS VIA CONTROLLER ----
            $(".generateHashtag").on("click", function () {
                let prompt = $(".prompt").val();
                if (!prompt) {
                notify("error", "Please enter a topic or prompt.");
                return;
                }

                let action = $(this).data("action");

                $.ajax({
                url: action,
                type: "GET",
                data: { prompt: prompt },
                beforeSend: function () {
                    $(".generateHashtag i").addClass("d-none");
                    $(".loader2").removeClass("d-none");
                },
                success: function (response) {
                    $(".generated_tags_wrapper").html("");
                    response.data.hashTags.forEach(function (tag) {
                    $(".generated_tags_wrapper").append(`
                        <div class="form-check">
                        <input type="checkbox" id="${tag}" class="form-check-input hashtagCheck" value="${tag}">
                        <label for="${tag}" class="form-check-label">${tag}</label>
                        </div>
                    `);
                    });

                    $(".tag_input_warp").removeClass("col-lg-12").addClass("col-lg-6");
                    $(".generated_tags_content").removeClass("d-none");
                    $(".add_hashtag").removeClass("d-none");
                    $(".prompt").val("");

                    $(".generateHashtag i").removeClass("d-none");
                    $(".loader2").addClass("d-none");
                },
                error: function (xhr) {
                    notify("error", xhr.responseJSON.message || "Something went wrong.");
                    $(".generateHashtag i").removeClass("d-none");
                    $(".loader2").addClass("d-none");
                }
                });
            });


            $(document).on("input", ".post-content", function () {
                let val = $(this).val().trim();
                $(".content-preview").html(val || "Your post will be displayed here");
            });

            $(".generate-content").on("click", function () {
                let prompt = $("#prompts").val();
                let language = $("#language").val();
                let tone = $("select[name='tone']").val();
                let creativity = $("select[name='ai_creativity']").val();
                let length = $("input[type='number']").val();
                let action = $(this).data("action");

                // ✅ Validate input
                if (!prompt) {
                notify("error", "Please enter a prompt.");
                return;
                }
                if (!language) {
                notify("error", "Please select an output language.");
                return;
                }
                if (!tone) {
                notify("error", "Please select a tone of voice.");
                return;
                }

                $.ajax({
                url: action,
                type: "GET",
                data: {
                    prompt: prompt,
                    language: language,
                    tone: tone,
                    creativity: creativity,
                    length: length,
                },
                beforeSend: function () {
                    $(".loader3").removeClass("d-none");
                    $(".generate-content").attr("disabled", true);
                },
                success: function (response) {
                    $(".loader3").addClass("d-none");
                    $(".generate-content").attr("disabled", false);

                    if (response.status === "error") {
                    notify("error", response.message);
                    return;
                    }

                    if (response.status === "success") {
                    let generated = response.data.content.trim();
                    let oldContent = $(".post-content").val().trim();


                    let newContent = generated;

                    $(".post-content").val(newContent);
                    $(".content-preview").html(newContent);
                    $("#prompts").val(""); // clear prompt
                    $("#exampleModal").modal("hide");
                    }
                },
                error: function (xhr, status, error) {
                    $(".loader3").addClass("d-none");
                    $(".generate-content").attr("disabled", false);
                    notify("error", xhr.responseJSON?.message || "Something went wrong.");
                },
                });
            });

        })(jQuery);

    </script>
@endpush
