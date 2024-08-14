@extends('layouts.admin')
@section('content')
    @can('hotdeal_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.hotdeals.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.hotdeal.title_singular') }}
                </a>
                <button class="btn btn-warning" data-toggle="modal" data-target="#csvImportModal">
                    {{ trans('global.app_csvImport') }}
                </button>
                @include('csvImport.modal', [
                    'model' => 'Hotdeal',
                    'route' => 'admin.hotdeals.parseCsvImport',
                ])
            </div>
        </div>
    @endcan
    <div class="card">
        <div class="card-header">
            <h5>{{ trans('cruds.hotdeal.title_singular') }} {{ trans('global.list') }}</h5>
        </div>

        <div class="card-body">
            <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-Hotdeal">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.hotdeal.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.hotdeal.fields.cover') }}
                        </th>
                        <th>
                            {{ trans('cruds.hotdeal.fields.logo') }}
                        </th>
                        <th>
                            {{ trans('cruds.hotdeal.fields.title') }}
                        </th>
                        <th>
                            {{ trans('cruds.hotdeal.fields.promo_code') }}
                        </th>
                        <th>
                            {{ trans('cruds.hotdeal.fields.redeem') }}
                        </th>
                        <th>
                            {{ trans('cruds.hotdeal.fields.type') }}
                        </th>
                        <th>
                            {{ trans('cruds.hotdeal.fields.description') }}
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
            @can('hotdeal_delete')
                let deleteButtonTrans = '{{ trans('global.datatables.delete') }}';
                let deleteButton = {
                text: deleteButtonTrans,
                url: "{{ route('admin.hotdeals.massDestroy') }}",
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
                buttons: [],
                processing: true,
                serverSide: true,
                retrieve: true,
                searching: true,
                aaSorting: [],
                ajax: "{{ route('admin.hotdeals.index') }}",
                columns: [{
                        data: 'placeholder',
                        name: 'placeholder'
                    },
                    {
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'cover',
                        name: 'cover',
                        sortable: false,
                        searchable: false
                    },
                    {
                        data: 'logo',
                        name: 'logo',
                        sortable: false,
                        searchable: false
                    },
                    {
                        data: 'title',
                        name: 'title'
                    },
                    {
                        data: 'promo_code',
                        name: 'promo_code'
                    },
                    {
                        data: 'redeem',
                        name: 'redeem'
                    },
                    {
                        data: 'type',
                        name: 'type'
                    },
                    {
                        data: 'description',
                        name: 'description'
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
            let table = $('.datatable-Hotdeal').DataTable(dtOverrideGlobals);
            $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });

        });
    </script>
@endsection
