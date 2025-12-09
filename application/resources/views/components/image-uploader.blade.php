@props([
    'type' => null,
    'image' => null,
    'imagePath' => null,
    'size' => null,
    'name' => 'image',
    'tigerId' => 'browserTrigger',
    'accept' => '.png, .jpg, .jpeg',
    'required' => true,
    'id' => 'image-input-' . uniqid(),
    'whiteLogoClass' => false,
])
@php
    $size = $size ?? getFileSize($type);
    $preview = $imagePath ?? getImage(getFilePath($type) . '/' . $image, $size);
    $hasImage = !empty($preview);
@endphp

<div class="image-upload--container br--solid-dash radius--base d-flex flex-column justify-content-center align-items-center">
    <div class="thumb--wrap d-flex justify-content-center align-items-center flex-shrink-0 position-relative">
        <div class="no-image-icon d-none">
            <i class="fa-regular fa-image fa-2x text-muted"></i>
        </div>

        <img class="preview-image image--preview {{ $whiteLogoClass ? 'bg__gray' : '' }}" src="{{ $imagePath }}" alt="{{ keyToTitle($name) }} @lang('Image')" />

        <span class="remove-image cross--icon position-absolute d-flex align-items-center justify-content-center cursor-pointer">
            <i class="fa-solid fa-xmark"></i>
        </span>
    </div>
    <div class="content--wrap d-flex flex-column justify-center align-items-center">
        <i class="fa-solid fa-cloud-arrow-up"></i>
        <h6>
            @lang('Drag files here or')
            <span class="text--primary cursor-pointer browse-trigger">@lang('browse')</span>
        </h6>
        <p class="text-center">@lang('Supported Files:') <span class="text-black">{{ $accept }}</span>.
            @lang('Image will be resized into') <span class="text-black">{{ $size }}@lang('px')</span>
        </p>
    </div>
    <input type="file" accept="{{ $accept }}" name="{{ $name }}" id="{{ $id }}" class="file-input" hidden @required($required)/>
</div>

@push('script')
   <script>
        $(function () {
            "use strict";
            $('.image-upload--container').each(function () {
                const $container = $(this);
                const $fileInput = $container.find('.file-input');
                const $previewImage = $container.find('.preview-image');
                const $browseTrigger = $container.find('.browse-trigger');
                const $removeImage = $container.find('.remove-image');
                const $noImageIcon = $container.find('.no-image-icon');

                function openFileInput(input) {
                    input.dispatchEvent(new MouseEvent('click', { bubbles: true }));
                }

                $browseTrigger.off('click').on('click', () => openFileInput($fileInput[0]));

                $fileInput.off('change').on('change', e => {
                    handleFile(e.target.files[0]);
                });

                $container.off('dragover').on('dragover', e => {
                    e.preventDefault();
                    $container.addClass('dragover');
                });

                $container.off('dragleave').on('dragleave', () => {
                    $container.removeClass('dragover');
                });

                $container.off('drop').on('drop', e => {
                    e.preventDefault();
                    $container.removeClass('dragover');
                    const file = e.originalEvent.dataTransfer.files[0];
                    handleFile(file);
                });

                function handleFile(file) {
                    if (!file || !file.type.startsWith('image/')) return;

                    const reader = new FileReader();
                    reader.onload = function (e) {
                        $previewImage.attr('src', e.target.result).show();
                        $removeImage.css('display', 'flex');
                        $noImageIcon.addClass('d-none');
                        $container.addClass('has-image');

                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(file);
                        $fileInput[0].files = dataTransfer.files;
                    };
                    reader.readAsDataURL(file);
                }

                $removeImage.off('click').on('click', () => {
                    $previewImage.attr('src', '').hide();
                    $removeImage.hide();
                    $noImageIcon.removeClass('d-none');
                    $fileInput.val('');
                    $container.removeClass('has-image');
                });
            });
        });
    </script>
@endpush
