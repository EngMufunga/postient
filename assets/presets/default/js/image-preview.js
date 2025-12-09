(function ($) {
  "use strict";

  let fileIdCounter = 0;
  let selectedFiles = [];
  let currentRenderedFiles = new Set();
  let isEditMode = $("#previewContainer .uploader-thumb").length > 0;
  let currentPlatformType = null;

  $(document).ready(function () {
    if (isEditMode) {
      const platform = getCurrentPlatform();
      const platformType = getPlatformType(platform);
      currentPlatformType = platformType;
      renderPlatformPreviews($(".media_preview"), platformType);
    }
  });

  $("#fileInput").on("change", function (e) {
    const input = e.target;
    $(".extra_elements").removeClass("d-none");
    $(".media_preview").removeClass("d-none");

    const newFiles = Array.from(input.files).map((f) => ({
      id: fileIdCounter++,
      file: f,
    }));

    if (isEditMode) {
      selectedFiles = [...selectedFiles, ...newFiles];
    } else {
      selectedFiles = newFiles;
    }

    renderPreviews();
  });

  function getCurrentPlatform() {    
    return $(".account-switch:checked").data("platform-name") || "facebook";
  }

  function getPlatformType(platformName) {
    if (platformName == "Instagram") return "instagram";
    if (platformName == "Linkedin-openid") return "linkedin-openid";
    if (platformName == "Youtube") return "youtube";
    return "facebook";
  }

  window.renderPreviews = function () {
    const previewContainer = $("#previewContainer");
    const mediaPreview = $(".media_preview");

    previewContainer.find(".uploader-thumb[data-file-id]").remove();
    mediaPreview.empty();

    currentRenderedFiles.clear();

    selectedFiles.forEach(({ id, file }) => {
      if (currentRenderedFiles.has(id)) return;

      const reader = new FileReader();
      reader.onload = (ev) => {
        let thumbContent;
        if (file.type.match("video.*")) {
          thumbContent = `
                <div class="video-thumb-wrapper">
                    <video class="video-thumb" src="${ev.target.result}"></video>
                    <div class="play-icon-overlay"><i class="las la-play"></i></div>
                </div>`;
        } else {
          thumbContent = `<img src="${ev.target.result}" alt="${file.name}">`;
        }

        const $thumb = $(`
            <div class="uploader-thumb" data-file-id="${id}">
                ${thumbContent}
                <span class="close-icon new-file"><i class="las la-times"></i></span>
            </div>
            `);

        $thumb.find(".close-icon.new-file").on("click", function () {
          removeNewFile(id);
        });

        previewContainer.append($thumb);
        currentRenderedFiles.add(id);
      };
      reader.readAsDataURL(file);
    });

    const platform = getCurrentPlatform();
    const platformType = getPlatformType(platform);
    currentPlatformType = platformType;
    
    renderPlatformPreviews(mediaPreview, platformType);
  };

  function renderPlatformPreviews(mediaPreview, platformType) {
    
    const hasNewFiles = selectedFiles.length > 0;
    const hasExistingMedia =
      $("#previewContainer .uploader-thumb:not([data-file-id])").length > 0;

    if (!hasNewFiles && !hasExistingMedia) {
      $(".media_preview").empty();
      $(".extra_elements").addClass("d-none");
      $(".media_preview").addClass("d-none");
      return;
    }

    mediaPreview.empty();
    $(".extra_elements").removeClass("d-none");
    $(".media_preview").removeClass("d-none");

    mediaPreview.removeClass("youtube-grid");

    
    if (platformType === "instagram" || platformType === "linkedin-openid") {
      
      if (hasNewFiles && hasExistingMedia) {
        renderCombinedInstagramPreviews(mediaPreview);
      } else if (hasNewFiles) {
        renderNewInstagramPreviews(mediaPreview);
      } else if (hasExistingMedia) {
        renderExistingInstagramPreviews(mediaPreview);
      }
    } else if (platformType === "youtube") {
      mediaPreview.addClass("youtube-grid");
      if (hasNewFiles && hasExistingMedia) {
        renderCombinedYoutubePreviews(mediaPreview);
      } else if (hasNewFiles) {
        renderNewYoutubePreviews(mediaPreview);
      } else if (hasExistingMedia) {
        renderExistingYoutubePreviews(mediaPreview);
      }
    } else {
      renderCombinedFacebookPreviews(mediaPreview);
    }
  }

  function renderCombinedFacebookPreviews(mediaPreview) {
    const existingMedia = $(
      "#previewContainer .uploader-thumb:not([data-file-id])"
    );
    const totalNewFiles = selectedFiles.length;
    const totalExistingMedia = existingMedia.length;
    const totalMedia = totalNewFiles + totalExistingMedia;

    if (totalMedia === 0) return;

    const maxVisible = 4;
    let mediaCount = 0;
    let mediaItems = [];

    if (totalExistingMedia > 0) {
      existingMedia.each(function () {
        const $media = $(this);
        const isVideo = $media.find("video").length > 0;
        const src = isVideo
          ? $media.find("video").attr("src")
          : $media.find("img").attr("src");

        mediaItems.push({
          type: "existing",
          isVideo: isVideo,
          src: src,
        });
      });
    }

    if (totalNewFiles > 0) {
      selectedFiles.forEach(({ id, file }) => {
        mediaItems.push({
          type: "new",
          id: id,
          file: file,
        });
      });
    }

    const mediaToShow = mediaItems.slice(0, Math.min(maxVisible, totalMedia));
    const shouldShowPlusTile = totalMedia > maxVisible;
    const mediaToRender = shouldShowPlusTile
      ? mediaToShow.slice(0, 3)
      : mediaToShow;

    mediaToRender.forEach((mediaItem) => {
      if (mediaItem.type === "existing") {
        let previewHtml;
        if (mediaItem.isVideo) {
          previewHtml = `
          <div class="preview-thumb">
            <div class="video-thumb-wrapper">
              <video class="video-thumb" src="${mediaItem.src}"></video>
              <div class="play-icon-overlay"><i class="las la-play"></i></div>
            </div>
          </div>`;
        } else {
          previewHtml = `
          <div class="preview-thumb">
            <div class="single-thumb"><img src="${mediaItem.src}" alt=""></div>
          </div>`;
        }
        mediaPreview.append(previewHtml);
        mediaCount++;
      } else {
        const reader = new FileReader();
        reader.onload = (ev) => {
          let previewHtml;
          if (mediaItem.file.type.match("video.*")) {
            previewHtml = `
            <div class="preview-thumb" data-file-id="${mediaItem.id}">
              <div class="video-thumb-wrapper">
                <video class="video-thumb" src="${ev.target.result}"></video>
                <div class="play-icon-overlay"><i class="las la-play"></i></div>
              </div>
            </div>`;
          } else {
            previewHtml = `
            <div class="preview-thumb" data-file-id="${mediaItem.id}">
              <div class="single-thumb"><img src="${ev.target.result}" alt=""></div>
            </div>`;
          }
          mediaPreview.append(previewHtml);
          mediaCount++;

          if (mediaCount === mediaToRender.length && shouldShowPlusTile) {
            addPlusTile(mediaPreview, mediaItems[3], totalMedia - maxVisible);
          }
        };
        reader.readAsDataURL(mediaItem.file);
      }
    });

    if (mediaCount === mediaToRender.length && shouldShowPlusTile) {
      addPlusTile(mediaPreview, mediaItems[3], totalMedia - maxVisible);
    }
  }

  function addPlusTile(mediaPreview, fourthMediaItem, hiddenCount) {
    let lastMediaSrc = "";

    if (fourthMediaItem.type === "existing" && !fourthMediaItem.isVideo) {
      lastMediaSrc = fourthMediaItem.src;
      mediaPreview.append(`
      <div class="preview-thumb plus-tile">
        <div class="plus-tile-background">
          <img src="${lastMediaSrc}" alt="">
        </div>
        <div class="plus-overlay">
          <span class="extra-count">+${hiddenCount}</span>
        </div>
      </div>
    `);
    } else if (fourthMediaItem.type === "new") {
      const reader = new FileReader();
      reader.onload = (ev) => {
        if (!fourthMediaItem.file.type.match("video.*")) {
          lastMediaSrc = ev.target.result;
        }
        mediaPreview.append(`
        <div class="preview-thumb plus-tile">
          <div class="plus-tile-background">
            <img src="${lastMediaSrc}" alt="">
          </div>
          <div class="plus-overlay">
            <span class="extra-count">+${hiddenCount}</span>
          </div>
        </div>
      `);
      };
      reader.readAsDataURL(fourthMediaItem.file);
    } else {
      mediaPreview.append(`
      <div class="preview-thumb plus-tile">
        <div class="plus-tile-background">
          <div class="generic-background"></div>
        </div>
        <div class="plus-overlay">
          <span class="extra-count">+${hiddenCount}</span>
        </div>
      </div>
    `);
    }
  }

  function renderCombinedYoutubePreviews(mediaPreview) {
    const existingMedia = $(
      "#previewContainer .uploader-thumb:not([data-file-id])"
    );
    const totalNewFiles = selectedFiles.length;
    const totalExistingMedia = existingMedia.length;
    const totalMedia = totalNewFiles + totalExistingMedia;

    if (totalMedia === 0) return;

    mediaPreview.addClass("youtube-grid");

    existingMedia.each(function () {
      const $media = $(this);
      const isVideo = $media.find("video").length > 0;
      const src = isVideo
        ? $media.find("video").attr("src")
        : $media.find("img").attr("src");

      let previewHtml;
      if (isVideo) {
        previewHtml = `
          <div class="youtube-thumb">
            <div class="video-thumb-wrapper">
              <video class="video-thumb" src="${src}"></video>
              <div class="play-icon-overlay"><i class="las la-play"></i></div>
            </div>
            <div class="youtube-duration">1:30</div>
          </div>`;
      } else {
        previewHtml = `
          <div class="youtube-thumb">
            <img src="${src}" alt="">
          </div>`;
      }
      mediaPreview.append(previewHtml);
    });

    selectedFiles.forEach(({ id, file }) => {
      const reader = new FileReader();
      reader.onload = (ev) => {
        let previewHtml;
        if (file.type.match("video.*")) {
          previewHtml = `
            <div class="youtube-thumb" data-file-id="${id}">
              <div class="video-thumb-wrapper">
                <video class="video-thumb" src="${ev.target.result}"></video>
                <div class="play-icon-overlay"><i class="las la-play"></i></div>
              </div>
              <div class="youtube-duration">1:30</div>
            </div>`;
        } else {
          previewHtml = `
            <div class="youtube-thumb" data-file-id="${id}">
              <img src="${ev.target.result}" alt="">
            </div>`;
        }
        mediaPreview.append(previewHtml);
      };
      reader.readAsDataURL(file);
    });
  }

  function renderCombinedInstagramPreviews(mediaPreview) {
    const existingMedia = $(
      "#previewContainer .uploader-thumb:not([data-file-id])"
    );
    const totalNewFiles = selectedFiles.length;
    const totalExistingMedia = existingMedia.length;
    const totalMedia = totalNewFiles + totalExistingMedia;

    if (totalMedia === 0) return;

    let firstMedia = null;

    if (totalExistingMedia > 0) {
      const firstExistingMedia = existingMedia.first();
      const isVideo = firstExistingMedia.find("video").length > 0;
      const src = isVideo
        ? firstExistingMedia.find("video").attr("src")
        : firstExistingMedia.find("img").attr("src");

      firstMedia = {
        type: "existing",
        isVideo: isVideo,
        src: src,
      };
    } else if (totalNewFiles > 0) {
      firstMedia = {
        type: "new",
        file: selectedFiles[0].file,
        id: selectedFiles[0].id,
      };
    }

    if (firstMedia) {
      if (firstMedia.type === "existing") {
        let previewHtml;
        if (firstMedia.isVideo) {
          previewHtml = `
          <div class="instagram-thumb">
            <div class="video-thumb-wrapper">
              <video class="video-thumb" src="${firstMedia.src}" autoplay muted loop></video>
              <div class="play-icon-overlay"><i class="las la-play"></i></div>
            </div>
            <div class="dots-container"></div>
          </div>`;
        } else {
          previewHtml = `
          <div class="instagram-thumb">
            <img src="${firstMedia.src}" alt="">
            <div class="dots-container"></div>
          </div>`;
        }
        mediaPreview.append(previewHtml);
      } else {
        const reader = new FileReader();
        reader.onload = (ev) => {
          let previewHtml;
          if (firstMedia.file.type.match("video.*")) {
            previewHtml = `
            <div class="instagram-thumb" data-file-id="${firstMedia.id}">
              <div class="video-thumb-wrapper">
                <video class="video-thumb" src="${ev.target.result}" autoplay muted loop></video>
                <div class="play-icon-overlay"><i class="las la-play"></i></div>
              </div>
              <div class="dots-container"></div>
            </div>`;
          } else {
            previewHtml = `
            <div class="instagram-thumb" data-file-id="${firstMedia.id}">
              <img src="${ev.target.result}" alt="">
              <div class="dots-container"></div>
            </div>`;
          }
          mediaPreview.append(previewHtml);

          const dotsContainer = mediaPreview.find(".dots-container");
          dotsContainer.empty();
          for (let i = 0; i < totalMedia; i++) {
            dotsContainer.append('<span class="dot"></span>');
          }
        };
        reader.readAsDataURL(firstMedia.file);
        return;
      }

      const dotsContainer = mediaPreview.find(".dots-container");
      dotsContainer.empty();
      for (let i = 0; i < totalMedia; i++) {
        dotsContainer.append('<span class="dot"></span>');
      }
    }
  }

  $(".account-switch").on("change", function () {
    const platform = getCurrentPlatform();
    const platformType = getPlatformType(platform);

    $(".media_preview").empty();

    if (platformType !== currentPlatformType) {
      const hasNewFiles = selectedFiles.length > 0;
      const hasExistingMedia =
        $("#previewContainer .uploader-thumb").length > 0;

      if (hasNewFiles || hasExistingMedia) {
        currentPlatformType = platformType;

        if (platformType === "facebook") {
          renderCombinedFacebookPreviews($(".media_preview"));
        } else if (platformType === "instagram" || platformType === "linkedin-openid") {
          renderPlatformPreviews($(".media_preview"), "instagram");
        } else if (platformType === "youtube") {
          renderPlatformPreviews($(".media_preview"), "youtube");
        }
      }
    } else {
      const hasNewFiles = selectedFiles.length > 0;
      const hasExistingMedia =
        $("#previewContainer .uploader-thumb").length > 0;

      if (hasNewFiles || hasExistingMedia) {
        if (platformType === "facebook") {
          renderCombinedFacebookPreviews($(".media_preview"));
        } else if (platformType === "instagram") {
          renderPlatformPreviews($(".media_preview"), "instagram");
        } else if (platformType === "youtube") {
          renderPlatformPreviews($(".media_preview"), "youtube");
        }
      }
    }
  });

  function renderNewInstagramPreviews(mediaPreview) {
    const total = selectedFiles.length;
    const { id, file } = selectedFiles[0];
    const reader = new FileReader();

    reader.onload = (ev) => {
      let previewHtml;

      if (file.type.match("video.*")) {
        previewHtml = `
        <div class="instagram-thumb" data-file-id="${id}">
          <div class="video-thumb-wrapper">
            <video class="video-thumb" src="${ev.target.result}" muted></video>
            <div class="play-icon-overlay"><i class="las la-play"></i></div>
          </div>
          <div class="dots-container"></div>
        </div>`;
      } else {
        previewHtml = `
        <div class="instagram-thumb" data-file-id="${id}">
          <img src="${ev.target.result}" alt="">
          <div class="dots-container"></div>
        </div>`;
      }

      mediaPreview.append(previewHtml);

      const dotsContainer = mediaPreview.find(".dots-container");
      dotsContainer.empty();
      for (let i = 0; i < total; i++) {
        dotsContainer.append('<span class="dot"></span>');
      }
    };
    reader.readAsDataURL(file);
  }

  function renderNewYoutubePreviews(mediaPreview) {
    selectedFiles.forEach(({ id, file }) => {
      const reader = new FileReader();
      reader.onload = (ev) => {
        let previewHtml;
        if (file.type.match("video.*")) {
          previewHtml = `
            <div class="youtube-thumb" data-file-id="${id}">
              <div class="video-thumb-wrapper">
                <video class="video-thumb" src="${ev.target.result}"></video>
                <div class="play-icon-overlay"><i class="las la-play"></i></div>
              </div>
              <div class="youtube-duration">1:30</div>
            </div>`;
        } else {
          previewHtml = `
            <div class="youtube-thumb" data-file-id="${id}">
              <img src="${ev.target.result}" alt="">
            </div>`;
        }
        mediaPreview.append(previewHtml);

        const newVideo = mediaPreview.find(".video-thumb").last()[0];
        if (newVideo) {
          newVideo.pause();
          newVideo.currentTime = 0; 
        }
      };
      reader.readAsDataURL(file);
    });
  }

  function removeNewFile(id) {
    const $thumb = $(
      `.uploader-thumb[data-file-id="${id}"], .uploader-thumb:not([data-file-id]) .close-icon[data-id="${id}"]`
    ).closest(".uploader-thumb");

    if ($thumb.length && !$thumb.attr("data-file-id")) {
      const mediaId = $thumb.data("id") || id;

      const $removeField = $(".removed_medias");
      let removedIds = $removeField.val() ? $removeField.val().split(",") : [];

      if (!removedIds.includes(mediaId.toString())) {
        removedIds.push(mediaId.toString());
        $removeField.val(removedIds.join(","));
      }

      $thumb.remove();
    } else {
      selectedFiles = selectedFiles.filter((x) => x.id !== id);
      currentRenderedFiles.delete(id);

      const dt = new DataTransfer();
      selectedFiles.forEach((x) => dt.items.add(x.file));
      $("#fileInput")[0].files = dt.files;

      $(`.uploader-thumb[data-file-id="${id}"]`).remove();
    }

    $(`.preview-thumb[data-file-id="${id}"]`).remove();
    $(`.instagram-thumb[data-file-id="${id}"]`).remove();
    $(`.youtube-thumb[data-file-id="${id}"]`).remove();

    const platform = getCurrentPlatform();
    const hasNewFiles = selectedFiles.length > 0;
    const hasExistingMedia =
      $("#previewContainer .uploader-thumb:not([data-file-id])").length > 0;

    if (hasNewFiles || hasExistingMedia) {
      if (platform == "Instagram") {
        renderPlatformPreviews($(".media_preview"), "instagram");
      } else if (platform == "Youtube") {
        renderPlatformPreviews($(".media_preview"), "youtube");
      } else {
        renderPlatformPreviews($(".media_preview"), "facebook");
      }
    } else {
      $(".media_preview").empty();
      $(".extra_elements").addClass("d-none");
      $(".media_preview").addClass("d-none");
    }
  }

  $(document).on(
    "click",
    ".uploader-thumb:not([data-file-id]) .close-icon",
    function () {
      const mediaId = $(this).data("id");
      removeNewFile(mediaId);
    }
  );

  function renderExistingInstagramPreviews(mediaPreview) {
    const existingMedia = $(
      "#previewContainer .uploader-thumb:not([data-file-id])"
    );
    const total = existingMedia.length;
    if (total === 0) return;

    const firstMedia = existingMedia.first();
    const isVideo = firstMedia.find("video").length > 0;
    const src = isVideo
      ? firstMedia.find("video").attr("src")
      : firstMedia.find("img").attr("src");

    let previewHtml;
    if (isVideo) {
      previewHtml = `
      <div class="instagram-thumb">
          <div class="video-thumb-wrapper">
              <video class="video-thumb" src="${src}" autoplay muted loop></video>
              <div class="play-icon-overlay"><i class="las la-play"></i></div>
          </div>
          <div class="dots-container"></div>
      </div>`;
    } else {
      previewHtml = `
      <div class="instagram-thumb">
          <img src="${src}" alt="">
          <div class="dots-container"></div>
      </div>`;
    }

    mediaPreview.append(previewHtml);

    const dotsContainer = mediaPreview.find(".dots-container");
    dotsContainer.empty();
    for (let i = 0; i < total; i++) {
      dotsContainer.append('<span class="dot"></span>');
    }
  }

  function renderExistingYoutubePreviews(mediaPreview) {
    const existingMedia = $(
      "#previewContainer .uploader-thumb:not([data-file-id])"
    );
    const total = existingMedia.length;

    if (total === 0) return;

    mediaPreview.addClass("youtube-grid");
    mediaPreview.empty();

    existingMedia.each(function () {
      const $media = $(this);
      const isVideo = $media.find("video").length > 0;
      const src = isVideo
        ? $media.find("video").attr("src")
        : $media.find("img").attr("src");

      let previewHtml;
      if (isVideo) {
        previewHtml = `
            <div class="youtube-thumb">
                <div class="video-thumb-wrapper">
                    <video class="video-thumb" src="${src}"></video>
                    <div class="play-icon-overlay"><i class="las la-play"></i></div>
                </div>
                <div class="youtube-duration">1:30</div>
            </div>`;
      } else {
        previewHtml = `
            <div class="youtube-thumb">
                <img src="${src}" alt="">
            </div>`;
      }
      mediaPreview.append(previewHtml);
    });
  }
})(jQuery);
