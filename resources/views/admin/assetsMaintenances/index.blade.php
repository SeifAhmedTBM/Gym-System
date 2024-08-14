@extends('layouts.admin')
@section('content')
    @can('assets_maintenance_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-8">
                <a class="btn btn-success" href="{{ route('admin.assets-maintenances.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.assetsMaintenance.title_singular') }}
                </a>
                <button class="btn btn-warning" data-toggle="modal" data-target="#csvImportModal">
                    {{ trans('global.app_csvImport') }}
                </button>
                @include('csvImport.modal', [
                    'model' => 'AssetsMaintenance',
                    'route' => 'admin.assets-maintenances.parseCsvImport',
                ])
            </div>
            <div class="col-lg-2">
                <h2 class="text-center">{{ $assetsMaintenances->count() }}</h2>
                <h2 class="text-center">{{ trans('global.count') }}</h2>
            </div>
            <div class="col-lg-2">
                <h2 class="text-center">{{ $assetsMaintenances->sum('amount') }}</h2>
                <h2 class="text-center">{{ trans('invoices::invoice.total_amount') }}</h2>
            </div>
        </div>
    @endcan
    <div class="card">
        <div class="card-header">
            <h5>{{ trans('cruds.assetsMaintenance.title_singular') }} {{ trans('global.list') }}</h5>
        </div>

        <div class="card-body">
            <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-AssetsMaintenance">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.assetsMaintenance.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.assetsMaintenance.fields.date') }}
                        </th>
                        <th>
                            {{ trans('cruds.assetsMaintenance.fields.amount') }}
                        </th>
                        <th>
                            {{ trans('cruds.assetsMaintenance.fields.comment') }}
                        </th>
                        <th>
                            {{ trans('cruds.account.title_singular') }}
                        </th>
                        <th>
                            {{ trans('cruds.assetsMaintenance.fields.asset') }}
                        </th>
                        <th>
                            {{ trans('cruds.assetsMaintenance.fields.maintence_vendor') }}
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
            @can('assets_maintenance_delete')
                let deleteButtonTrans = '{{ trans('global.datatables.delete') }}';
                let deleteButton = {
                text: deleteButtonTrans,
                url: "{{ route('admin.assets-maintenances.massDestroy') }}",
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
                buttons: [dtButtons],
                processing: true,
                serverSide: true,
                retrieve: true,
                searching: true,
                aaSorting: [],
                ajax: "{{ route('admin.assets-maintenances.index') }}",
                columns: [{
                        data: 'placeholder',
                        name: 'placeholder'
                    },
                    {
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'date',
                        name: 'date'
                    },
                    {
                        data: 'amount',
                        name: 'amount'
                    },
                    {
                        data: 'comment',
                        name: 'comment'
                    },
                    {
                        data: 'account',
                        name: 'account.name'
                    },
                    {
                        data: 'asset_name',
                        name: 'asset.name'
                    },
                    {
                        data: 'maintence_vendor_name',
                        name: 'maintence_vendor.name'
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
            let table = $('.datatable-AssetsMaintenance').DataTable(dtOverrideGlobals);
            $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });

        });
    </script>
@endsection
