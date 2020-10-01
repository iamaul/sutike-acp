@if (auth()->user()->canCreateProductCategories())
@component(__v() . '.components.modal', [
    'action' => route('product-categories.store'),
    'method' => 'POST',
    'target' => 'product-categories',
    'type' => '',
    'title' => 'Product Categories',
    'class' => '',
    'footer' => true
    ])

    <div class="form-group">
        <div class="col-sm-6">
            <input type="text" class="form-control" id="name" placeholder="{{ __('Category Name') }}" 
                name="name" autocomplete="off">
        </div>
    </div>
    
@endcomponent
@endif