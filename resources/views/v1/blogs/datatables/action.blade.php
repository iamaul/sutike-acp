<div class="btn-group" role="group">
	<a href="#" class="btn btn-flat btn-xs btn-info _edit" data-id="{{ $id }}"
		data-toggle="tooltip" data-placement="top" title="Edit"
		{{ auth()->user()->canUpdateBlogs() ? '' : 'disabled' }}
	>
		<i class="fa fa-edit"></i>
	</a>
	<a href="#" class="btn btn-flat btn-xs btn-danger _destroy" data-id="{{ $id }}"
		data-toggle="tooltip" data-placement="top" title="Delete"
		{{ auth()->user()->canDestroyBlogs() ? '' : 'disabled' }}
	>
		<i class="fa fa-trash"></i>
	</a>
</div>