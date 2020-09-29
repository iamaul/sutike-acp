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
                    id="blogs-form"
                    action="{{isset($blog) ? route('blogs.update', [$blog['id']]) : route('blogs.store') }}"
                    method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @if(isset($blog))
                        @method('PUT')
                    @endif
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="tag_id" class="control-label">Tag <span class="star" style="color:red">*</span></label>
                                                <select name="tag_id" id="tag_id">
                                                    @if (isset($blog->blogTags))
                                                        <option value="{{ $blog->blogTags->id }}" selected>
                                                            {{ $blog->blogTags->name }}
                                                        </option>
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="header_image" class="control-label">Header Image</label>
                                                <input type="file" name="header_image" id="header_image" />
                                                <input type="text" name="header_image_form" id="header_image_form" class="form-control" readonly/>
                                                <div class="with-bg" style="padding-top: 10px;">
                                                    <img
                                                        id="profile_user_img"
                                                        name="profile_user_img"
                                                        class="profile-user-img"
                                                        src="{{ isset($blog['header_image']) ? Storage::cloud()->url($blog['header_image']) : 
                                                            auth()->user()->userable->avatar ?? 'https://www.gravatar.com/avatar/'.md5(strtolower(auth()->user()->email)).'.jpg?s=200&d=mm' }}"
                                                        alt="header_image"
                                                    >
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="title" class="control-label">Title <span class="star" style="color:red">*</span></label>
                                                <input type="text" class="form-control" id="title" name="title" value="{{ isset($blog['title']) ? $blog['title'] : '' }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group">
                                            <label for="body" class="control-label">Body <span class="star" style="color:red">*</span></label>
                                            <textarea id="body" name="body" class="form-control"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="box-footer">
                        <div class="col-md-4 col-md-offset-8">
                            <a href="{{ url('blogs') }}" class="btn btn-md btn-flat btn-danger pull-right">
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
    const BlogsForm = $('#blogs-form');

    $('#tag_id').select2({
        allowClear: false,
        ajax: {
            url: "{{ route('blog-tags.select2') }}",
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
        placeholder: 'Choose tags',
    });

    $('#header_image_form').hide();
    $('.profile_user_img').hide();

    BlogsForm.on('click', '#header_image_form', function() {
        $('input[name="header_image"]').click();
    });

    BlogsForm.on('change', 'input[name="header_image"]', function() {
        $('#profile_user_img').show();
        var preview = document.querySelector('#profile_user_img');
        var file    = document.querySelector('input[type=file]').files[0];
        var reader  = new FileReader();
        BlogsForm.find('#header_image_form').val(document.querySelector('input[type=file]').files[0].name);
        reader.onloadend = function () { preview.src = reader.result; }

        if (file) {
            reader.readAsDataURL(file);
            console.log(file);
            console.log($('#header_image').val());
        } else {
            preview.src = '';
        }
    });

    @isset($blog['header_image'])
        $('#header_image_form').show();
        $('#profile_user_img').show();
        $('#header_image').addClass('hidden');
    @endisset

    tinymce.init({
        selector: '#body',
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
            'removeformat | help',
        // setup: function (editor) {
        //     editor.on('init', function (e) {
        //         @isset($blog['body'])
        //             editor.setContent('{{ $blog["body"] }}');
        //         @endisset
        //     });
        // }
    });

    let body = document.getElementById('body').value = '{{ $blog["body"] }}';
    let bodyHTML = document.getElementById('body').innerHTML = body;
    tinymce.get('body').setContent(bodyHTML);

    @if(auth()->user()->canUpdateBlogs())
        id = '{{ isset($blog) ? $blog["id"] : "" }}';

        const validators = {
            tag_id: {
                validators: {
                    notEmpty: {}
                }
            },
            header_image: {
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
            // header_image_form: {
            //     validators: {
            //         notEmpty: {}
            //     }
            // },
            title: {
                validators: {
                    notEmpty: {}
                }
            },
            body: {
                validators: {
                    notEmpty: {}
                }
            }
        };

        BlogsForm.callFormValidation(validators).on('success.form.fv', function (e) {
            e.preventDefault();
            const config = { headers: { 'Content-Type': 'multipart/form-data' } };

            const $form = $(e.target),
                fv = $form.data('formValidation');
                let formData = new FormData();

            for (var p of ($form.serialize()).split("&")) {
                var k = p.split("=")
                formData.append(k[0], decodeURI(k[1]));
            }
            formData.append('header_image', document.getElementById('header_image').files[0]);
            formData.append('body', tinymce.get('body').getContent());

            const Axios = axios.post($form.attr('action'), formData, config);
            BlogsForm.waitMeShow();
            Axios.then((response) => {
                BlogsForm.waitMeHide();
                successResponse(response.data);
                setTimeout(() => {
                    window.location.href = '{{ url("blogs") }}';
                }, 1000)
            });
            Axios.catch((error) => {
                BlogsForm.waitMeHide();
                failedResponse(error);
            });
        });
    @else
        $('form').on('submit', e => {
            e.preventDefault();
            // $('#profile_user_img').hide();
        });
    @endif
        
</script>
@endsection
