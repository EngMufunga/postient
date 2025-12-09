@extends('admin.layouts.app')
@section('panel')
<div class="row mb-none-30">
  <div class="col-md-12">
    <div class="card b-radius--10 ">
      <div class="card-body p-0">
        <div class="table-responsive--md  table-responsive">
          <table class="table table--light style--two">
            <thead>
              <tr>
                <th>@lang('Type')</th>
                <th>@lang('Message')</th>
                <th>@lang('Status')</th>
              </tr>
            </thead>
            <tbody>
              @forelse($reports as $report)
              <tr>
                <td>{{ $report->req_type ?? '' }}</td>
                <td class="text-center white-space-wrap">{{ $report->message }}</td>
                <td><span class="badge badge--{{ $report->status_class }}">{{ $report->status_text ?? '' }}</span></td>
              </tr>
              @empty
              <tr>
                <td colspan="100%" class="text-center">{{ __($emptyMessage) }}</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="bugModal" tabindex="-1" role="dialog" aria-labelledby="bugModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="bugModalLabel">@lang('Report & Request')</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
        </button>
      </div>
      <form action method="post">
        @csrf
        <div class="modal-body">
          <div class="form-group">
            <label>@lang('Type')</label>
            <select class="form-control form-select" name="type" required>
              <option value="bug" @selected(old('type')=='bug' )>@lang('Report Bug')</option>
              <option value="feature" @selected(old('type')=='feature' )>@lang('Feature Request')</option>
            </select>
          </div>
          <div class="form-group">
            <label>@lang('Message')</label>
            <textarea class="form-control" name="message" rows="5" required>{{ old('message') }}</textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn--primary">@lang('Save')</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection


@push('breadcrumb-plugins')
    <button class="btn btn--primary" data-bs-toggle="modal" data-bs-target="#bugModal">
        <i class="fa-solid fa-bug"></i>
        @lang('Report a bug')
    </button>
    <a href="https://wstacks.com/support" target="_blank" class="btn btn--primary">
        <i class="fa-solid fa-headset"></i>
        @lang('Request for Support')
    </a>
@endpush
