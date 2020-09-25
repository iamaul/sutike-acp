@extends(__v() . '.layouts.app')

@section('content')
<section class="content">
    <div class="row">
        <div class="col-md-12 can-focus">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ str_title() }}</h3>
                </div>
                <div class="box-body">
                    <div class="col-md-12"> 
                        <table id="roles" class="table table-hover dt-responsive nowrap">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Ability</th>
                                    <th>Description</th>
                                    @foreach ($roles as $v)
                                        <th>{{ ucwords($v->name) }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                        </table>
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
    const Table = $('#roles').callFullDataTable({
        ajax: "{{route('permissions.index')}}",
        buttons: {
            visible: true,
            refresh: true,
            // add: {
            //     addCallback: function() {
            //         @if (auth()->user()->canCreateRoles())
            //             RoleForm.find('.modal').modal('show');
            //             RoleForm.attr('action', '/roles');
            //             RoleForm.find('[name="_method"]').val('POST');
            //             RoleForm.find('#name').val('').focus();
            //             RoleForm.find('#description').val('');
            //         @endif
            //     }
            // },
            // trash: true,
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
                { data: 'id', name: 'id', orderable: true, searchable: false },
                { data: 'display_name', name: 'display_name', orderable: true, searchable: true },
                { data: 'description', name: 'description', orderable: true, searchable: true },
                @foreach ($roles as $i => $v)
                    { data: 'action_{{ $i }}', name: 'action_{{ $i }}', orderable: false, searchable: false },
                @endforeach
            ],
            columnDefs: [{
                    className: 'text-center', 'targets': [ 0, -1, -2 ],
                },{
                    visible: false, targets: []
                }
            ],
            orderDefs: [1, 'asc']
        }, 
        drawCallback: function(){}
    });
    Table.on('draw.dt', () => {
        @if (auth()->user()->canStorePermissions())
            $('.checkbox').checkboxRequest();
        @else
            $('.checkbox').iCheck({
                checkboxClass: 'icheckbox_square-blue',
                radioClass: 'iradio_square-blue',
                increaseArea: '20%' /* optional */
            });
        @endif
    });
    // $('#roles').callDatatables(
    //     [
    //         { data: 'id', name: 'id', orderable: true, searchable: false, width: '3%' },
    //         { data: 'name', name: 'name', orderable: true, searchable: true, width: '20%' },
    //         { data: 'description', name: 'description', orderable: true, searchable: true },
    //         @foreach ($roles as $i => $v)
    //             { data: 'action_{{ $i }}', name: 'action_{{ $i }}', orderable: false, searchable: false },
    //         @endforeach
    //     ],
    //     [
    //         {
    //             responsivePriority: 0,
    //             className: 'text-center', 'targets': [
    //                 @foreach ($roles as $i => $v)
    //                     {{ '-'.++$i.',' }}
    //                 @endforeach
    //                 0
    //             ],
    //         }
    //     ], 1, 'asc'
    // ).on('draw.dt', () => {
    //     @if (auth()->user()->canStorePermissions())
    //         $('.checkbox').checkboxRequest();
    //     @else
    //         $('.checkbox').iCheck({
    //             checkboxClass: 'icheckbox_square-blue',
    //             radioClass: 'iradio_square-blue',
    //             increaseArea: '20%' /* optional */
    //         });
    //     @endif
    // });
</script>
@endsection