@extends('layouts.admin')
@section('content')
    @can('service_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.services.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.service.title_singular') }}
                </a>
            </div>
        </div>
    @endcan
    <div class="card">
        <div class="card-header">
            <h5>{{ trans('cruds.service.title_singular') }} {{ trans('global.list') }}</h5>
        </div>

        <div class="card-body">
            <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-Service">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.service.fields.order') }}
                        </th>
                        <th>
                            {{ trans('cruds.gallery.fields.images') }}
                        </th>
                        <th>
                            {{ trans('cruds.service.fields.name') }}
                        </th>
                        <th>
                            {{ trans('cruds.service.fields.expiry') }}
                        </th>
                        <th>
                            {{ trans('cruds.service.fields.service_type') }}
                        </th>
                        <th>
                            {{ trans('cruds.service.fields.status') }}
                        </th>
                        <th>
                            {{ trans('global.commission') }}
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>



@endsection
@section('scripts')
    @parent
    <script>
        $(function() {
            let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
            // @can('service_delete')
            //     let deleteButtonTrans = '{{ trans('global.datatables.delete') }}';
            //     let deleteButton = {
            //     text: deleteButtonTrans,
            //     url: "{{ route('admin.services.massDestroy') }}",
            //     className: 'btn-danger',
            //     action: function (e, dt, node, config) {
            //     var ids = $.map(dt.rows({ selected: true }).data(), function (entry) {
            //     return entry.id
            //     });

            //     if (ids.length === 0) {
            //     alert('{{ trans('global.datatables.zero_selected') }}')

            //     return
            //     }

            //     if (confirm('{{ trans('global.areYouSure') }}')) {
            //     $.ajax({
            //     headers: {'x-csrf-token': _token},
            //     method: 'POST',
            //     url: config.url,
            //     data: { ids: ids, _method: 'DELETE' }})
            //     .done(function () { location.reload() })
            //     }
            //     }
            //     }
            //     dtButtons.push(deleteButton)
            // @endcan

            let dtOverrideGlobals = {
                buttons:[dtButtons],
                processing: true,
                serverSide: true,
                retrieve: true,
                searching:true,
                aaSorting: [],
                ajax: "{!! route('admin.services.index', request()->all()) !!}",
                columns: [{
                        data: 'placeholder',
                        name: 'placeholder'
                    },
                    {
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'logo',
                        name: 'logo',
                        searchable:false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'expiry',
                        name: 'expiry'
                    },
                    {
                        data: 'service_type_name',
                        name: 'service_type.name'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'sales_commission',
                        name: 'sales_commission'
                    },
                    {
                        data: 'actions',
                        name: '{{ trans('global.actions') }}'
                    }
                ],
                orderCellsTop: true,
                order: [
                    [1, 'desc']
                ],
                pageLength: 50,
            };
            let table = $('.datatable-Service').DataTable(dtOverrideGlobals);
            $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });

        });
    </script>
@endsection
