<script>

    let id = '';
    const validators = {
        name: {
            validators: {
                notEmpty: {},
                remote: {
                    url: `${window.App.APP_ROUTE}/create`,
                    data: function(validator, $field, value) {
                        return {
                            id: id,
                            name: validator.getFieldElements('name').val(),
                        };
                    },
                    message: 'Tag name is already in use',
                    type: 'GET'
                }
            }
        },
    };

    @if (auth()->user()->canCreateBlogTags())
    FormBlogTags.callFormValidation(validators)
    .on('success.form.fv', function(e) {
        e.preventDefault();
        FormBlogTags.find('.modal-content').waitMeShow();
        const Text = FormBlogTags.find('.modal-title').text();
        const $form = $(e.target),
            fv    = $form.data('formValidation');
        const Axios = axios.post($form.attr('action'), $form.serialize());
            Axios.then((response) => {
                FormBlogTags.find('.modal-content').waitMeHide();
                FormBlogTags.find('.modal').modal('hide');
                successResponse(response.data);
                Table.ajax.reload();
            });
            Axios.catch((error) => {
                failedResponse(error);
                FormBlogTags.find('.modal-content').waitMeHide();
                FormBlogTags.find('.modal').modal('hide');
            });
    });
    @endif

    FormBlogTags.find('.modal').on('hidden.bs.modal', function() {
        id = '';
    });

    @if (auth()->user()->canUpdateBlogTags())
    $(document).on('click', '._edit', function(e) {
        e.preventDefault();
        FormBlogTags.find('.modal-title').html('EDIT BLOG TAGS');
        FormBlogTags.attr('action', `/blog-tags/${$(this).data('id')}`);
        FormBlogTags.find('.modal').modal('show');
        const Axios = axios.get(`/blog-tags/${$(this).data('id')}/edit`);
        Axios.then((response) => {
            id = $(this).data('id');
            FormBlogTags.find('[name="_method"]').val('PUT');
            FormBlogTags.find('#name').val(response.data.data.name);
           
        });
        Axios.catch((error) => {
            failedResponse(error);
            FormBlogTags.find('.modal').modal('hide');
        });
    });
    @endif
</script>