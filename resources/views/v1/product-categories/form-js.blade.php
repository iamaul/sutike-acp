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
                    message: 'Category name is already in use',
                    type: 'GET'
                }
            }
        },
    };

    @if (auth()->user()->canCreateProductCategories())
    FormProductCategories.callFormValidation(validators)
    .on('success.form.fv', function(e) {
        e.preventDefault();
        FormProductCategories.find('.modal-content').waitMeShow();
        const Text = FormProductCategories.find('.modal-title').text();
        const $form = $(e.target),
            fv    = $form.data('formValidation');
        const Axios = axios.post($form.attr('action'), $form.serialize());
            Axios.then((response) => {
                FormProductCategories.find('.modal-content').waitMeHide();
                FormProductCategories.find('.modal').modal('hide');
                successResponse(response.data);
                Table.ajax.reload();
            });
            Axios.catch((error) => {
                failedResponse(error);
                FormProductCategories.find('.modal-content').waitMeHide();
                FormProductCategories.find('.modal').modal('hide');
            });
    });
    @endif

    FormProductCategories.find('.modal').on('hidden.bs.modal', function() {
        id = '';
    });

    @if (auth()->user()->canUpdateProductCategories())
    $(document).on('click', '._edit', function(e) {
        e.preventDefault();
        FormProductCategories.find('.modal-title').html('EDIT PRODUCT CATEGORIES');
        FormProductCategories.attr('action', `/product-categories/${$(this).data('id')}`);
        FormProductCategories.find('.modal').modal('show');
        const Axios = axios.get(`/product-categories/${$(this).data('id')}/edit`);
        Axios.then((response) => {
            id = $(this).data('id');
            FormProductCategories.find('[name="_method"]').val('PUT');
            FormProductCategories.find('#name').val(response.data.data.name);
           
        });
        Axios.catch((error) => {
            failedResponse(error);
            FormProductCategories.find('.modal').modal('hide');
        });
    });
    @endif
</script>