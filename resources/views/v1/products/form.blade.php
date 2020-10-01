@extends(__v() . '.layouts.app')

@section('content')

<section class="content">
    <div class="row">
        <div class="col-md-12 can-focus">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ str_title() }}</h3>
                    <div class="box-tools pull-right">
                        {{ box_collapse('collapse') }}
                        {{ box_remove('remove') }}
                    </div>
                </div>
                <form
                    id="products-form"
                    action="{{isset($product) ? route('products.update', [$product['id']]) : route('products.store') }}"
                    method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @if(isset($product))
                        @method('PUT')
                    @endif
                    <input type="hidden" id="category" name="category" value="{{ isset($product->productCategories) ? $product->productCategories->name : '' }}">
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="product_category_id" class="control-label">Category <span class="star" style="color:red">*</span></label>
                                                <select name="product_category_id" id="product_category_id">
                                                    @if (isset($product->productCategories))
                                                        <option value="{{ $product->productCategories->id }}" selected>
                                                            {{ $product->productCategories->name }}
                                                        </option>
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="product_image" class="control-label">Product Image</label>
                                                <input type="file" name="product_image" id="product_image" />
                                                <input type="text" name="product_image_form" id="product_image_form" class="form-control" readonly/>
                                                <div class="with-bg" style="padding-top: 10px;">
                                                    <img
                                                        id="profile_user_img"
                                                        name="profile_user_img"
                                                        class="profile-user-img"
                                                        src="{{ isset($product['product_image']) ? Storage::cloud()->url($product['product_image']) : 
                                                            auth()->user()->userable->avatar ?? 'https://www.gravatar.com/avatar/'.md5(strtolower(auth()->user()->email)).'.jpg?s=200&d=mm' }}"
                                                        alt="product_image"
                                                    >
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="name" class="control-label">Name <span class="star" style="color:red">*</span></label>
                                                <input type="text" class="form-control" id="name" name="name" value="{{ isset($product['name']) ? $product['name'] : '' }}">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="price" class="col-sm-3 control-label">Price <span class="star" style="color:red">*</span></label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control price" id="price" name="price" 
                                                            autocomplete="off" style="padding-right: 12px;"
                                                            value="{{ isset($product['price']) ? $product['price'] : '' }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-9">
                                                <br>
                                                <div class="input-group">
                                                    <input type="checkbox" id="on_sale" name="on_sale" onclick="getDiscountCheckedState();" {{ isset($product['on_sale']) ? 'checked' : '' }}/>
                                                    <label for="on_sale" style="padding-left:10px;">Discount?</label>
                                                </div>
                                            </div>
                                            <div class="col-md-4" id="cut-price">
                                                <div class="form-group">
                                                    <label for="sale_price" class="col-sm-3 control-label">Cut-price <span class="star" style="color:red">*</span></label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control sale_price" id="sale_price" name="sale_price" 
                                                            autocomplete="off" style="padding-right: 12px;"
                                                            value="{{ isset($product['sale_price']) ? $product['sale_price'] : '' }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="stock" class="control-label">Stock <span class="star" style="color:red">*</span></label>
                                                    <input type="number" class="form-control" id="stock" name="stock"
                                                        value="{{ isset($product['stock']) ? $product['stock'] : 0 }}">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="status" class="control-label">Status <span class="star" style="color:red">*</span></label>
                                                    <select class="form-control" name="status" id="status">
                                                        @if(isset($product['status']))
                                                            @if($product['status'])
                                                                <option value="true" selected>
                                                                    Private
                                                                </option>
                                                                <option value="false">
                                                                    Public
                                                                </option>
                                                            @else
                                                                <option value="true">
                                                                    Private
                                                                </option>
                                                                <option value="false" selected>
                                                                    Public
                                                                </option>
                                                            @endif
                                                        @else
                                                            <option value="true">
                                                                Private
                                                            </option>
                                                            <option value="false">
                                                                Public
                                                            </option>
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <label for="description" class="control-label">Description <span class="star" style="color:red">*</span></label>
                                                <textarea id="description" name="description" class="form-control">
                                                    @isset($product['description'])
                                                        {!! $product['description'] !!}
                                                    @endisset
                                                </textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="short_description" class="control-label">Short Description</label>
                                                <textarea id="short_description" name="short_description" class="form-control">
                                                    @isset($product['short_description'])
                                                        {!! $product['short_description'] !!}
                                                    @endisset
                                                </textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="box-footer">
                        <div class="col-md-4 col-md-offset-8">
                            <a href="{{ url('products') }}" class="btn btn-md btn-flat btn-danger pull-right">
                                &nbsp;&nbsp;&nbsp;&nbsp;Cancel&nbsp;&nbsp;&nbsp;&nbsp;</a>
                            <button type="submit" class="btn btn-md btn-flat btn-success pull-right btn-submit">
                                &nbsp;&nbsp;&nbsp;&nbsp;Save&nbsp;&nbsp;&nbsp;&nbsp;</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection

@section('js')

<script src="https://cdn.tiny.cloud/1/{{ env('TINYMCE_API_KEY') }}/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    const ProductsForm = $('#products-form');

    $('#product_category_id').select2({
        allowClear: false,
        ajax: {
            url: "{{ route('product-categories.select2') }}",
            dataType: 'JSON',
            delay: 0,
            data: function (params) {
                return {
                    search: params.term,
                    page: params.page,
                    limit: 20
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;
                return {
                    results: data.items,
                    pagination: {
                        more: (params.page * 20) < data.total_count
                    }
                };
            },
            cache: false
        },
        placeholder: 'Choose categories',
    });

    $('#product_image_form').hide();
    $('.profile_user_img').hide();

    $('#price').formatPrice();
    @isset($product['on_sale']) 
        @if($product['on_sale'])
            $('#cut-price').show();
        @else
            $('#cut-price').hide();
        @endif
    @else
        $('#cut-price').hide();
    @endisset

    ProductsForm.on('click', '#product_image_form', function() {
        $('input[name="product_image"]').click();
    });

    ProductsForm.on('change', 'input[name="product_image"]', function() {
        $('#profile_user_img').show();
        var preview = document.querySelector('#profile_user_img');
        var file    = document.querySelector('input[type=file]').files[0];
        var reader  = new FileReader();
        ProductsForm.find('#product_image_form').val(document.querySelector('input[type=file]').files[0].name);
        reader.onloadend = function () { preview.src = reader.result; }

        if (file) {
            reader.readAsDataURL(file);
            console.log(file);
            console.log($('#product_image').val());
        } else {
            preview.src = '';
        }
    });

    @isset($product['product_image'])
        $('#product_image_form').show();
        $('#profile_user_img').show();
        $('#product_image').addClass('hidden');
    @endisset

    tinymce.init({
        selector: '#description',
        height: 300,
        menubar: true,
        plugins: [
            'advlist autolink lists link image charmap print preview anchor',
            'searchreplace visualblocks code fullscreen',
            'insertdatetime media table paste code help wordcount'
        ],
        toolbar: 'undo redo | formatselect | ' +
            'bold italic backcolor | alignleft aligncenter ' +
            'alignright alignjustify | bullist numlist outdent indent | ' +
            'removeformat | help'
    });
    tinymce.init({
        selector: '#short_description',
        height: 200
    });

    @if(auth()->user()->canUpdateProducts())
        id = '{{ isset($product) ? $product["id"] : "" }}';

        const validators = {
            product_category_id: {
                validators: {
                    notEmpty: {}
                }
            },
            product_image: {
                validators: {
                    file: {
                        extension: 'jpg,jpeg,png',
                        type: 'image/jpeg,image/png',
                        message: 'Only jpg, jpeg, and png are allowed',
                        maxSize: 1024000
                    }
                    // notEmpty: {}
                }
            },
            // product_image_form: {
            //     validators: {
            //         notEmpty: {}
            //     }
            // },
            name: {
                validators: {
                    notEmpty: {}
                }
            },
            status: {
                validators: {
                    notEmpty: {}
                }
            },
            price: {
                validators: {
                    notEmpty: {}
                }
            },
            stock: {
                validators: {
                    notEmpty: {}
                }
            },
            description: {
                validators: {
                    notEmpty: {}
                }
            }
        };

        ProductsForm.callFormValidation(validators).on('success.form.fv', function (e) {
            e.preventDefault();
            const config = { headers: { 'Content-Type': 'multipart/form-data' } };

            const $form = $(e.target),
                fv = $form.data('formValidation');
                let formData = new FormData();

            for (var p of ($form.serialize()).split("&")) {
                var k = p.split("=")
                formData.append(k[0], decodeURI(k[1]));
            }
            formData.append('product_image', document.getElementById('product_image').files[0]);
            formData.append('description', tinymce.get('description').getContent({ format: 'html' }));
            formData.append('short_description', tinymce.get('short_description').getContent({ format: 'html' }));

            const Axios = axios.post($form.attr('action'), formData, config);
            ProductsForm.waitMeShow();
            Axios.then((response) => {
                ProductsForm.waitMeHide();
                successResponse(response.data);
                setTimeout(() => {
                    window.location.href = '{{ url("products") }}';
                }, 1000)
            });
            Axios.catch((error) => {
                ProductsForm.waitMeHide();
                failedResponse(error);
            });
        });
    @else
        $('form').on('submit', e => {
            e.preventDefault();
            // $('#profile_user_img').hide();
        });
    @endif

    function getDiscountCheckedState() {
        let input = document.getElementById("on_sale");
        let cutPrice = document.getElementById("cut-price");
        let isChecked = input.checked;
        if (isChecked) {
            cutPrice.style.display = 'block';
        } else {
            cutPrice.style.display = 'none';
        }
    }
        
</script>
@endsection
