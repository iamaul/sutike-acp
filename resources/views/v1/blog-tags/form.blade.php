@if (auth()->user()->canCreateBlogTags())
@component(__v() . '.components.modal', [
    'action' => route('blog-tags.store'),
    'method' => 'POST',
    'target' => 'blog-tags',
    'type' => '',
    'title' => 'Blog Tags',
    'class' => '',
    'footer' => true
    ])

    <div class="form-group">
        <div class="col-sm-9">
            <input type="text" class="form-control" id="name" placeholder="{{ __('Tag Name') }}" 
                name="name" autocomplete="off">
        </div>
    </div>
    
@endcomponent
@endif