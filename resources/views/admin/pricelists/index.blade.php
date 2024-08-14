@extends('layouts.admin')
@section('content')
    @can('pricelist_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                {{-- <a class="btn btn-success" href="{{ route('admin.pricelists.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.pricelist.title_singular') }}
                </a> --}}
            </div>
        </div>
    @endcan
    <div class="card">
        <div class="card-header">
            <h5>{{ trans('cruds.pricelist.title_singular') }} {{ trans('global.list') }}</h5>
        </div>

        <div class="card-body">
            <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-Pricelist">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.pricelist.fields.order') }}
                        </th>
                        <th>
                            {{ trans('cruds.pricelist.fields.name') }}
                        </th>
                        {{-- <th>
                            {{ trans('cruds.pricelist.fields.service') }}
                        </th> --}}
                        <th>
                            {{ trans('cruds.pricelist.fields.freeze_count') }}
                        </th>
                        <th>
                            {{ trans('cruds.pricelist.fields.session_count') }}
                        </th>
                  
                        {{-- <th>
                            {{ trans('cruds.pricelist.fields.upgrade_from') }} 
                        </th>
                        <th>
                            {{ trans('cruds.pricelist.fields.expiring_date') }} 
                        </th>
                        <th>
                            {{ trans('cruds.pricelist.fields.expiring_session') }} 
                        </th> --}}
                        <th>
                            {{ trans('cruds.pricelist.fields.amount') }}
                        </th>
                        <th>
                            {{ trans('global.full_day') }}
                        </th>
                        <th>
                            Main Service
                        </th>
                        <th>
                            {{ trans('global.all_days') }}
                        </th>
                        <th>
                            {{ trans('cruds.pricelist.fields.status') }}
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
            @can('pricelist_delete')
                let deleteButtonTrans = '{{ trans('global.datatables.delete') }}';
                let deleteButton = {
                text: deleteButtonTrans,
                url: "{{ route('admin.pricelists.massDestroy') }}",
                className: 'btn-danger',
                action: function (e, dt, node, config) {
                var ids = $.map(dt.rows({ selected: true }).data(), function (entry) {
                return entry.id
                });
            
                if (ids.length === 0) {
                alert('{{ trans('global.datatables.zero_selected') }}')
            
                return
                }
            
                if (confirm('{{ trans('global.areYouSure') }}')) {
                $.ajax({
                headers: {'x-csrf-token': _token},
                method: 'POST',
                url: config.url,
                data: { ids: ids, _method: 'DELETE' }})
                .done(function () { location.reload() })
                }
                }
                }
                dtButtons.push(deleteButton)
            @endcan

            let dtOverrideGlobals = {
                buttons:[dtButtons],
                processing: true,
                serverSide: true,
                retrieve: true,
                searching:true,
                aaSorting: [],
                ajax: "{{ route('admin.pricelists.index') }}",
                columns: [{
                        data: 'placeholder',
                        name: 'placeholder'
                    },
                    {
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    // {
                    //     data: 'service_name',
                    //     name: 'service.name'
                    // },
                    {
                        data: 'freeze_count',
                        name: 'freeze_count'
                    },
                    {
                        data: 'session_count',
                        name: 'session_count'
                    },
              
                    // {
                    //     data: 'upgrade_from',
                    //     name: 'upgrade_from'
                    // },
                    // {
                    //     data: 'expiring_date',
                    //     name: 'expiring_date'
                    // },
                    // {
                    //     data: 'expiring_session',
                    //     name: 'expiring_session'
                    // },
                    {
                        data: 'amount',
                        name: 'amount'
                    },
                    {
                        data: 'full_day',
                        name: 'full_day'
                    },
                    {
                        data: 'main_service',
                        name: 'main_service'
                    },
                    {
                        data: 'all_days',
                        name: 'all_days'
                    },
                    {
                        data: 'status',
                        name: 'status'
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
            let table = $('.datatable-Pricelist').DataTable(dtOverrideGlobals);
            $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });

        });
    </script>
@endsection
