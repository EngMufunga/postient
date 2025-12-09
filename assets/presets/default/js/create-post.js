(function ($) {
  "use strict";

  function checkedAccount() {
    let youtube = $('.account-checkbox[data-platform-name="Youtube"]');
    let others = $(".account-checkbox").not('[data-platform-name="Youtube"]');

    let youtubeChecked = youtube.is(":checked");
    let otherChecked = others.is(":checked");
    if (youtubeChecked) {
      others.prop("disabled", true);
      youtube.prop("disabled", false);

      $(".action_btn").each(function () {
        $(this).prop("disabled", false);
      });
      changeFormField();
    } else if (otherChecked) {
      youtube.prop("disabled", true);
      others.prop("disabled", false);

      $(".action_btn").each(function () {
        $(this).prop("disabled", false);
      });
    } else {
      $(".account-checkbox").prop("disabled", false);
      resetForm();
    }

    $(".account-switch").prop("checked", false);

    let firstCheckedFound = false;

    $(".account-checkbox").each(function () {
      let $singlePost = $(this).closest(".single-post");
      let isChecked = $(this).is(":checked");

      $singlePost.find(".account-switch").prop("disabled", !isChecked);

      if (isChecked && !firstCheckedFound) {
        let $radio = $singlePost.find('input[type="radio"]').first();
        let account = $radio.data("account");

        $radio.prop("checked", true);
        firstCheckedFound = true;

        let platformName = $radio.data("platform-name");

        if (platformName) {
          $(".platform-preview").addClass("d-none");
          let previewBox = $(
            `.platform-preview[data-preview="${platformName.toLowerCase()}"]`
          );
          previewBox.find(".profile").attr("src", `${account.profile_image}`);
          previewBox.find(".user_name").html(`${account.profile_name}`);
          previewBox.removeClass("d-none");
        }
      }
    });
  }

  function mediaType() {
    let mediaType = $(".media_type").val();
    if (mediaType == "video") {
      $("#fileInput").attr("accept", "video/*");
      $("#fileInput").attr("multiple", false);
    } else {
      $("#fileInput").attr("accept", "image/*");
      $("#fileInput").attr("multiple", true);
    }
  }

  $(".media_type").on("change", function () {
    mediaType();
  });

  function initializePreviewForEdit() {
    if ($(".account-checkbox:checked").length > 0) {
      let $firstChecked = $(".account-checkbox:checked").first();
      let $radio = $firstChecked
        .closest(".single-post")
        .find(".account-switch");
      let platformName = $radio.data("platform-name");
      let account = $radio.data("account");

      if (platformName && account) {
        $(".platform-preview").addClass("d-none");
        let previewBox = $(
          `.platform-preview[data-preview="${platformName.toLowerCase()}"]`
        );
        previewBox.find(".profile").attr("src", account.profile_image);
        previewBox.find(".user_name").html(account.profile_name);
        previewBox.removeClass("d-none");
      }

      let postContent = $(".post-content").val();
      if (postContent) {
        $(".content-preview").removeClass("d-none");
        $(".extra_elements").removeClass("d-none");
        $(".content-preview").html(postContent);
      }

      let postTitle = $(".post-title").val();
      if (postTitle) {
        $(".content-preview").removeClass("d-none");
        $(".extra_elements").removeClass("d-none");
        $(".title-preview").html(postTitle);
      }
      let hashtags = $(".hashtag").val();
      if (hashtags) {
        $(".post-hashtag").removeClass("d-none");
        $(".post-hashtag").html(hashtags);
        $(".hashtag-preview").removeClass("d-none");
        $(".hashtag-preview").html(hashtags);
      }
    }
  }

  function resetForm() {
    $("#post_form")[0].reset();
    const previewContainer = $("#previewContainer");
    previewContainer.empty();

    $(".post-hashtag").addClass("d-none");
    $(".post-hashtag").html("");

    $(".hashtag_input").val("");

    $(".who_can_see").removeClass("d-none");
    $(".who_can_see_two").addClass("d-none");

    $(".action_btn").each(function () {
      $(this).prop("disabled", true);
    });

    $(".post-title").addClass("d-none");
    $(".post-title").attr("required", false);

    $("#fileInput").attr("accept", "image/*");

    $(".media_type_warper").removeClass("d-none");
    resetPreviewBox();
  }

  checkedAccount();
  initializePreviewForEdit();
  mediaType();

  $(".account-checkbox").on("change", function () {
    checkedAccount();
  });

  function changeFormField() {
    $("#fileInput").attr("accept", "video/*");
    $("#fileInput").attr("multiple", false);

    $(".who_can_see").addClass("d-none");
    $(".who_can_see_two").removeClass("d-none");

    $(".post-title").removeClass("d-none");
    $(".post-title").attr("required", "required");

    $(".media_type_warper").addClass("d-none");
  }

  $(document).ready(function () {
    $(".select2-auto-tokenize").select2({
      dropdownParent: $(".select2-parent"),
      tags: true,
      tokenSeparators: [","],
    });
  });




  
  $(".hashtag_modal").on("click", function () {
    let modal = $("#hashtagModal");
    $(".generated_tags_wrapper").html("");
    $(".add_hashtag").addClass("d-none");

    $(".tag_input_warp").addClass("col-lg-12");
    $(".tag_input_warp").removeClass("col-lg-6");
    $(".generated_tags_content").addClass("d-none");

    modal.modal("show");
  });

  $(".add_hashtag").on("click", function () {
    let input = $(".hashtag_input");
    let oldVal = input.val();

    $(".hashtagCheck:checked").each(function () {
      let value = $(this).val();
      oldVal += (oldVal ? " " : "") + value;

      $(this).addClass("d-none");
    });

    input.val(oldVal);
    $(".generated_tags_wrapper").empty();
    $(this).addClass("d-none");

    $(".tag_input_warp").addClass("col-lg-12");
    $(".tag_input_warp").removeClass("col-lg-6");
    $(".generated_tags_content").addClass("d-none");
  });

  $(".hashtagSubmit").on("click", function () {
    let modal = $("#hashtagModal");
    let hashtags = $(".hashtag_input").val();
    $("[name=tags]").val(hashtags);
    $(".post-hashtag").removeClass("d-none");
    $(".post-hashtag").html("");
    $(".post-hashtag").html(hashtags);

    $(".hashtag-preview").removeClass("d-none");
    $(".hashtag-preview").html("");
    $(".hashtag-preview").html(hashtags);
    modal.modal("hide");
  });

  $(".generateHashtag").on("click", function () {
    let prompt = $(".prompt").val();
    if (!prompt) {
      notify("error", "Please enter a prompt.");
      return;
    }
    let action = $(this).data("action");
    $.ajax({
      url: action,
      type: "get",
      data: {
        prompt: prompt,
      },
      beforeSend: function () {
        $(".generateHashtag").find("i").addClass("d-none");
        $(".loader2").removeClass("d-none");
      },
      success: function (response) {
        $(".generated_tags_wrapper").html("");
        response.data.hashTags.forEach(function (value, index) {
          let html = `
                    <div class="form-check">
                        <input type="checkbox" id="${value}" class="form-check-input hashtagCheck" value="${value}">
                        <label for="${value}" class="form-check-label">${value}</label>
                    </div>`;

          $(".generated_tags_wrapper").append(html);
        });

        $(".tag_input_warp").removeClass("col-lg-12");
        $(".tag_input_warp").addClass("col-lg-6");
        $(".generated_tags_content").removeClass("d-none");
        $(".generateHashtag").find("i").removeClass("d-none");
        $(".loader2").addClass("d-none");
        $(".add_hashtag").removeClass("d-none");
        $(".prompt").val("");
      },
      error: function (xhr, status, error) {
        notify("error", xhr.responseJSON.message);
        $(".generateHashtag").find("i").removeClass("d-none");
        $(".loader2").addClass("d-none");
      },
    });
  });

  new EmojiPicker({
    trigger: [
      {
        selector: ".emoji__picker",
        insertInto: [".emoji__area"],
      },
      {
        selector: ".second-btn",
        insertInto: ".two",
      },
    ],
    closeButton: true,
  });

  $('input[name="schedule_date"]').daterangepicker({
    autoUpdateInput: false,
    minDate: moment().startOf("day"),
    locale: {
      format: "YYYY-MM-DD",
      cancelLabel: "Clear",
    },
    parentEl: $(".schedule"),
    drops: "up",
    applyButtonClasses: "btn--base btn-sm",
  });

  $('input[name="schedule_date"]').on(
    "apply.daterangepicker",
    function (ev, picker) {
      $(this).val(
        picker.startDate.format("YYYY-MM-DD") +
          " - " +
          picker.endDate.format("YYYY-MM-DD")
      );
    }
  );

  $('input[name="schedule_date"]').on(
    "cancel.daterangepicker",
    function (ev, picker) {
      $(this).val("");
    }
  );

  flatpickr('input[name="schedule_time"]', {
    enableTime: true,
    noCalendar: true,
    dateFormat: "H:i",
    time_24hr: true,
    parentEl: document.querySelector(".schedule"),
    position: "above",
  });

  $(".action_btn").on("click", function (e) {
    e.preventDefault();
    let submitType = $(this).data("submit-type");
    let postTitle = $(".post-title").val();
    let postContent = $(".post-content").val();
    let postMedia = $(".post-media").val();
    let mediaType = $(".media_type").val();

    let $form = $("#post_form");
    let id = $form.data("id") ?? 0;

    let checkedAccounts = $(".account-checkbox:checked");
    let hasYouTube = false;
    let selectedAccounts = [];

    checkedAccounts.each(function () {
      let platformName = $(this).data("platform-name").toLowerCase();
      selectedAccounts.push({
        id: $(this).val(),
        platformName: platformName,
      });

      if (platformName.includes("youtube")) {
        hasYouTube = true;
      }
    });

    if (hasYouTube && !postTitle) {
      $(".post-title").addClass("is-invalid");
      notify("error", "Please enter a title.");
      return;
    }
    if (!hasYouTube && mediaType == "") {
      $(".mediaType").addClass("is-invalid");
      notify("error", "Please select a media type.");
      return;
    }
    if (!postContent) {
      $(".post-content").addClass("is-invalid");
      notify("error", "Please enter a content.");
      return;
    }
    if (!postMedia && id == 0) {
      notify("error", "Please upload media.");
      return;
    }

    if (submitType == "schedule") {
      let scheduleDate = $(".schedule-date").val();
      let scheduleTime = $(".schedule-time").val();
      let isScheduleChecked = $(".schedule-type:checked").length <= 0;

      if (isScheduleChecked) {
        $(".schedule-type").addClass("is-invalid");
        notify("error", "Please select a schedule type.");
        return;
      }

      if (!scheduleDate) {
        $(".schedule-date").addClass("is-invalid");
        notify("error", "Please give a schedule date.");
        return;
      }
      if (!scheduleTime) {
        $(".schedule-time").addClass("is-invalid");
        notify("error", "Please give a schedule time.");
        return;
      }
    }

    let platformType = hasYouTube ? "video" : "social";
    let formData = new FormData($form[0]);
    formData.append("platform_type", platformType);
    formData.append("submit_type", submitType);

    let action = $form.data("action");
    let $clickedBtn = $(this);

    $.ajax({
      url: action,
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
      },
      beforeSend: function () {
        // $('.action_btn').prop('disabled', true);
      },
      success: function (response) {
        if (response.status == "error") {
          notify("error", response.message);
          return;
        }

        if (response.status == "success") {
          notify("success", response.message);
          setTimeout(() => {
            // window.location.reload();
          }, 1500);
        }
      },
      error: function (xhr, status, error) {
        console.error("Error:", error);
      },
    });
  });

  $(".account-switch").on("change", function () {
    let platformName = $(this).data("platform-name");
    let account = $(this).data("account");

    $(".platform-preview").addClass("d-none");
    let previewBox = $(
      `.platform-preview[data-preview="${platformName.toLowerCase()}"]`
    );

    previewBox.removeClass("d-none");

    previewBox.find(".profile").attr("src", `${account.profile_image}`);
    previewBox.find(".user_name").html(`${account.profile_name}`);
  });

  $(".post-content").on("input", function () {
    $(".content-preview").removeClass("d-none");
    $(".extra_elements").removeClass("d-none");
    let content = $(this).val();
    $(".content-preview").html(content);
  });

  $(".post-title").on("input", function () {
    $(".content-preview").removeClass("d-none");
    $(".extra_elements").removeClass("d-none");
    let title = $(this).val();
    $(".title-preview").html(title);
  });

  function resetPreviewBox() {
    $(".platform-preview").addClass("d-none");
    $('.platform-preview[data-preview="default"]').removeClass("d-none");

    $(".content-preview").addClass("d-none");
    $(".content-preview").html("");
    $(".extra_elements").addClass("d-none");
    $(".hashtag-preview").addClass("d-none");
    $(".hashtag-preview").html("");
    $(".media_preview").addClass("d-none");
    $(".media_preview").html("");
    $(".comment-box__content").html("");
  }

  $(".comment-btn").on("click", function () {
    let account = $(".account-switch:checked").data("account");
    let comment = $(this).closest(".comment-form").find(".comment_text").val();

    if (account.platform_id === 3) {
      comment = $(this)
        .closest(".comment-form")
        .find(".youtube_comment_text")
        .val();
    }

    if (comment == "") {
      notify("error", "Please write a comment");
      return;
    }

    let html = `
      <div class="comment-bow-wrapper">
        <div class="comment-box-item comment-item">
            <div class="comment-box-item__thumb">
                <img class="profile" src="${account.profile_image}" alt="">
            </div>
            <div class="comment-box__right">
                <div class="comment-box-item__content">
                    <p class="comment-box-item__name user_name"> 
                        ${account.profile_name}
                        <span class="time"> Just Now</span>
                    </p>
                    <p class="comment-box-item__text">${comment}</p>
                </div>
                <div class="reaction-btn-wrapper">
                    <span class="reaction-btn">
                        <i class="fa-regular fa-thumbs-up"></i>
                        Like
                    </span>
                    <span class="reaction-btn reply-btn">
                       Reply
                        <i class="las la-reply"></i>
                    </span>
                </div>
            </div>
        </div>

    </div>
    `;

    $(".comment-box__content").html("");
    $(".comment-box__content").append(html);

    $('input[name="first_comment"]').val("");
    $('input[name="first_comment"]').val(comment);

    $(".comment_text").val("");
    if (account.platform_id == 3) {
      $(".youtube_comment_text").val("");
    }
  });

  $(".generate-content").on("click", function () {
    let prompt = $(".content-prompt").val();
    if (!prompt) {
      notify("error", "Please enter a prompt.");
      return;
    }
    let action = $(this).data("action");
    $.ajax({
      url: action,
      type: "get",
      data: {
        prompt: prompt,
      },
      beforeSend: function () {
        $(".loader3").removeClass("d-none");
        $(".generate-content").attr("disabled", true);
      },
      success: function (response) {
        $(".loader3").addClass("d-none");
        $(".generate-content").attr("disabled", false);
        $(".content-prompt").val("");

        if (response.status == "error") {
          notify("error", response.message);
          return;
        }
        if (response.status == "success") {
          let oldContent = $(".post-content").val();
          if (oldContent == "") {
            $(".post-content").val(response.data.content);
          } else {
            $(".post-content").val(oldContent + ". " + response.data.content);
          }
        }
      },
      error: function (xhr, status, error) {
        console.error("Error:", error);
      },
    });
  });

  $(document).on("click", "a", function (e) {
    let url = $(this).attr("href");

    if (url && url !== "#" && !$(this).attr("data-bs-dismiss")) {
      e.preventDefault();
      $("#leavePageModal").modal("show");
      $("#leavePageModal .leave-page-btn")
        .off("click")
        .on("click", function () {
          window.location.href = url;
        });
    }
  });
})(jQuery);
