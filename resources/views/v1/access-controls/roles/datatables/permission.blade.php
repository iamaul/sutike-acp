<div class="btn-group" role="group">
	<a href="{{ ( auth()->user()->canStoreAccess() || auth()->user()->canStorePermissions() ) ? route('permissions.show', ['id' => $id]) : 'javascript:void(0);' }}" class="btn btn-flat btn-xs btn-primary" data-id="{{ $id }}"
		{{ ( auth()->user()->canStoreAccess() || auth()->user()->canStorePermissions() ) ? '' : 'disabled' }}
	>
		<i class="fa fa-list"></i>
	</a>
</div>