<div id="confirmationModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Confirmation Alert!')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <form action="" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="question"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn {{ request()->routeIs('admin.*') ? 'btn--dark' : 'btn-sm btn--secondary' }}" data-bs-dismiss="modal">@lang('No')</button>
                    <button type="submit" class="btn {{ request()->routeIs('admin.*') ? 'btn--primary' : 'btn-sm btn--base' }}">@lang('Yes')</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('script')

<script>
    (function ($) {
        "use strict";
        $(document).on('click','.confirmationBtn', function () {
            var modal   = $('#confirmationModal');
            let data    = $(this).data();
            modal.find('.question').text(`${data.question}`);
            modal.find('form').attr('action', `${data.action}`);
            modal.modal('show');
        });

        $(document).on('change', '.confirmationBtn', function (e) {
            e.preventDefault();

            let checkbox = $(this);
            let isChecked = checkbox.is(':checked');

            // Revert the state until confirmed
            checkbox.prop('checked', !isChecked);

            var modal = $('#confirmationModal');
            let data = checkbox.data();

            modal.find('.question').text(`${data.question}`);
            modal.find('form').attr('action', `${data.action}`);

            // Save the clicked checkbox reference inside modal
            modal.data('checkbox', checkbox);
            modal.data('checked', isChecked);

            modal.modal('show');
        });


        $('#confirmationModal form').on('submit', function (e) {
            let modal = $('#confirmationModal');
            let checkbox = modal.data('checkbox');
            let shouldBeChecked = modal.data('checked');

            checkbox.prop('checked', shouldBeChecked);

            return true;
        });
    })(jQuery);
</script>
@endpush
