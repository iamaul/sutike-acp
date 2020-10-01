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
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            {{-- {{ callout_primary('This callout generated from helpers, please check on <code>app/Service/Support/helpers.php</code>', $dimmis = false, $icon = false) }} --}}
                            <table id="product-categories" class="table table-hover dt-responsive nowrap">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Name</th>
                                        <th>Slug</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
                {{ box_footer() }}
            </div>
        </div>
    </div>
</section>
@include('v1.product-categories.form')
@endsection

@section('js')
<script>
    const FormProductCategories = $('#product-categoriesModal');
    const Table = $('#product-categories').callFullDataTable({
        buttons: {
            visible: true,
            refresh: true,
            add: {
                addCallback: function() {
                    @if (auth()->user()->canCreateProductCategories())
                        FormProductCategories.find('.modal').modal('show');
                        FormProductCategories.find('.modal-title').html('CREATE PRODUCT CATEGORIES');
                        FormProductCategories.attr('action', '/product-categories');
                        FormProductCategories.find('[name="_method"]').val('POST');
                    @endif
                }
            },
            trash: true,
            export: {
                advance: false,
                csv: {
                    url: ''
                },
                pdf: {
                    url: ''
                }
            }
        },
        data: {
            columns: [
                { data: 'id', name: 'id', orderable: true, searchable: false, width: '3%' },
                { data: 'name', name: 'name', orderable: true, searchable: true },
                { data: 'slug', name: 'slug', orderable: true, searchable: true },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            columnDefs: [{
                    className: 'text-center', 'targets': [ 0, -1, 1 ],
                },{
                    visible: false, targets: []
                }
            ],
            orderDefs: [0, 'desc']
        },
        drawCallback: function(){}
    });

    $(document).on('click', '._destroy', function(e) {
        e.preventDefault();
        @if (auth()->user()->canDestroyProductCategories())
        const _this = $(this);
        _this.sweetAlert().then((aggre) => {
            if (aggre) {
                $('.content').find('.box').waitMeShow();
                const Axios = _this.destroy(`product-categories/${_this.data('id')}`);
                Axios.then((response) => {
                    successResponse(response.data);
                    Table.ajax.reload();
                    setTimeout(() => $('.content').find('.box').waitMeHide(), 1000);
                });
                Axios.catch((error) => {
                    $('.content').find('.box').waitMeHide();
                    failedResponse(error);
                });
            } else {
                swal(Label.sweetTextCancel, {
                    icon: 'error',
                });
            }
        });
        @endif
    });
</script>
@include('v1.product-categories.form-js')
@endsection
