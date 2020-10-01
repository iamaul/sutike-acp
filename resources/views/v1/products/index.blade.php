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
                            <table id="products" class="table table-hover dt-responsive nowrap">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Category</th>
                                        <th>Image</th>
                                        <th>Name</th>
                                        <th>Slug</th>
                                        <th>Stock</th>
                                        <th>Status</th>
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
@endsection

@section('js')
<script>
    const FormProducts = $('#Products');
    const Table = $('#products').callFullDataTable({
        buttons: {
            visible: true,
            refresh: true,
            add: {
                addCallback: function() {
                    @if (auth()->user()->canCreateProducts())
                        window.open('{{ url('products/create') }}', '_self');
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
                { data: 'category_name', name: 'category_name', orderable: true, searchable: true },
                { data: 'product_image', name: 'product_image', orderable: true, searchable: true },
                { data: 'name', name: 'name', orderable: true, searchable: true },
                { data: 'slug', name: 'slug', orderable: true, searchable: true },
                { data: 'stock', name: 'stock', orderable: true, searchable: true },
                { data: 'status', name: 'status', orderable: true, searchable: true },
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

    $(document).on('click', '._edit', function(e) {
        e.preventDefault();
        @if (auth()->user()->canUpdateProducts())
            const _this = $(this);
            var href = '{{ url("products") }}' + '/' + `${_this.data('id')}`+'/edit';
            window.open(href,'_self');
        @endif
    });

    $(document).on('click', '._destroy', function(e) {
        e.preventDefault();
        @if (auth()->user()->canDestroyProducts())
        const _this = $(this);
        _this.sweetAlert().then((aggre) => {
            if (aggre) {
                $('.content').find('.box').waitMeShow();
                const Axios = _this.destroy(`products/${_this.data('id')}`);
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
@endsection
