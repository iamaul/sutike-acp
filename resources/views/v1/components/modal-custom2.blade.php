<div id="{{ isset($target) ? $target . 'Modal' : 'Modal' }}" class="form-horizontal" style="display:none">

  <div class="modal fade {{ $class ?? '' }}" id="{{ $target ?? '' }}" {{ $attributes ?? '' }}>
    <div class="modal-dialog {{ $type ?? '' }}">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close" id=closeBtnHdr>
          <span aria-hidden="true"><i class="material-icons">clear</i></span></button>
          <h4 class="modal-title">&nbsp;{!! isset($title) ? strtoupper($title) : '' !!}</h4>
        </div>
        <div class="modal-body">
          {{ $slot }}
        </div>
        @if ($footer)
          <div class="modal-footer">
            <button type="button" id="closeBtn" class="btn btn-md btn-flat btn-danger" data-dismiss="modal">{{ __('global.buttons.cancel') }}</button>
          </div>
        @endif
      </div>
    </div>
  </div>
</div>