@extends('admin.layouts.app')
@section('panel')
    <div class="row gy-4 justify-content-start mb-none-30">
        <div class="col-xxl-3 col-xl-3 col-lg-4 col-md-4">
            @include('admin.components.navigate_sidebar')
        </div>

        <div class="col-xxl-9 col-xl-9 col-lg-8 col-md-8 mb-30">
            <div class="row gy-4">
                <div class="col-md-12 mb-30">
                    <div class="card b-radius--10 ">
                        <div class="card-body p-0">
                            <div class="table-responsive--sm table-responsive">
                                <table class="table table--light style--two custom-data-table">
                                    <thead>
                                        <tr>
                                            <th>@lang('Title')</th>
                                            <th>@lang('Client ID')</th>
                                            <th>@lang('Status')</th>
                                            <th>@lang('Action')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($credentials as $key => $credential)
                                            <tr>
                                                <td class="fw-bold">{{ ucfirst($key) }}</td>
                                                <td>{{ $credential->client_id }}</td>
                                                <td>
                                                    @if ($credential->status == Status::ENABLE)
                                                        <span class="badge badge--success">@lang('Enabled')</span>
                                                    @else
                                                        <span class="badge badge--warning">@lang('Disabled')</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="d-flex justify-content-end align-items-center gap-2">
                                                        <div class="form-group mb-0">
                                                            <label class="switch m-0" title="@lang('Change Status')">
                                                                <input type="checkbox" class="toggle-switch confirmationBtn" data-action="{{ route('admin.setting.social_app.credentials.status.update', $key) }}"
                                                                data-question="@lang('Are you sure to change credential status from this system?')" @checked($credential->status)>
                                                                <span class="slider round"></span>
                                                            </label>
                                                        </div>

                                                        <button class="btn btn-sm editBtn" data-client_id="{{ $credential->client_id }}" data-client_secret="{{ $credential->client_secret }}" @if($key == 'youtube') data-api_key="{{ $credential->api_key }}" @endif data-key="{{ $key }}" data-action="{{ route('admin.setting.social_app.credentials.update', $key) }}" data-callbackurl="{{ route('user.social.account.connect.callback', $key) }}" title="@lang('Configure')">
                                                            <i class="fa-solid fa-gears"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty

                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- EDIT METHOD MODAL --}}
    <div id="editModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Update Credential'): <span class="credential-name text-end fw-bold"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">

                    </button>
                </div>
                <form method="POST" action="">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>@lang('Client ID')</label>
                            <input type="text" class="form-control" name="client_id">
                        </div>
                        <div class="form-group">
                            <label>@lang('Client Secret')</label>
                            <input type="text" class="form-control" name="client_secret">
                        </div>
                        <div class="form-group youtube_key">
                            <label>@lang('API Key')</label>
                            <input type="text" class="form-control" name="api_key">
                        </div>

                        <div class="form-group">
                            <label>@lang('Callback URL')</label>
                            <div class="input-group">
                                <input type="text" class="form-control callback" readonly>
                                <button type="button" class="input-group-text copyInput" title="@lang('Copy')">
                                    <i class="las la-clipboard"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary" id="editBtn">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>



    <x-confirmation-modal/>

@endsection

@push('script')
    <script>
        (function($) {
            "use strict";

            $(document).on('click', '.editBtn', function() {
                let modal = $('#editModal');
                let data = $(this).data();
                modal.find('form').attr('action', $(this).data('action'));
                modal.find('.credential-name').text(data.key);
                modal.find('[name=client_id]').val(data.client_id);
                modal.find('[name=client_secret]').val(data.client_secret);
                if(data.key == 'youtube'){
                    modal.find('.youtube_key').show();
                    modal.find('[name=api_key]').val(data.api_key);
                }else{
                    modal.find('.youtube_key').hide();
                }
                modal.find('.callback').val($(this).data('callbackurl'));
                modal.modal('show');
            });


            $('.copyInput').on('click', function(e) {
                var copybtn = $(this);
                var input = copybtn.closest('.input-group').find('input');
                if (input && input.select) {
                    input.select();
                    try {
                        document.execCommand('SelectAll')
                        document.execCommand('Copy', false, null);
                        input.blur();
                        notify('success', `Copied: ${copybtn.closest('.input-group').find('input').val()}`);
                    } catch (err) {

                    }
                }
            });

        })(jQuery);
    </script>
@endpush
