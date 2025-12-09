@extends('Template::layouts.master')
@section('content')

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div id="calendar" class="mt-4" data-posts="{{ base64_encode(json_encode($posts)) }}"></div>
            </div>
        </div>
    </div>


    <div class="modal post__prev__modal custom--modal fade" id="postModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Post Preview')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="post_thumb sm--user">
                        <span>
                            <img id="post_thumb_modal"
                                src="{{ getImage(getFilePath('userProfile') . '/' . auth()->user()->image) }}"
                                alt="@lang('Profile Image')">
                            <img id="platformImage"
                                src="{{ getImage(getFilePath('userProfile') . '/' . auth()->user()->image) }}"
                                alt="@lang('Platform Image')">
                        </span>
                        <span id="profile_name">@lang('Profile')</span>
                    </p>
                    <div class="sm--post">
                        <h5 id="postTitle"></h5>
                        <p id="postContent"></p>
                        <ul>
                            <li class="post-schedule">
                                <span><i class="fa-solid fa-calendar"></i> @lang('Schedule Time'): </span><span
                                    id="scheduleTime"></span>
                            </li>
                        </ul>
                    </div>

                    <div class="sm--files">
                        <h6 id="media_image">@lang('Post Media'):</h6>
                        <div id="mediaImages" class="mb-2"></div>
                        <div id="mediaVideos"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('script-lib')
    <script src="{{ asset($activeTemplateTrue . 'js/fullcalendar.min.js') }}"></script>
@endpush

@push('style-lib')
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/fullcalendar.min.css') }}">
@endpush



@push('style')
    <style>
        .post_layout {
            max-width: 400px;
            margin: 60px auto;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 4px;
            position: relative;
        }

        .post_layout .post__item {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 5px;
        }

        .overlay-img {
            position: absolute;
            background: rgba(0, 0, 0, 0.6);
            color: white;
            font-size: 24px;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 100%;
            border-radius: 5px;
        }
    </style>
@endpush

@push('script')
    <script>
        var base_url = "{{ url('/') }}";
        document.addEventListener('DOMContentLoaded', function () {

            'use strict';
            var calendarEl = document.getElementById('calendar');

            const postsEncoded = calendarEl.dataset.posts;
            const posts = JSON.parse(atob(postsEncoded));

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                timeZone: 'local',
                selectable: true,
                displayEventTime: false,
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
                },
                slotMinTime: "00:00:00",
                slotMaxTime: "23:59:59",
                events: posts.map(function (post) {
                    return {
                        id: post.id,
                        title: post.title,
                        start: post.schedule_time,
                        description: post.post_description,
                        platform_image: post.platform_image,
                        media_images: post.media_images,
                        media_videos: post.media_videos,
                        social_profile_image: post.social_profile_image,
                        social_profile_name: post.social_profile_name,
                        edit_route: post.edit_route
                    };
                }),
                eventClick: function (info) {
                    $("#postTitle").text(info.event.title);
                    $("#postContent").text(info.event.extendedProps.description);

                    const eventDate = new Date(info.event.start);
                    const timeZone = "{{ config('app.timezone') }}";

                    const formattedDate = eventDate.toLocaleString('en-US', {
                        timeZone: timeZone,
                        day: '2-digit',
                        month: 'long',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit',
                        hour12: true
                    });

                    const currentDateTime = new Date().toLocaleString('en-US', {
                        timeZone: timeZone,
                        day: '2-digit',
                        month: 'long',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit',
                        hour12: true
                    });

                    $("#scheduleTime").text(formattedDate);
                    $("#currentTime").text(currentDateTime);
                    const platformImage = info.event.extendedProps.platform_image;
                    const platformPath = "{{ getFilePath('platform') }}";
                    $("#platformImage").attr("src", base_url + '/' + platformImage);

                    const mediaImages = info.event.extendedProps.media_images;
                    let mediaImagesHtml = '';


                    let imagesArray = [];

                    if (Array.isArray(mediaImages)) {
                        imagesArray = mediaImages;
                    } else if (mediaImages && typeof mediaImages === 'object') {
                        imagesArray = Object.values(mediaImages);
                    }

                    if (imagesArray.length > 0) {
                        imagesArray.forEach(function (image) {
                            const imageUrl = base_url + '/' + image.file_path + '/' + image.file_name;
                            mediaImagesHtml += `
                                        <img src="${imageUrl}" alt="Media Image">
                                    `;
                        });

                        $("#media_image").removeClass('d-none');
                        $("#mediaImages").removeClass('d-none').html(mediaImagesHtml);

                    } else {

                        $("#mediaImages").addClass('d-none').empty();
                    }




                    const mediaVideos = info.event.extendedProps.media_videos;

                    let mediaVideosHtml = '';

                    if (mediaVideos.length > 0) {
                        if (Array.isArray(mediaVideos) && mediaVideos.length > 0) {
                            mediaVideos.forEach(function (video) {
                                const videoUrl = base_url + '/' + video.file_path + '/' + video.file_name;

                                mediaVideosHtml += `<video controls><source src="${videoUrl}" type="video/mp4"></video>`;
                            });
                        } else {
                            mediaVideosHtml = '<p>No videos available for this post.</p>';
                        }
                        $("#mediaVideos").html(mediaVideosHtml);

                    } else {
                        $("#media_video").addClass('d-none');
                        $("#mediaVideos").addClass('d-none');
                    }


                    if (mediaVideos.length > 0 || imagesArray.length > 0) {
                        $("#media_image").removeClass('d-none');
                    } else {
                        $("#media_image").addClass('d-none');
                    }



                    const socialProfileImage = info.event.extendedProps.social_profile_image;

                    if (socialProfileImage) {
                        $("#post_thumb_modal").attr("src", socialProfileImage);
                    } else {
                        $("#post_thumb_modal").attr("src", '');
                    }

                    $('#profile_name').text(info.event.extendedProps.social_profile_name);

                    $("#postModal").modal("show");
                }
            })

            calendar.render();
        });
    </script>
@endpush