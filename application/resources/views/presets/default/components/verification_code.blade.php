<div class="code-box-wrapper d-flex w-100">
    <div class="form-group mb-3 flex-fill">
        <span class="text-white mb-1 d-block">@lang('Verification Code')</span>
        <div class="verification-code-two">
            <input type="text" name="code" id="verification-code" class="form-control overflow-hidden" required autocomplete="off" hidden>
            <div class="boxes-two">
                <input type="text" class="code-input" maxlength="1" placeholder="-">
                <input type="text" class="code-input" maxlength="1" placeholder="-">
                <input type="text" class="code-input" maxlength="1" placeholder="-">
                <input type="text" class="code-input" maxlength="1" placeholder="-">
                <input type="text" class="code-input" maxlength="1" placeholder="-">
                <input type="text" class="code-input" maxlength="1" placeholder="-">
            </div>
        </div>
    </div>
</div>


@push('style-lib')
    <link rel="stylesheet" href="{{asset('assets/common/css/verification_code.css')}}">
@endpush


@push('script')
<script>
    (function($) {
        'use strict';

        let codeInput = $('#verification-code');
        let inputs = $('.code-input');

        inputs.on('input', function() {
            let code = '';
            inputs.each(function() {
                code += $(this).val();
            });
            codeInput.val(code);

            if (code.length === 6) {
                setTimeout(() => {
                    $('.submit-form').find('button[type=submit]').html('<i class="las la-spinner fa-spin"></i>');
                    $('.submit-form').find('button[type=submit]').removeClass('disabled');
                    $('.submit-form').submit();
                }, 200);
            } else {
                $('.submit-form').find('button[type=submit]').addClass('disabled');
            }

            if ($(this).val().length === 1 && $(this).next('.code-input').length) {
                $(this).next('.code-input').focus();
            }
        });

        inputs.on('focus', function() {
            $(this).select();
        });

    })(jQuery);

</script>
@endpush
